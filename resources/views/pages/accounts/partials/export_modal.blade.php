{{--
    GTMS Enterprise Universal Multi-Format Export Modal
    Formats: CSV, Excel (.xls/.xlsx), PDF, Word (.doc), Print, Clipboard
--}}
@php
    $modalId = $id ?? 'gtmsExportModal';
    $modalTitle = $title ?? 'Financial Records Export';
    $defaultFilename = $filename ?? ('GTMS_Export_' . date('Ymd_His'));
    $targetSelector = $tableSelector ?? 'table';
    $hasServerCsv = !empty($serverCsvUrl);
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(255,255,255,0.15);">
                        <i class="fas fa-file-export fs-6 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="{{ $modalId }}Label" style="font-size: 1.05rem;">
                            Export Records • {{ $modalTitle }}
                        </h5>
                        <small class="text-white-50" style="font-size: 0.76rem;">Select your desired format (Excel, CSV, PDF, Word) to download or print</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-light">
                {{-- Format Selector Grid --}}
                <label class="form-label text-uppercase fw-bold text-muted mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    1. Choose Export Format
                </label>
                <div class="row g-3 mb-4" id="{{ $modalId }}_formats">
                    {{-- 1. Excel --}}
                    <div class="col-md-4 col-sm-6">
                        <div class="export-format-card active" data-format="excel" style="cursor: pointer;">
                            <input class="form-check-input d-none" type="radio" name="export_format_{{ $modalId }}" id="fmt_excel_{{ $modalId }}" value="excel" checked>
                            <div class="d-flex align-items-start gap-2">
                                <div class="fmt-icon-box bg-success-subtle text-success">
                                    <i class="fas fa-file-excel fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">Excel Sheet</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">Recommended</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">.xlsx / .xls Workbook</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. CSV --}}
                    <div class="col-md-4 col-sm-6">
                        <div class="export-format-card" data-format="csv" style="cursor: pointer;">
                            <input class="form-check-input d-none" type="radio" name="export_format_{{ $modalId }}" id="fmt_csv_{{ $modalId }}" value="csv">
                            <div class="d-flex align-items-start gap-2">
                                <div class="fmt-icon-box bg-primary-subtle text-primary">
                                    <i class="fas fa-file-csv fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">CSV File</span>
                                        <span class="badge bg-light text-primary border" style="font-size: 0.65rem;">UTF-8 BOM</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Comma-separated table</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. PDF --}}
                    <div class="col-md-4 col-sm-6">
                        <div class="export-format-card" data-format="pdf" style="cursor: pointer;">
                            <input class="form-check-input d-none" type="radio" name="export_format_{{ $modalId }}" id="fmt_pdf_{{ $modalId }}" value="pdf">
                            <div class="d-flex align-items-start gap-2">
                                <div class="fmt-icon-box bg-danger-subtle text-danger">
                                    <i class="fas fa-file-pdf fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">PDF Document</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.65rem;">Print-Ready</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Audit-ready report</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Word --}}
                    <div class="col-md-4 col-sm-6">
                        <div class="export-format-card" data-format="word" style="cursor: pointer;">
                            <input class="form-check-input d-none" type="radio" name="export_format_{{ $modalId }}" id="fmt_word_{{ $modalId }}" value="word">
                            <div class="d-flex align-items-start gap-2">
                                <div class="fmt-icon-box bg-info-subtle text-info">
                                    <i class="fas fa-file-word fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">MS Word</span>
                                        <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">.doc Format</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Editable document</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Print --}}
                    <div class="col-md-4 col-sm-6">
                        <div class="export-format-card" data-format="print" style="cursor: pointer;">
                            <input class="form-check-input d-none" type="radio" name="export_format_{{ $modalId }}" id="fmt_print_{{ $modalId }}" value="print">
                            <div class="d-flex align-items-start gap-2">
                                <div class="fmt-icon-box bg-secondary-subtle text-dark">
                                    <i class="fas fa-print fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">Print View</span>
                                        <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">A4 Page</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Direct printer / PDF</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. Clipboard --}}
                    <div class="col-md-4 col-sm-6">
                        <div class="export-format-card" data-format="clipboard" style="cursor: pointer;">
                            <input class="form-check-input d-none" type="radio" name="export_format_{{ $modalId }}" id="fmt_clip_{{ $modalId }}" value="clipboard">
                            <div class="d-flex align-items-start gap-2">
                                <div class="fmt-icon-box bg-warning-subtle text-warning">
                                    <i class="fas fa-clipboard-list fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">Copy Table</span>
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 0.65rem;">TSV Clipboard</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">Paste into Google Sheets</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configuration Row --}}
                <div class="bg-white p-3 rounded border mb-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-muted mb-1">Custom File Name (Optional)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-tag"></i></span>
                                <input type="text" class="form-control" id="{{ $modalId }}_filename" value="{{ $defaultFilename }}">
                                <span class="input-group-text bg-light fw-bold text-secondary" id="{{ $modalId }}_extension">.xlsx</span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted mb-1">Export Data Scope</label>
                            <div class="d-flex flex-column gap-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="{{ $modalId }}_scope" id="{{ $modalId }}_scope_visible" value="visible" checked>
                                    <label class="form-check-label small text-dark fw-semibold" for="{{ $modalId }}_scope_visible">
                                        Current Visible Records
                                    </label>
                                </div>
                                @if($hasServerCsv)
                                <div class="form-check" id="{{ $modalId }}_server_scope_box">
                                    <input class="form-check-input" type="radio" name="{{ $modalId }}_scope" id="{{ $modalId }}_scope_full" value="full">
                                    <label class="form-check-label small text-dark fw-semibold" for="{{ $modalId }}_scope_full">
                                        Complete Filtered Database Dataset
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Information Strip --}}
                <div class="d-flex align-items-center justify-content-between p-2.5 rounded bg-white border small text-muted" style="font-size: 0.76rem;">
                    <div>
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        <span id="{{ $modalId }}_info_text">Ready to export table rows in high-fidelity formatted Excel workbook.</span>
                    </div>
                    <span class="badge bg-light text-dark border font-monospace" id="{{ $modalId }}_row_count_badge">Loading rows…</span>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer bg-white py-2.5 px-4 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-navy btn-sm px-4 fw-bold shadow-sm" id="{{ $modalId }}_btn_download" style="background-color: #0F1E4D; color: #fff; min-width: 170px;">
                    <i class="fas fa-download me-1"></i> <span id="{{ $modalId }}_btn_label">Download Excel</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .export-format-card {
        background: #FFFFFF;
        border: 2px solid #E2E8F0;
        border-radius: 10px;
        padding: 12px;
        transition: all 0.16s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
    }
    .export-format-card:hover {
        border-color: #1E3A8A;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .export-format-card.active {
        border-color: #0F1E4D !important;
        background-color: #EFF6FF !important;
        box-shadow: 0 0 0 1px #0F1E4D;
    }
    .fmt-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>

<script>
(function() {
    const modalId = '{{ $modalId }}';
    const targetTableSelector = '{{ $targetSelector }}';
    const defaultFilename = '{{ $defaultFilename }}';
    const serverCsvUrl = '{{ $serverCsvUrl ?? "" }}';

    function initExportModal() {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;

        const cards = modalEl.querySelectorAll('.export-format-card');
        const filenameInput = document.getElementById(modalId + '_filename');
        const extSpan = document.getElementById(modalId + '_extension');
        const infoText = document.getElementById(modalId + '_info_text');
        const btnLabel = document.getElementById(modalId + '_btn_label');
        const btnDownload = document.getElementById(modalId + '_btn_download');
        const rowCountBadge = document.getElementById(modalId + '_row_count_badge');

        let selectedFormat = 'excel';

        const extMap = {
            excel: '.xls',
            csv: '.csv',
            pdf: '.pdf',
            word: '.doc',
            print: '',
            clipboard: ''
        };

        const labelMap = {
            excel: 'Download Excel',
            csv: 'Download CSV',
            pdf: 'Open / Save PDF',
            word: 'Download Word',
            print: 'Print Document',
            clipboard: 'Copy to Clipboard'
        };

        const infoMap = {
            excel: 'Ready to export table rows in high-fidelity formatted Excel workbook.',
            csv: 'Universal Comma-Separated Values (CSV) with UTF-8 BOM encoding.',
            pdf: 'High-definition printable report with GTMS header and clean margins.',
            word: 'Editable Microsoft Word document formatted for A4 / Landscape layout.',
            print: 'Triggers formatted browser print preview with company header.',
            clipboard: 'Copies structured tab-separated values to paste directly into spreadsheets.'
        };

        function getTargetTable() {
            return document.querySelector(targetTableSelector) || document.querySelector('table');
        }

        function updateRowCount() {
            const tbl = getTargetTable();
            if (tbl) {
                const rows = tbl.querySelectorAll('tbody tr');
                const validRows = Array.from(rows).filter(r => !r.classList.contains('empty-row') && r.querySelectorAll('td').length > 1);
                rowCountBadge.textContent = `${validRows.length} Table Rows Found`;
            } else {
                rowCountBadge.textContent = 'Table Ready';
            }
        }

        modalEl.addEventListener('show.bs.modal', function () {
            updateRowCount();
        });

        cards.forEach(card => {
            card.addEventListener('click', function() {
                cards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                selectedFormat = this.dataset.format;
                if (extSpan) extSpan.textContent = extMap[selectedFormat] || '';
                if (btnLabel) btnLabel.textContent = labelMap[selectedFormat] || 'Export';
                if (infoText) infoText.textContent = infoMap[selectedFormat] || '';

                const serverBox = document.getElementById(modalId + '_server_scope_box');
                if (serverBox) {
                    if (selectedFormat === 'csv' && serverCsvUrl) {
                        serverBox.style.display = 'block';
                    } else {
                        serverBox.style.display = 'none';
                        const visRadio = document.getElementById(modalId + '_scope_visible');
                        if (visRadio) visRadio.checked = true;
                    }
                }
            });
        });

        // Extraction helper
        function extractTableData() {
            const tbl = getTargetTable();
            if (!tbl) return { headers: [], rows: [] };

            const headers = [];
            const headerCells = tbl.querySelectorAll('thead th');
            const skipIndices = new Set();

            headerCells.forEach((th, idx) => {
                const text = th.innerText.trim();
                // Skip Actions, checkboxes, buttons
                if (text.toLowerCase().includes('action') || text === '#' || text === '') {
                    // we can keep '#' if desirable, but skip 'action'
                    if (text.toLowerCase().includes('action')) {
                        skipIndices.add(idx);
                        return;
                    }
                }
                headers.push({ index: idx, title: text });
            });

            const rows = [];
            const bodyRows = tbl.querySelectorAll('tbody tr');

            bodyRows.forEach(tr => {
                if (tr.classList.contains('empty-row') || tr.querySelectorAll('td').length <= 1) return;
                const rowCells = tr.querySelectorAll('td');
                if (rowCells.length === 0) return;

                const rowData = [];
                headers.forEach(h => {
                    const td = rowCells[h.index];
                    let cellText = td ? td.innerText.trim() : '';
                    // Clean unwanted multiple newlines
                    cellText = cellText.replace(/\r?\n+/g, ' ').replace(/\s+/g, ' ');
                    rowData.push(cellText);
                });
                rows.push(rowData);
            });

            return { headers: headers.map(h => h.title), rows: rows };
        }

        // Export Actions
        btnDownload.addEventListener('click', function() {
            const baseName = (filenameInput.value || defaultFilename).trim();
            const scopeFullRadio = document.getElementById(modalId + '_scope_full');
            const isFullServer = scopeFullRadio && scopeFullRadio.checked;

            if (selectedFormat === 'csv' && isFullServer && serverCsvUrl) {
                // Direct server streamed CSV download
                window.location.href = serverCsvUrl;
                bootstrap.Modal.getInstance(modalEl)?.hide();
                return;
            }

            const data = extractTableData();
            if (data.rows.length === 0 && selectedFormat !== 'print') {
                alert('No data rows found to export.');
                return;
            }

            switch(selectedFormat) {
                case 'csv':
                    exportToCsv(data, baseName + '.csv');
                    break;
                case 'excel':
                    exportToExcel(data, baseName + '.xls');
                    break;
                case 'word':
                    exportToWord(data, baseName + '.doc');
                    break;
                case 'pdf':
                case 'print':
                    exportToPrint(data, baseName, selectedFormat === 'pdf');
                    break;
                case 'clipboard':
                    copyToClipboard(data);
                    break;
            }

            bootstrap.Modal.getInstance(modalEl)?.hide();
        });

        function triggerDownload(blob, filename) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.setAttribute('download', filename);
            document.body.appendChild(a);

            if (typeof a.click === 'function') {
                a.click();
            } else {
                a.dispatchEvent(new MouseEvent('click', { bubbles: false, cancelable: true, view: window }));
            }

            setTimeout(() => {
                if (a.parentNode) {
                    a.parentNode.removeChild(a);
                }
                URL.revokeObjectURL(url);
            }, 1000);
        }

        // 1. CSV
        function exportToCsv(data, filename) {
            let csvContent = '\uFEFF'; // UTF-8 BOM
            csvContent += data.headers.map(h => `"${h.replace(/"/g, '""')}"`).join(',') + '\r\n';
            data.rows.forEach(r => {
                csvContent += r.map(c => `"${c.replace(/"/g, '""')}"`).join(',') + '\r\n';
            });
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            triggerDownload(blob, filename);
        }

        // 2. Excel (Rich HTML Workbook)
        function exportToExcel(data, filename) {
            const title = '{{ $modalTitle }}';
            const xlHead = '<!--[if gte mso 9]><xml><' + 'x:ExcelWorkbook><' + 'x:ExcelWorksheets><' + 'x:ExcelWorksheet><' + 'x:Name>GTMS Records</' + 'x:Name><' + 'x:WorksheetOptions><' + 'x:DisplayGridlines/></' + 'x:WorksheetOptions></' + 'x:ExcelWorksheet></' + 'x:ExcelWorksheets></' + 'x:ExcelWorkbook></xml><![endif]-->';
            let html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
${xlHead}
<style>
    body { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; font-size: 11pt; }
    .header-title { font-size: 16pt; font-weight: bold; color: #0F1E4D; text-align: left; }
    .header-sub { font-size: 10pt; color: #475569; margin-bottom: 15px; }
    table { border-collapse: collapse; width: 100%; }
    th { background-color: #0F1E4D; color: #FFFFFF; font-weight: bold; padding: 10px 8px; border: 1px solid #CBD5E1; text-align: left; }
    td { padding: 8px; border: 1px solid #CBD5E1; vertical-align: middle; }
    .alt-row { background-color: #F8FAFC; }
    .financial { text-align: right; font-family: 'Consolas', monospace; }
</style>
</head>
<body>
<div class="header-title">GTMS • Granite / Mining Tracking Management System</div>
<div class="header-sub">${title} | Exported on ${new Date().toLocaleString()}</div>
<table>
    <thead>
        <tr>${data.headers.map(h => `<th>${h}</th>`).join('')}</tr>
    </thead>
    <tbody>
        ${data.rows.map((row, i) => `
            <tr class="${i % 2 === 1 ? 'alt-row' : ''}">
                ${row.map(cell => `<td>${cell}</td>`).join('')}
            </tr>
        `).join('')}
    </tbody>
</table>
</body>
</html>`;
            const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            triggerDownload(blob, filename);
        }

        // 3. Word (.doc)
        function exportToWord(data, filename) {
            const title = '{{ $modalTitle }}';
            let html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
    @page Section1 { size: 11in 8.5in; mso-page-orientation: landscape; margin: 0.5in; }
    div.Section1 { page: Section1; }
    body { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; font-size: 10pt; color: #0F172A; }
    h2 { color: #0F1E4D; margin-bottom: 2px; }
    .meta { font-size: 9pt; color: #64748B; margin-bottom: 16px; border-bottom: 2px solid #0F1E4D; padding-bottom: 6px; }
    table { border-collapse: collapse; width: 100%; }
    th { background-color: #0F1E4D; color: #FFFFFF; font-weight: bold; padding: 8px 6px; border: 1px solid #94A3B8; text-align: left; font-size: 9.5pt; }
    td { padding: 6px; border: 1px solid #CBD5E1; font-size: 9pt; }
    tr:nth-child(even) { background-color: #F8FAFC; }
</style>
</head>
<body>
<div class="Section1">
    <h2>GTMS — Financial Management System</h2>
    <div class="meta">Document: <strong>${title}</strong> &bull; Generated: ${new Date().toLocaleString()}</div>
    <table>
        <thead>
            <tr>${data.headers.map(h => `<th>${h}</th>`).join('')}</tr>
        </thead>
        <tbody>
            ${data.rows.map(row => `
                <tr>${row.map(cell => `<td>${cell}</td>`).join('')}</tr>
            `).join('')}
        </tbody>
    </table>
</div>
</body>
</html>`;
            const blob = new Blob([html], { type: 'application/msword;charset=utf-8;' });
            triggerDownload(blob, filename);
        }

        // 4. Print / PDF
        function exportToPrint(data, title, isPdf) {
            const printWin = window.open('', '_blank', 'width=1100,height=750');
            if (!printWin) {
                alert('Pop-up blocked. Please allow pop-ups to open the print report.');
                return;
            }

            const html = `<!DOCTYPE html>
<html>
<head>
    <title>${title} • GTMS</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        @media print {
            @page { size: landscape; margin: 12mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 25px; color: #0F172A; }
        .report-header { border-bottom: 3px solid #0F1E4D; padding-bottom: 12px; margin-bottom: 20px; }
        .table th { background-color: #0F1E4D !important; color: #FFFFFF !important; font-weight: 700; font-size: 0.85rem; }
        .table td { font-size: 0.82rem; vertical-align: middle; }
    </style>
</head>
<body>
    <div class="no-print d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">
        <div>
            <strong>${isPdf ? 'Save as PDF' : 'Print Document'}:</strong> Use the browser dialog to print or choose "Save as PDF".
        </div>
        <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold px-3">
            <i class="fas fa-print"></i> Print / Save PDF
        </button>
    </div>

    <div class="report-header d-flex justify-content-between align-items-end">
        <div>
            <h3 class="fw-bold mb-1" style="color: #0F1E4D;">GTMS • Mining Tracking Management System</h3>
            <div class="text-secondary small fw-semibold">${title}</div>
        </div>
        <div class="text-end small text-muted">
            <div>Printed: ${new Date().toLocaleString()}</div>
            <div>Official Accounts Register</div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>${data.headers.map(h => `<th>${h}</th>`).join('')}</tr>
        </thead>
        <tbody>
            ${data.rows.map(row => `
                <tr>${row.map(cell => `<td>${cell}</td>`).join('')}</tr>
            `).join('')}
        </tbody>
    </table>

    <div class="mt-4 pt-3 border-top d-flex justify-content-between small text-muted">
        <div>Confidential Internal Document &bull; GTMS ERP</div>
        <div>Total Records: ${data.rows.length}</div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        };
    <\/script>
</body>
</html>`;
            printWin.document.open();
            printWin.document.write(html);
            printWin.document.close();
        }

        // 5. Copy to Clipboard (TSV)
        function copyToClipboard(data) {
            let tsv = data.headers.join('\t') + '\r\n';
            data.rows.forEach(r => {
                tsv += r.join('\t') + '\r\n';
            });
            navigator.clipboard.writeText(tsv).then(() => {
                if (typeof toastr !== 'undefined') {
                    toastr.success('Table data copied to clipboard! Paste (Ctrl+V) into Excel or Sheets.', 'Copied Successfully');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied to Clipboard!',
                        text: 'Table data copied as TSV. Paste directly into Excel or Google Sheets.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('Table data copied to clipboard! You can paste directly into Excel.');
                }
            }).catch(err => {
                alert('Could not copy to clipboard: ' + err);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initExportModal);
    } else {
        initExportModal();
    }
})();
</script>
