/**
 * GTMS Resilient Chunked Uploader (Enterprise Synology NAS Edition)
 *
 * Provides resilient, resumable, low-memory file uploads for multi-gigabyte (10GB+)
 * drone orthomosaics, point clouds, video footage, and statutory documents.
 *
 * Features:
 * - Chunk slicing (configurable chunk size, default 10MB)
 * - Session initiation via /api/upload/chunk/init
 * - Sequential or parallel chunk streaming (concurrency control)
 * - Automatic retry with exponential backoff on network failures (up to 3 times)
 * - Resume capability: queries /api/upload/chunk/status and skips already uploaded chunks
 * - Stream assembly via /api/upload/chunk/assemble (<64KB server RAM)
 * - Session cancellation and cleanup via /api/upload/chunk/cancel
 * - Pause and Resume methods
 * - Granular event callbacks: onProgress, onSuccess, onError, onChunkSuccess, onPause, onResume
 *
 * @version 1.0.0
 * @author GTMS Architecture Team
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.GtmsChunkUploader = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    class GtmsChunkUploader {
        /**
         * @param {Object} options
         * @param {File|Blob} options.file - HTML5 File or Blob to upload
         * @param {number} [options.chunkSize=10485760] - Chunk size in bytes (default: 10MB)
         * @param {string} [options.targetModule] - Module key (e.g. 'drone', 'dgps', 'lease_applications', 'compliance')
         * @param {number} [options.referenceId] - Record ID in the target module
         * @param {Object} [options.metadata] - Extra metadata (e.g. document_name, folder_id, folder_category)
         * @param {string} [options.uploadToken] - Existing upload token to resume
         * @param {number} [options.concurrency=1] - Concurrent chunk uploads (default 1 for NAS disk affinity)
         * @param {number} [options.maxRetries=3] - Maximum retry attempts per chunk on network failure
         * @param {number} [options.retryDelay=1500] - Base delay in ms between retries
         * @param {string} [options.csrfToken] - CSRF token string (auto-detected if omitted)
         * @param {string} [options.apiBase=''] - Base URL prefix for API endpoints
         * @param {Object} [options.headers={}] - Custom headers to include on every request
         * @param {Function} [options.onProgress] - ({ percent, loaded, total, chunkIndex, totalChunks, speed }) => void
         * @param {Function} [options.onSuccess] - (response) => void
         * @param {Function} [options.onError] - (error) => void
         * @param {Function} [options.onChunkSuccess] - ({ chunkIndex, totalChunks, uploadToken, response }) => void
         * @param {Function} [options.onPause] - () => void
         * @param {Function} [options.onResume] - () => void
         * @param {Function} [options.onCancel] - () => void
         */
        constructor(options = {}) {
            if (!options.file || !(options.file instanceof Blob)) {
                throw new Error('[GtmsChunkUploader] A valid File or Blob instance is required.');
            }

            this.file = options.file;
            this.chunkSize = options.chunkSize && options.chunkSize > 0 ? options.chunkSize : 10 * 1024 * 1024; // 10MB default
            this.targetModule = options.targetModule || options.module || null;
            this.referenceId = options.referenceId || options.moduleRecordId || null;
            this.metadata = options.metadata || {};
            this.uploadToken = options.uploadToken || null;
            this.concurrency = Math.max(1, Math.min(4, options.concurrency || 1));
            this.maxRetries = options.maxRetries !== undefined ? options.maxRetries : 3;
            this.retryDelay = options.retryDelay || 1500;
            this.apiBase = (options.apiBase || '').replace(/\/$/, '');
            this.customHeaders = options.headers || {};

            // Auto-detect CSRF token
            this.csrfToken = options.csrfToken || this._detectCsrfToken();

            // Event Callbacks
            this.onProgress = options.onProgress || function () {};
            this.onSuccess = options.onSuccess || function () {};
            this.onError = options.onError || function () {};
            this.onChunkSuccess = options.onChunkSuccess || function () {};
            this.onPause = options.onPause || function () {};
            this.onResume = options.onResume || function () {};
            this.onCancel = options.onCancel || function () {};

            // Internal State
            this.totalSize = this.file.size;
            this.totalChunks = Math.max(1, Math.ceil(this.totalSize / this.chunkSize));
            this.uploadedChunks = new Set();
            this.chunkByteSizes = new Map();
            this.status = 'idle'; // 'idle' | 'initializing' | 'uploading' | 'paused' | 'assembling' | 'completed' | 'cancelled' | 'failed'
            this.isPaused = false;
            this.isCancelled = false;
            this.activeControllers = new Map(); // chunkIndex -> AbortController
            this.startTime = null;

            // Pre-calculate chunk sizes
            for (let i = 0; i < this.totalChunks; i++) {
                const start = i * this.chunkSize;
                const end = Math.min(start + this.chunkSize, this.totalSize);
                this.chunkByteSizes.set(i, end - start);
            }
        }

        /**
         * Detect CSRF token from document meta tags or form inputs.
         * @private
         */
        _detectCsrfToken() {
            if (typeof document === 'undefined') return '';

            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken && metaToken.getAttribute('content')) {
                return metaToken.getAttribute('content');
            }

            const inputToken = document.querySelector('input[name="_token"]');
            if (inputToken && inputToken.value) {
                return inputToken.value;
            }

            return '';
        }

        /**
         * Build default request headers with CSRF and JSON/Multipart acceptance.
         * @private
         */
        _getHeaders(isJson = false) {
            const headers = Object.assign({}, this.customHeaders);

            if (this.csrfToken) {
                headers['X-CSRF-TOKEN'] = this.csrfToken;
                headers['X-Requested-With'] = 'XMLHttpRequest';
            }

            if (isJson) {
                headers['Content-Type'] = 'application/json';
                headers['Accept'] = 'application/json';
            } else {
                headers['Accept'] = 'application/json';
            }

            return headers;
        }

        /**
         * Start the upload workflow.
         * @returns {Promise<Object>} Resolves with the assembled server response.
         */
        async start() {
            if (this.status === 'uploading' || this.status === 'assembling') {
                console.warn('[GtmsChunkUploader] Upload already in progress.');
                return;
            }

            this.isPaused = false;
            this.isCancelled = false;
            this.startTime = Date.now();

            try {
                // Step 1: Initialize or Resume Session
                if (!this.uploadToken) {
                    this.status = 'initializing';
                    await this._initSession();
                } else {
                    await this.syncStatus();
                }

                // If already completed on server, finalize immediately
                if (this.uploadedChunks.size >= this.totalChunks) {
                    return await this._assemble();
                }

                // Step 2: Upload Chunks
                this.status = 'uploading';
                await this._uploadChunks();

                if (this.isPaused || this.isCancelled) {
                    return;
                }

                // Step 3: Finalize and Assemble on Server
                this.status = 'assembling';
                const finalResult = await this._assemble();
                this.status = 'completed';
                this.onSuccess(finalResult);
                return finalResult;

            } catch (error) {
                if (this.isPaused) {
                    return;
                }
                this.status = 'failed';
                this.onError(error);
                throw error;
            }
        }

        /**
         * Pause an ongoing upload session.
         */
        pause() {
            if (this.status !== 'uploading' && this.status !== 'initializing') {
                return;
            }

            this.isPaused = true;
            this.status = 'paused';

            // Abort in-flight network requests
            for (const [chunkIndex, controller] of this.activeControllers.entries()) {
                controller.abort();
            }
            this.activeControllers.clear();

            this.onPause();
        }

        /**
         * Resume a paused upload session.
         * Queries the server for missing chunks and continues uploading without loss.
         * @returns {Promise<Object>}
         */
        async resume() {
            if (!this.isPaused && this.status !== 'paused') {
                return;
            }

            this.isPaused = false;
            this.status = 'uploading';
            this.onResume();

            try {
                // Sync status with server to verify which chunks landed before pause
                await this.syncStatus();

                // If all chunks already uploaded, jump straight to assemble
                if (this.uploadedChunks.size >= this.totalChunks) {
                    this.status = 'assembling';
                    const finalResult = await this._assemble();
                    this.status = 'completed';
                    this.onSuccess(finalResult);
                    return finalResult;
                }

                await this._uploadChunks();

                if (this.isPaused || this.isCancelled) {
                    return;
                }

                this.status = 'assembling';
                const finalResult = await this._assemble();
                this.status = 'completed';
                this.onSuccess(finalResult);
                return finalResult;

            } catch (error) {
                if (this.isPaused) return;
                this.status = 'failed';
                this.onError(error);
                throw error;
            }
        }

        /**
         * Cancel the upload session and request server-side temporary chunk cleanup.
         * @returns {Promise<void>}
         */
        async cancel() {
            this.isCancelled = true;
            this.status = 'cancelled';

            // Abort active requests
            for (const [chunkIndex, controller] of this.activeControllers.entries()) {
                controller.abort();
            }
            this.activeControllers.clear();

            if (this.uploadToken) {
                try {
                    const url = `${this.apiBase}/api/upload/chunk/cancel`;
                    await fetch(url, {
                        method: 'POST',
                        headers: this._getHeaders(true),
                        body: JSON.stringify({ upload_token: this.uploadToken })
                    });
                } catch (e) {
                    console.warn('[GtmsChunkUploader] Failed to send cancel request to server:', e);
                }
            }

            this.onCancel();
        }

        /**
         * Query upload status and sync uploaded chunks set from the server.
         * @returns {Promise<Object>}
         */
        async syncStatus() {
            if (!this.uploadToken) {
                throw new Error('[GtmsChunkUploader] No uploadToken available to check status.');
            }

            const url = `${this.apiBase}/api/upload/chunk/status?token=${encodeURIComponent(this.uploadToken)}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: this._getHeaders(true)
            });

            if (!response.ok) {
                throw new Error(`[GtmsChunkUploader] Failed to fetch upload status (HTTP ${response.status})`);
            }

            const data = await response.json();
            if (data.uploaded_chunks && Array.isArray(data.uploaded_chunks)) {
                this.uploadedChunks.clear();
                for (const idx of data.uploaded_chunks) {
                    this.uploadedChunks.add(idx);
                }
            }

            this._emitProgress();
            return data;
        }

        /**
         * Initialize upload session on the backend.
         * @private
         */
        async _initSession() {
            const url = `${this.apiBase}/api/upload/chunk/init`;
            const payload = {
                file_name: this.file.name,
                original_name: this.file.name,
                total_size: this.totalSize,
                chunk_size: this.chunkSize,
                total_chunks: this.totalChunks,
                mime_type: this.file.type || 'application/octet-stream',
                target_module: this.targetModule,
                reference_id: this.referenceId,
                metadata: this.metadata
            };

            const response = await fetch(url, {
                method: 'POST',
                headers: this._getHeaders(true),
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                let errorMsg = `HTTP ${response.status}`;
                try {
                    const errJson = await response.json();
                    errorMsg = errJson.message || JSON.stringify(errJson.errors) || errorMsg;
                } catch (_) {}
                throw new Error(`[GtmsChunkUploader] Failed to initialize upload session: ${errorMsg}`);
            }

            const data = await response.json();
            if (!data.upload_token) {
                throw new Error('[GtmsChunkUploader] Server response did not contain an upload_token.');
            }

            this.uploadToken = data.upload_token;
            if (data.chunk_size) {
                this.chunkSize = data.chunk_size;
            }
        }

        /**
         * Upload all pending chunks using a worker queue with controlled concurrency.
         * @private
         */
        async _uploadChunks() {
            // Build list of chunks that need uploading
            const pendingIndices = [];
            for (let i = 0; i < this.totalChunks; i++) {
                if (!this.uploadedChunks.has(i)) {
                    pendingIndices.push(i);
                }
            }

            if (pendingIndices.length === 0) {
                return;
            }

            let nextQueueIndex = 0;
            const workerPool = [];
            const numWorkers = Math.min(this.concurrency, pendingIndices.length);

            const runWorker = async () => {
                while (nextQueueIndex < pendingIndices.length) {
                    if (this.isPaused || this.isCancelled) {
                        return;
                    }

                    const chunkIndex = pendingIndices[nextQueueIndex++];
                    if (this.uploadedChunks.has(chunkIndex)) {
                        continue;
                    }

                    await this._uploadChunkWithRetry(chunkIndex);
                }
            };

            for (let w = 0; w < numWorkers; w++) {
                workerPool.push(runWorker());
            }

            await Promise.all(workerPool);
        }

        /**
         * Upload a single chunk with automatic retry on network errors.
         * @param {number} chunkIndex
         * @private
         */
        async _uploadChunkWithRetry(chunkIndex) {
            let attempt = 0;

            while (attempt <= this.maxRetries) {
                if (this.isPaused || this.isCancelled) {
                    return;
                }

                try {
                    const result = await this._uploadChunk(chunkIndex);
                    this.uploadedChunks.add(chunkIndex);

                    this._emitProgress(chunkIndex);

                    this.onChunkSuccess({
                        chunkIndex,
                        totalChunks: this.totalChunks,
                        uploadToken: this.uploadToken,
                        response: result
                    });
                    return result;

                } catch (err) {
                    if (this.isPaused || this.isCancelled) {
                        return;
                    }

                    attempt++;
                    if (attempt > this.maxRetries) {
                        throw new Error(`[GtmsChunkUploader] Chunk ${chunkIndex} failed after ${this.maxRetries} retries: ${err.message}`);
                    }

                    const delay = this.retryDelay * Math.pow(2, attempt - 1);
                    console.warn(`[GtmsChunkUploader] Chunk ${chunkIndex} failed (attempt ${attempt}/${this.maxRetries}), retrying in ${delay}ms...`, err);
                    await new Promise(resolve => setTimeout(resolve, delay));
                }
            }
        }

        /**
         * Execute binary multipart transfer for one chunk.
         * @param {number} chunkIndex
         * @private
         */
        async _uploadChunk(chunkIndex) {
            const start = chunkIndex * this.chunkSize;
            const end = Math.min(start + this.chunkSize, this.totalSize);

            // Cross-browser slice
            const chunkBlob = (this.file.slice || this.file.webkitSlice || this.file.mozSlice).call(this.file, start, end);

            const formData = new FormData();
            formData.append('upload_token', this.uploadToken);
            formData.append('chunk_index', chunkIndex.toString());
            formData.append('chunk', chunkBlob, `chunk_${chunkIndex}.part`);

            const controller = new AbortController();
            this.activeControllers.set(chunkIndex, controller);

            const url = `${this.apiBase}/api/upload/chunk/upload`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: this._getHeaders(false),
                    body: formData,
                    signal: controller.signal
                });

                if (!response.ok) {
                    let errMsg = `HTTP ${response.status}`;
                    try {
                        const errJson = await response.json();
                        errMsg = errJson.message || errMsg;
                    } catch (_) {}
                    throw new Error(`Server returned ${errMsg}`);
                }

                return await response.json();

            } finally {
                this.activeControllers.delete(chunkIndex);
            }
        }

        /**
         * Finalize all uploaded chunks on server via low-memory binary stream copy (<64KB RAM).
         * @private
         */
        async _assemble() {
            const url = `${this.apiBase}/api/upload/chunk/assemble`;
            const payload = {
                upload_token: this.uploadToken,
                target_module: this.targetModule,
                reference_id: this.referenceId
            };

            const response = await fetch(url, {
                method: 'POST',
                headers: this._getHeaders(true),
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                let errorMsg = `HTTP ${response.status}`;
                try {
                    const errJson = await response.json();
                    errorMsg = errJson.message || errorMsg;
                } catch (_) {}
                throw new Error(`[GtmsChunkUploader] File assembly failed: ${errorMsg}`);
            }

            const data = await response.json();

            // Emit final 100% progress
            this.onProgress({
                percent: 100.0,
                loaded: this.totalSize,
                total: this.totalSize,
                chunkIndex: this.totalChunks - 1,
                totalChunks: this.totalChunks,
                speed: 0
            });

            return data;
        }

        /**
         * Calculate and emit progress notification.
         * @param {number} [lastChunkIndex=0]
         * @private
         */
        _emitProgress(lastChunkIndex = 0) {
            let loadedBytes = 0;
            for (const idx of this.uploadedChunks) {
                loadedBytes += (this.chunkByteSizes.get(idx) || this.chunkSize);
            }
            loadedBytes = Math.min(loadedBytes, this.totalSize);

            const percent = this.totalSize > 0
                ? Math.min(100, Number(((loadedBytes / this.totalSize) * 100).toFixed(2)))
                : 100;

            let speed = 0;
            if (this.startTime && loadedBytes > 0) {
                const elapsedSec = (Date.now() - this.startTime) / 1000;
                if (elapsedSec > 0) {
                    speed = Math.round(loadedBytes / elapsedSec); // bytes per second
                }
            }

            this.onProgress({
                percent,
                loaded: loadedBytes,
                total: this.totalSize,
                chunkIndex: lastChunkIndex,
                totalChunks: this.totalChunks,
                speed
            });
        }
    }

    return GtmsChunkUploader;
}));
