/**
 * GTMS Intelligent "Open With..." File Dispatcher
 *
 * Provides a smart, user-selectable modal when viewing or downloading files:
 * - Identifies file format (.kml, .kmz, .blend, .dwg, .docx, .xlsx, .pptx, .pdf, .zip, etc.)
 * - Offers contextual launch options (Desktop App vs Web Viewer vs Direct Download)
 * - Remembers user preferences with easy reset
 * - Supports native protocol handlers (ms-word:, ms-excel:, ms-powerpoint:)
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
        root.GtmsOpenWith = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    class GtmsOpenWithManager {
        constructor() {
            this.modalEl = null;
            this.modalInstance = null;
            this.currentFile = null;
            this.initListeners();
        }

        /**
         * Initialize global event delegation across all GTMS view buttons
         */
        initListeners() {
            document.addEventListener('DOMContentLoaded', () => {
                this._initModalElement();
            });

            // Global delegation for all document view buttons
            document.addEventListener('click', (e) => {
                const targetBtn = e.target.closest(
                    '.btn-view-item, .btn-doc-view, .btn-view-doc, .doc-btn-view, ' +
                    '.btn-view-drone-item, .btn-view-dgps-item, .btn-view-ppt-item, ' +
                    '.btn-view-comp-item, .btn-view-lab-item, .btn-view-report-item, ' +
                    '.btn-view-receipt, [data-open-with]'
                );

                if (!targetBtn) return;

                const href = targetBtn.getAttribute('href') || targetBtn.getAttribute('data-file-path');
                if (!href || href === '#' || href.startsWith('javascript:')) return;

                // Stop default link opening to trigger our Open With dispatcher
                e.preventDefault();
                e.stopPropagation();

                const fileName = targetBtn.getAttribute('data-file-name') || 
                                 href.split('?')[0].split('#')[0].split('/').pop() || 
                                 'document';

                const row = targetBtn.closest('.checklist-row, tr, .card, li');
                const docName = targetBtn.getAttribute('data-doc-name') || 
                                row?.querySelector('.ci-name, .doc-title, td:first-child')?.textContent?.trim() || 
                                'Attached Document';

                const fileSize = targetBtn.getAttribute('data-file-size') || 
                                 row?.querySelector('.ci-size, .file-size, .badge')?.textContent?.trim() || 
                                 '';

                // Allow Shift+Click or Alt+Click to always force prompt even if preference saved
                const forcePrompt = e.shiftKey || e.altKey;

                this.open({
                    url: href,
                    fileName: decodeURIComponent(fileName),
                    docName: docName,
                    fileSize: fileSize,
                    forcePrompt: forcePrompt
                });
            }, true); // Use capture phase so we cleanly intercept before page-level handlers
        }

        _initModalElement() {
            this.modalEl = document.getElementById('gtmsOpenWithModal');
            if (this.modalEl && typeof bootstrap !== 'undefined') {
                this.modalInstance = bootstrap.Modal.getOrCreateInstance(this.modalEl);

                // Setup Reset preference button
                const resetBtn = document.getElementById('gtmsResetChoicesBtn');
                if (resetBtn) {
                    resetBtn.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        if (this.currentFile?.ext) {
                            localStorage.removeItem(`gtms_pref_${this.currentFile.ext}`);
                            resetBtn.classList.add('d-none');
                            this._showToast(`Preference for .${this.currentFile.ext} reset!`, 'info');
                        }
                    });
                }
            }
        }

        /**
         * Open the modal or dispatch action according to user preference
         */
        open(fileData) {
            this._initModalElement();

            const fullUrl = this._toAbsoluteUrl(fileData.url);
            const ext = this._extractExtension(fileData.fileName || fullUrl);

            this.currentFile = {
                url: fullUrl,
                fileName: fileData.fileName || fullUrl.split('/').pop(),
                docName: fileData.docName || 'Attached Document',
                fileSize: fileData.fileSize || '',
                ext: ext.toLowerCase()
            };

            // Check if user has a saved preference for this extension
            const savedPref = localStorage.getItem(`gtms_pref_${this.currentFile.ext}`);
            if (savedPref && !fileData.forcePrompt) {
                const executed = this._executeAction(savedPref, this.currentFile);
                if (executed) return;
            }

            // Build options for this file format and display modal
            this._populateModal(this.currentFile);
            if (this.modalInstance) {
                this.modalInstance.show();
            } else if (this.modalEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(this.modalEl).show();
            } else {
                // Fallback: direct download/open in new tab if modal markup is not present
                window.open(fullUrl, '_blank');
            }
        }

        _extractExtension(fileNameOrUrl) {
            const clean = fileNameOrUrl.split('?')[0].split('#')[0];
            const parts = clean.split('.');
            return parts.length > 1 ? parts.pop().toLowerCase() : '';
        }

        _toAbsoluteUrl(url) {
            if (/^(?:[a-z]+:)?\/\//i.test(url)) return url;
            return window.location.origin + (url.startsWith('/') ? '' : '/') + url;
        }

        /**
         * Build tailored action options based on file type
         */
        _getFileProfile(ext) {
            const extUpper = ext.toUpperCase();

            // 1. Google Earth & GPS / Navigation Formats
            if (['kml', 'kmz', 'gpx', 'osm'].includes(ext)) {
                return {
                    badge: extUpper,
                    badgeClass: 'bg-primary text-white',
                    icon: 'bi-globe-americas',
                    iconColor: '#2563eb',
                    iconBg: '#eff6ff',
                    options: [
                        {
                            id: 'earth_desktop',
                            recommended: true,
                            title: 'Google Earth Pro (Desktop App)',
                            desc: 'Launch installed Google Earth Pro software on your PC',
                            icon: 'bi-globe2',
                            iconColor: '#1d4ed8',
                            iconBg: '#dbeafe',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'Google Earth Pro')
                        },
                        {
                            id: 'earth_web',
                            title: 'Google Earth Web (Browser)',
                            desc: 'Open interactive 3D satellite view in a new browser tab',
                            icon: 'bi-browser-chrome',
                            iconColor: '#059669',
                            iconBg: '#d1fae5',
                            handler: (file) => window.open(`https://earth.google.com/web/`, '_blank')
                        },
                        {
                            id: 'maps_web',
                            title: 'Google Maps Satellite',
                            desc: 'Open Google Maps in a new browser tab',
                            icon: 'bi-map-fill',
                            iconColor: '#d97706',
                            iconBg: '#fef3c7',
                            handler: (file) => window.open(`https://maps.google.com/`, '_blank')
                        }
                    ]
                };
            }

            // 2. 3D Models, Meshes & Blender (.blend, .obj, .fbx, .stl, .gltf, etc.)
            if (['blend', 'obj', 'fbx', 'stl', 'dae', 'gltf', 'glb', '3ds', 'ply', 'step', 'stp', 'iges', 'igs'].includes(ext)) {
                const isBlend = ext === 'blend';
                return {
                    badge: isBlend ? 'BLENDER 3D' : `${extUpper} 3D MODEL`,
                    badgeClass: 'bg-warning text-dark',
                    icon: 'bi-box',
                    iconColor: '#ea580c',
                    iconBg: '#ffedd5',
                    options: [
                        {
                            id: '3d_desktop',
                            recommended: true,
                            title: isBlend ? 'Open in Blender 3D (Desktop App)' : `Open in 3D Software (${extUpper})`,
                            desc: isBlend ? 'Open 3D scene & quarry models directly in Blender' : 'Launch Blender / 3D Viewer / CAD software on your PC',
                            icon: 'bi-box-seam-fill',
                            iconColor: '#ea580c',
                            iconBg: '#ffedd5',
                            handler: (file) => this._downloadAndPromptLaunch(file, isBlend ? 'Blender 3D' : '3D CAD Application')
                        },
                        {
                            id: 'download_3d',
                            title: `Download .${ext} Project File`,
                            desc: 'Save 3D source asset to your local computer drive',
                            icon: 'bi-download',
                            iconColor: '#2563eb',
                            iconBg: '#eff6ff',
                            handler: (file) => this._triggerDirectDownload(file)
                        }
                    ]
                };
            }

            // 3. AutoCAD & Civil Engineering CAD (.dwg, .dxf, .dgn, .dwf)
            if (['dwg', 'dxf', 'dgn', 'dwf'].includes(ext)) {
                return {
                    badge: `${extUpper} CAD`,
                    badgeClass: 'bg-danger text-white',
                    icon: 'bi-bounding-box',
                    iconColor: '#dc2626',
                    iconBg: '#fee2e2',
                    options: [
                        {
                            id: 'autocad_desktop',
                            recommended: true,
                            title: 'AutoCAD / DWG TrueView (Desktop)',
                            desc: 'Open engineering layout & quarry boundary plan in AutoCAD',
                            icon: 'bi-compass-fill',
                            iconColor: '#dc2626',
                            iconBg: '#fee2e2',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'AutoCAD / DWG TrueView')
                        },
                        {
                            id: 'autodesk_web',
                            title: 'Autodesk Online Web Viewer',
                            desc: 'View 2D/3D CAD design online in Autodesk Cloud Viewer',
                            icon: 'bi-window-stack',
                            iconColor: '#0284c7',
                            iconBg: '#e0f2fe',
                            handler: (file) => window.open('https://viewer.autodesk.com/', '_blank')
                        }
                    ]
                };
            }

            // 4. Microsoft Word & Word Processing (.doc, .docx, .rtf, .odt, .pages)
            if (['doc', 'docx', 'rtf', 'odt', 'pages', 'dot', 'dotx', 'docm'].includes(ext)) {
                return {
                    badge: 'WORD',
                    badgeClass: 'bg-primary text-white',
                    icon: 'bi-file-earmark-word-fill',
                    iconColor: '#1d4ed8',
                    iconBg: '#dbeafe',
                    options: [
                        {
                            id: 'word_desktop',
                            recommended: true,
                            title: 'Microsoft Word (Desktop App)',
                            desc: 'Open document directly in desktop Microsoft Word',
                            icon: 'bi-file-earmark-word',
                            iconColor: '#1d4ed8',
                            iconBg: '#dbeafe',
                            handler: (file) => this._openWithOfficeProtocol('ms-word', file)
                        },
                        {
                            id: 'docs_web',
                            title: 'Google Docs / Office Viewer (Web)',
                            desc: 'Preview document in browser without installing Office',
                            icon: 'bi-eye-fill',
                            iconColor: '#059669',
                            iconBg: '#d1fae5',
                            handler: (file) => window.open(`https://docs.google.com/viewer?url=${encodeURIComponent(file.url)}`, '_blank')
                        }
                    ]
                };
            }

            // 5. Microsoft Excel & Spreadsheets (.xls, .xlsx, .csv, .ods, .numbers, .tsv)
            if (['xls', 'xlsx', 'csv', 'ods', 'numbers', 'tsv', 'xlsm', 'xlsb'].includes(ext)) {
                return {
                    badge: 'EXCEL / DATA',
                    badgeClass: 'bg-success text-white',
                    icon: 'bi-file-earmark-excel-fill',
                    iconColor: '#16a34a',
                    iconBg: '#dcfce7',
                    options: [
                        {
                            id: 'excel_desktop',
                            recommended: true,
                            title: 'Microsoft Excel (Desktop App)',
                            desc: 'Open spreadsheet in desktop Microsoft Excel',
                            icon: 'bi-file-earmark-excel',
                            iconColor: '#16a34a',
                            iconBg: '#dcfce7',
                            handler: (file) => this._openWithOfficeProtocol('ms-excel', file)
                        },
                        {
                            id: 'sheets_web',
                            title: 'Google Sheets / Web Viewer',
                            desc: 'Preview spreadsheet data directly in your browser',
                            icon: 'bi-table',
                            iconColor: '#059669',
                            iconBg: '#d1fae5',
                            handler: (file) => window.open(`https://docs.google.com/viewer?url=${encodeURIComponent(file.url)}`, '_blank')
                        }
                    ]
                };
            }

            // 6. Microsoft PowerPoint & Presentations (.ppt, .pptx, .odp, .key)
            if (['ppt', 'pptx', 'odp', 'key', 'pps', 'ppsx', 'pptm'].includes(ext)) {
                return {
                    badge: 'POWERPOINT',
                    badgeClass: 'bg-warning text-dark',
                    icon: 'bi-file-earmark-ppt-fill',
                    iconColor: '#d97706',
                    iconBg: '#fef3c7',
                    options: [
                        {
                            id: 'ppt_desktop',
                            recommended: true,
                            title: 'Microsoft PowerPoint (Desktop)',
                            desc: 'Open slide deck presentation in PowerPoint',
                            icon: 'bi-file-earmark-ppt',
                            iconColor: '#ea580c',
                            iconBg: '#ffedd5',
                            handler: (file) => this._openWithOfficeProtocol('ms-powerpoint', file)
                        },
                        {
                            id: 'ppt_web',
                            title: 'Web Slide Deck Preview',
                            desc: 'View slides in online document viewer',
                            icon: 'bi-projector-fill',
                            iconColor: '#0284c7',
                            iconBg: '#e0f2fe',
                            handler: (file) => window.open(`https://docs.google.com/viewer?url=${encodeURIComponent(file.url)}`, '_blank')
                        }
                    ]
                };
            }

            // 7. PDF & eBook Documents (.pdf, .epub, .xps)
            if (['pdf', 'epub', 'xps'].includes(ext)) {
                return {
                    badge: extUpper,
                    badgeClass: 'bg-danger text-white',
                    icon: 'bi-file-earmark-pdf-fill',
                    iconColor: '#dc2626',
                    iconBg: '#fee2e2',
                    options: [
                        {
                            id: 'pdf_browser',
                            recommended: true,
                            title: 'View in Browser (New Tab)',
                            desc: 'Open and inspect document in high resolution right inside browser',
                            icon: 'bi-eye-fill',
                            iconColor: '#2563eb',
                            iconBg: '#eff6ff',
                            handler: (file) => window.open(file.url, '_blank')
                        },
                        {
                            id: 'pdf_desktop',
                            title: 'Adobe Acrobat / Dedicated Reader (Desktop)',
                            desc: 'Download and open in your computer\'s default document reader',
                            icon: 'bi-file-earmark-pdf',
                            iconColor: '#dc2626',
                            iconBg: '#fee2e2',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'Document Reader')
                        }
                    ]
                };
            }

            // 8. ZIP & Compressed Archives (.zip, .rar, .7z, .tar, .gz, .bz2, .xz)
            if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz', 'iso'].includes(ext)) {
                return {
                    badge: 'ARCHIVE',
                    badgeClass: 'bg-warning text-dark',
                    icon: 'bi-file-earmark-zip-fill',
                    iconColor: '#d97706',
                    iconBg: '#fef3c7',
                    options: [
                        {
                            id: 'archive_desktop',
                            recommended: true,
                            title: 'WinRAR / 7-Zip / Explorer (Desktop)',
                            desc: 'Download archive and extract files on your computer',
                            icon: 'bi-file-earmark-zip',
                            iconColor: '#d97706',
                            iconBg: '#fef3c7',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'WinRAR / 7-Zip')
                        }
                    ]
                };
            }

            // 9. Images & Graphics (.png, .jpg, .jpeg, .webp, .svg, .gif, .bmp)
            if (['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'bmp'].includes(ext)) {
                return {
                    badge: 'IMAGE',
                    badgeClass: 'bg-info text-dark',
                    icon: 'bi-image-fill',
                    iconColor: '#0891b2',
                    iconBg: '#cffafe',
                    options: [
                        {
                            id: 'image_browser',
                            recommended: true,
                            title: 'View Image in Browser Tab',
                            desc: 'Display high resolution photo in new tab',
                            icon: 'bi-arrows-fullscreen',
                            iconColor: '#0891b2',
                            iconBg: '#cffafe',
                            handler: (file) => window.open(file.url, '_blank')
                        }
                    ]
                };
            }

            // 10. Design & Adobe Creative Files (.psd, .ai, .eps, .cdr)
            if (['psd', 'psb', 'ai', 'eps', 'cdr', 'indd'].includes(ext)) {
                return {
                    badge: `${extUpper} DESIGN`,
                    badgeClass: 'bg-primary text-white',
                    icon: 'bi-palette-fill',
                    iconColor: '#7c3aed',
                    iconBg: '#ede9fe',
                    options: [
                        {
                            id: 'design_desktop',
                            recommended: true,
                            title: `Open in Adobe / CorelDRAW (Desktop)`,
                            desc: `Launch desktop design suite to edit ${extUpper} artwork`,
                            icon: 'bi-vector-pen',
                            iconColor: '#7c3aed',
                            iconBg: '#ede9fe',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'Adobe / Corel Application')
                        },
                        {
                            id: 'download_design',
                            title: `Download .${ext} Source File`,
                            desc: 'Save creative graphics file to your PC',
                            icon: 'bi-download',
                            iconColor: '#2563eb',
                            iconBg: '#eff6ff',
                            handler: (file) => this._triggerDirectDownload(file)
                        }
                    ]
                };
            }

            // 11. Videos & Drone Flights (.mp4, .webm, .mkv, .avi, .mov, .wmv)
            if (['mp4', 'webm', 'mkv', 'avi', 'mov', 'wmv', 'flv', '3gp'].includes(ext)) {
                return {
                    badge: 'VIDEO',
                    badgeClass: 'bg-secondary text-white',
                    icon: 'bi-camera-video-fill',
                    iconColor: '#4f46e5',
                    iconBg: '#e0e7ff',
                    options: [
                        {
                            id: 'video_browser',
                            recommended: true,
                            title: 'Play in Browser Video Player',
                            desc: 'Stream high-definition drone flight video directly in tab',
                            icon: 'bi-play-circle-fill',
                            iconColor: '#4f46e5',
                            iconBg: '#e0e7ff',
                            handler: (file) => window.open(file.url, '_blank')
                        },
                        {
                            id: 'video_desktop',
                            title: 'VLC / Windows Media Player (Desktop)',
                            desc: 'Open video in your computer\'s dedicated media player',
                            icon: 'bi-display',
                            iconColor: '#ea580c',
                            iconBg: '#ffedd5',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'VLC Media Player')
                        }
                    ]
                };
            }

            // 12. Audio Files (.mp3, .wav, .aac, .ogg, .m4a)
            if (['mp3', 'wav', 'aac', 'ogg', 'flac', 'm4a', 'wma'].includes(ext)) {
                return {
                    badge: 'AUDIO',
                    badgeClass: 'bg-success text-white',
                    icon: 'bi-music-note-beamed',
                    iconColor: '#059669',
                    iconBg: '#d1fae5',
                    options: [
                        {
                            id: 'audio_browser',
                            recommended: true,
                            title: 'Play Audio in Browser',
                            desc: 'Listen to voice note or recording in a new tab',
                            icon: 'bi-play-fill',
                            iconColor: '#059669',
                            iconBg: '#d1fae5',
                            handler: (file) => window.open(file.url, '_blank')
                        },
                        {
                            id: 'audio_desktop',
                            title: 'Media Player (Desktop)',
                            desc: 'Open audio in your default desktop player',
                            icon: 'bi-speaker-fill',
                            iconColor: '#2563eb',
                            iconBg: '#eff6ff',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'Audio Player')
                        }
                    ]
                };
            }

            // 13. Drone GIS, Remote Sensing & Survey Point Clouds (.las, .laz, .tif, .shp, .dem)
            if (['las', 'laz', 'tif', 'tiff', 'shp', 'shx', 'dbf', 'geojson', 'geotiff', 'dem', 'asc'].includes(ext)) {
                return {
                    badge: 'GIS / REMOTE SENSING',
                    badgeClass: 'bg-dark text-white',
                    icon: 'bi-layers-fill',
                    iconColor: '#0f766e',
                    iconBg: '#ccfbf1',
                    options: [
                        {
                            id: 'gis_desktop',
                            recommended: true,
                            title: 'QGIS / Global Mapper (Desktop)',
                            desc: 'Open geospatial dataset & elevation model in GIS software',
                            icon: 'bi-geo-alt-fill',
                            iconColor: '#0f766e',
                            iconBg: '#ccfbf1',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'QGIS / Global Mapper')
                        },
                        {
                            id: 'download_gis',
                            title: `Download .${ext} Dataset`,
                            desc: 'Save raw GIS survey file to local storage',
                            icon: 'bi-download',
                            iconColor: '#2563eb',
                            iconBg: '#eff6ff',
                            handler: (file) => this._triggerDirectDownload(file)
                        }
                    ]
                };
            }

            // 14. Text, Code, Data & Survey Logs (.txt, .log, .dat, .json, .xml, .sql)
            if (['txt', 'log', 'dat', 'json', 'xml', 'yaml', 'yml', 'sql', 'html', 'htm'].includes(ext)) {
                return {
                    badge: `${extUpper} DATA`,
                    badgeClass: 'bg-secondary text-white',
                    icon: 'bi-file-code-fill',
                    iconColor: '#334155',
                    iconBg: '#f1f5f9',
                    options: [
                        {
                            id: 'text_browser',
                            recommended: true,
                            title: 'View Plain Text in Browser',
                            desc: 'Open and read file contents in a new tab',
                            icon: 'bi-eye-fill',
                            iconColor: '#2563eb',
                            iconBg: '#eff6ff',
                            handler: (file) => window.open(file.url, '_blank')
                        },
                        {
                            id: 'text_desktop',
                            title: 'Notepad / VS Code (Desktop)',
                            desc: 'Open in computer\'s default text/code editor',
                            icon: 'bi-pencil-square',
                            iconColor: '#059669',
                            iconBg: '#d1fae5',
                            handler: (file) => this._downloadAndPromptLaunch(file, 'Text Editor')
                        }
                    ]
                };
            }

            // 15. UNIVERSAL DYNAMIC FALLBACK (FOR ANY OTHER EXTENSION IN THE WORLD)
            return {
                badge: extUpper ? `.${extUpper}` : 'FILE',
                badgeClass: 'bg-dark text-white',
                icon: 'bi-file-earmark-text-fill',
                iconColor: '#475569',
                iconBg: '#f1f5f9',
                options: [
                    {
                        id: 'universal_desktop',
                        recommended: true,
                        title: `Open in Windows Default Software for .${extUpper || 'file'}`,
                        desc: `Download and launch directly in your PC's registered application`,
                        icon: 'bi-laptop',
                        iconColor: '#2563eb',
                        iconBg: '#eff6ff',
                        handler: (file) => this._downloadAndPromptLaunch(file, `Default .${extUpper} Application`)
                    },
                    {
                        id: 'universal_browser',
                        title: 'Attempt Browser View',
                        desc: 'Try opening file in a new browser tab',
                        icon: 'bi-window',
                        iconColor: '#059669',
                        iconBg: '#d1fae5',
                        handler: (file) => window.open(file.url, '_blank')
                    }
                ]
            };
        }

        _populateModal(file) {
            const profile = this._getFileProfile(file.ext);

            // Set Header Badges and Icons
            const extBadge = document.getElementById('gtmsOpenWithExtBadge');
            if (extBadge) {
                extBadge.textContent = profile.badge;
                extBadge.className = `badge ${profile.badgeClass} fw-bold text-uppercase px-2 py-0.5`;
            }

            const headerIcon = document.getElementById('gtmsOpenWithHeaderIcon');
            if (headerIcon) {
                headerIcon.className = `bi ${profile.icon}`;
            }

            // Set Summary Fields
            const docNameEl = document.getElementById('gtmsOpenWithDocName');
            if (docNameEl) docNameEl.textContent = file.docName || 'Attached Document';

            const fileNameEl = document.getElementById('gtmsOpenWithFileName');
            if (fileNameEl) fileNameEl.textContent = file.fileName;

            const fileSizeEl = document.getElementById('gtmsOpenWithFileSize');
            if (fileSizeEl) fileSizeEl.textContent = file.fileSize || `${profile.badge} Document`;

            // Reset Preference Button Visibility
            const resetBtn = document.getElementById('gtmsResetChoicesBtn');
            const savedPref = localStorage.getItem(`gtms_pref_${file.ext}`);
            if (resetBtn) {
                if (savedPref) {
                    resetBtn.classList.remove('d-none');
                } else {
                    resetBtn.classList.add('d-none');
                }
            }

            const rememberCheckbox = document.getElementById('gtmsRememberChoice');
            if (rememberCheckbox) rememberCheckbox.checked = !!savedPref;

            // Direct Download Button at Footer
            const directDlBtn = document.getElementById('gtmsOpenWithDirectDownload');
            if (directDlBtn) {
                directDlBtn.href = file.url;
                directDlBtn.setAttribute('download', file.fileName);
                directDlBtn.onclick = (e) => {
                    this._triggerDirectDownload(file);
                    if (this.modalInstance) this.modalInstance.hide();
                };
            }

            // Build Options List
            const listContainer = document.getElementById('gtmsOpenWithOptionsList');
            if (!listContainer) return;
            listContainer.innerHTML = '';

            profile.options.forEach((opt) => {
                const card = document.createElement('a');
                card.href = 'javascript:void(0)';
                card.className = `gtms-open-option-card ${opt.recommended ? 'recommended' : ''}`;
                card.setAttribute('data-action-id', opt.id);

                card.innerHTML = `
                    <div class="d-flex align-items-center gap-3">
                        <div class="gtms-open-option-icon shadow-xs" style="background: ${opt.iconBg}; color: ${opt.iconColor};">
                            <i class="bi ${opt.icon}"></i>
                        </div>
                        <div>
                            <div class="gtms-open-option-title">${opt.title}</div>
                            <div class="gtms-open-option-desc">${opt.desc}</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right gtms-open-option-arrow"></i>
                `;

                card.addEventListener('click', (ev) => {
                    ev.preventDefault();

                    // Check remember choice
                    if (rememberCheckbox && rememberCheckbox.checked) {
                        localStorage.setItem(`gtms_pref_${file.ext}`, opt.id);
                    } else {
                        localStorage.removeItem(`gtms_pref_${file.ext}`);
                    }

                    // Execute Option Handler
                    opt.handler(file);

                    // Close modal
                    if (this.modalInstance) this.modalInstance.hide();
                });

                listContainer.appendChild(card);
            });
        }

        _executeAction(actionId, file) {
            const profile = this._getFileProfile(file.ext);
            const opt = profile.options.find(o => o.id === actionId);
            if (opt) {
                opt.handler(file);
                return true;
            }
            return false;
        }

        /**
         * Office Protocol Launcher (ms-word:, ms-excel:, ms-powerpoint:)
         * Falls back smoothly to download if protocol cannot open
         */
        _openWithOfficeProtocol(scheme, file) {
            const protocolUrl = `${scheme}:ofe|u|${file.url}`;
            
            // Try launching the protocol
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            let protocolWorked = false;
            const timeout = setTimeout(() => {
                document.body.removeChild(iframe);
                if (!protocolWorked) {
                    // Protocol might not be supported or prompt blocked: trigger download & alert
                    this._downloadAndPromptLaunch(file, scheme.replace('ms-', '').toUpperCase());
                }
            }, 1200);

            try {
                window.location.href = protocolUrl;
                protocolWorked = true;
                clearTimeout(timeout);
                setTimeout(() => {
                    if (iframe.parentNode) document.body.removeChild(iframe);
                }, 500);
            } catch (err) {
                clearTimeout(timeout);
                if (iframe.parentNode) document.body.removeChild(iframe);
                this._downloadAndPromptLaunch(file, scheme.replace('ms-', '').toUpperCase());
            }
        }

        /**
         * Triggers download and notifies user to open with default PC software
         */
        _downloadAndPromptLaunch(file, appName) {
            this._triggerDirectDownload(file);
            this._showToast(`Opening ${file.fileName} in ${appName}... Check your downloads.`, 'success');
        }

        _triggerDirectDownload(file) {
            const tempLink = document.createElement('a');
            tempLink.href = file.url;
            tempLink.setAttribute('download', file.fileName);
            tempLink.style.display = 'none';
            document.body.appendChild(tempLink);
            tempLink.click();
            setTimeout(() => {
                if (tempLink.parentNode) document.body.removeChild(tempLink);
            }, 300);
        }

        _showToast(message, type = 'info') {
            if (typeof toastr !== 'undefined') {
                toastr[type](message, 'GTMS File Dispatcher');
            } else {
                console.log(`[GTMS Open With] ${message}`);
            }
        }
    }

    // Export singleton
    return new GtmsOpenWithManager();
}));
