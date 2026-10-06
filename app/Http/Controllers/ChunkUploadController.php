<?php

namespace App\Http\Controllers;

use App\Services\ChunkedUploadService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ChunkUploadController extends Controller
{
    public function __construct(
        protected ChunkedUploadService $chunkService
    ) {
    }

    /**
     * Initialize a chunked upload session.
     * POST /api/upload/chunk/init
     */
    public function init(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'original_name' => 'nullable|string|max:255',
            'total_size' => 'nullable|integer|min:1',
            'file_size' => 'nullable|integer|min:1',
            'chunk_size' => 'nullable|integer|min:1024|max:52428800',
            'total_chunks' => 'nullable|integer|min:1',
            'mime_type' => 'nullable|string|max:100',
            'file_hash' => 'nullable|string|size:64',
            'target_module' => 'nullable|string|max:50',
            'module' => 'nullable|string|max:50',
            'reference_id' => 'nullable|integer',
            'module_record_id' => 'nullable|integer',
            'metadata' => 'nullable|array',
            'document_name' => 'nullable|string|max:255',
            'folder_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid upload initialization parameters.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $input = $request->all();

        // Standardize file name and size aliases
        $fileName = $input['file_name'] ?? $input['name'] ?? $input['original_name'] ?? null;
        if (!$fileName) {
            return response()->json([
                'success' => false,
                'message' => 'The file_name field is required.',
                'errors' => ['file_name' => ['A file name is required.']],
            ], 422);
        }

        $totalSize = $input['total_size'] ?? $input['file_size'] ?? null;
        if (!$totalSize || (int)$totalSize <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'The total_size field is required and must be greater than zero.',
                'errors' => ['total_size' => ['Total file size in bytes is required.']],
            ], 422);
        }

        $input['file_name'] = $fileName;
        $input['original_name'] = $fileName;
        $input['total_size'] = (int) $totalSize;

        try {
            $userId = Auth::id();
            $upload = $this->chunkService->initUpload($input, $userId);

            return response()->json([
                'success' => true,
                'upload_token' => $upload->upload_token,
                'chunk_size' => (int) $upload->chunk_size,
                'total_chunks' => (int) $upload->total_chunks,
                'status' => $upload->status,
                'message' => 'Upload session initialized successfully.',
            ], 201);
        } catch (Throwable $e) {
            Log::error('[ChunkUploadController@init] Initialization error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize chunked upload: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Upload an individual chunk binary.
     * POST /api/upload/chunk/upload
     */
    public function uploadChunk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'upload_token' => 'required|string',
            'chunk_index' => 'required|integer|min:0',
            'chunk_hash' => 'nullable|string|size:64',
            'chunk' => 'nullable|file',
            'file' => 'nullable|file',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid chunk upload parameters.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $chunkFile = $request->file('chunk') ?? $request->file('file');
        if (!$chunkFile || !$chunkFile->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid binary chunk file provided.',
                'errors' => ['chunk' => ['A valid chunk file is required.']],
            ], 422);
        }

        $token = $request->input('upload_token');
        $chunkIndex = (int) $request->input('chunk_index');
        $chunkHash = $request->input('chunk_hash');

        try {
            $part = $this->chunkService->saveChunk($token, $chunkIndex, $chunkFile, $chunkHash);
            $upload = $part->chunkedUpload;

            return response()->json([
                'success' => true,
                'upload_token' => $token,
                'chunk_index' => $part->chunk_index,
                'chunk_size' => (int) $part->chunk_size,
                'chunk_hash' => $part->chunk_hash,
                'uploaded_chunks_count' => (int) $upload->uploaded_chunks,
                'total_chunks' => (int) $upload->total_chunks,
                'is_complete' => ($upload->uploaded_chunks >= $upload->total_chunks),
                'progress_percent' => $upload->getProgressPercent(),
                'status' => $upload->status,
            ], 200);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Upload session not found or has expired.',
            ], 404);
        } catch (Throwable $e) {
            Log::warning("[ChunkUploadController@uploadChunk] Chunk upload failure [token={$token}, idx={$chunkIndex}]: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save chunk: ' . $e->getMessage(),
                'chunk_index' => $chunkIndex,
            ], 400);
        }
    }

    /**
     * Query status and resume checkpoints for an upload session.
     * GET /api/upload/chunk/status
     */
    public function status(Request $request): JsonResponse
    {
        $token = $request->query('token') ?? $request->query('upload_token') ?? $request->input('upload_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'The token or upload_token query parameter is required.',
            ], 422);
        }

        try {
            $statusData = $this->chunkService->getStatus((string) $token);

            return response()->json(array_merge([
                'success' => true,
            ], $statusData), 200);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Upload session not found or has expired.',
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve upload status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assemble uploaded chunks into the destination file.
     * POST /api/upload/chunk/assemble
     */
    public function assemble(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'upload_token' => 'required|string',
            'target_module' => 'nullable|string|max:50',
            'module' => 'nullable|string|max:50',
            'reference_id' => 'nullable|integer',
            'module_record_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid assemble parameters.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = $request->input('upload_token');
        $targetModule = $request->input('target_module') ?? $request->input('module');
        $referenceId = $request->input('reference_id') ?? $request->input('module_record_id');
        if ($referenceId !== null) {
            $referenceId = (int) $referenceId;
        }

        try {
            $assemblyResult = $this->chunkService->assembleFile($token, $targetModule, $referenceId);

            return response()->json($assemblyResult, 200);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Upload session not found.',
            ], 404);
        } catch (Throwable $e) {
            Log::error("[ChunkUploadController@assemble] Assembly failed [token={$token}]: " . $e->getMessage());

            $statusCode = str_contains($e->getMessage(), 'missing') ? 409 : 422;

            return response()->json([
                'success' => false,
                'message' => 'File assembly failed: ' . $e->getMessage(),
            ], $statusCode);
        }
    }

    /**
     * Cancel an upload session and clean up disk resources.
     * POST /api/upload/chunk/cancel
     * DELETE /api/upload/chunk/cancel
     */
    public function cancel(Request $request): JsonResponse
    {
        $token = $request->input('upload_token') ?? $request->query('upload_token') ?? $request->query('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'The upload_token parameter is required to cancel an upload.',
            ], 422);
        }

        try {
            $cancelled = $this->chunkService->cancelUpload((string) $token);

            if (!$cancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload session not found or already purged.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'upload_token' => $token,
                'status' => 'cancelled',
                'message' => 'Upload session cancelled and temporary chunks purged successfully.',
            ], 200);
        } catch (Throwable $e) {
            Log::error("[ChunkUploadController@cancel] Cancel failed [token={$token}]: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel upload session: ' . $e->getMessage(),
            ], 500);
        }
    }
}
