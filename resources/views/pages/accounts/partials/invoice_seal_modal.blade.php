<!-- INVOICE SEAL & SIGNATURE MANAGEMENT MODAL & CONTROLS -->
<style>
  .seal-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(4px);
    z-index: 100001;
    justify-content: center;
    align-items: center;
    padding: 20px;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  }
  .seal-modal-backdrop.show {
    display: flex;
  }
  .seal-modal-box {
    background: #ffffff;
    border-radius: 14px;
    width: 100%;
    max-width: 580px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    animation: sealModalFadeIn 0.2s ease-out;
    display: flex;
    flex-direction: column;
  }
  @keyframes sealModalFadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to { opacity: 1; transform: scale(1); }
  }
  .seal-modal-header {
    padding: 16px 22px;
    background: #0F1E4D;
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .seal-modal-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: 0.3px;
  }
  .seal-modal-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 22px;
    cursor: pointer;
    line-height: 1;
    padding: 0 4px;
    transition: color 0.15s;
  }
  .seal-modal-close:hover {
    color: #ffffff;
  }
  .seal-modal-body {
    padding: 22px;
    max-height: 75vh;
    overflow-y: auto;
  }
  .seal-option-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
  }
  .seal-option-card {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8fafc;
  }
  .seal-option-card:hover {
    border-color: #93c5fd;
    background: #f0fdf4;
  }
  .seal-option-card.selected {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
  }
  .seal-option-card input[type="radio"] {
    margin-top: 4px;
    cursor: pointer;
    accent-color: #2563eb;
    width: 18px;
    height: 18px;
  }
  .seal-option-info {
    flex: 1;
  }
  .seal-option-title {
    font-weight: 700;
    font-size: 14px;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
  }
  .seal-option-desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.4;
  }
  .seal-upload-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 18px;
    text-align: center;
    background: #ffffff;
    transition: all 0.2s;
    cursor: pointer;
    margin-top: 10px;
  }
  .seal-upload-zone:hover {
    border-color: #2563eb;
    background: #f8fafc;
  }
  .seal-upload-zone i {
    font-size: 32px;
    color: #64748b;
    margin-bottom: 8px;
  }
  .seal-preview-box {
    margin-top: 14px;
    padding: 12px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
  }
  .seal-preview-box img {
    max-height: 60px;
    max-width: 160px;
    object-fit: contain;
    background: #fff;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
  }
  .seal-modal-footer {
    padding: 14px 22px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .seal-btn-primary {
    background: #2563eb;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.15s;
  }
  .seal-btn-primary:hover {
    background: #1d4ed8;
  }
  .seal-btn-danger {
    background: transparent;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s;
  }
  .seal-btn-danger:hover {
    background: #fee2e2;
  }
  .action-btn.seal-status-btn {
    background: #0284c7;
  }
  .action-btn.seal-status-btn:hover {
    background: #0369a1;
  }
  .action-btn.seal-status-btn.blank-mode {
    background: #475569;
  }
  .action-btn.seal-status-btn.blank-mode:hover {
    background: #334155;
  }

  /* Invoice Seal Slot inside Document */
  .invoice-seal-slot {
    min-height: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 4px 0;
    width: 100%;
  }
  .seal-empty-space {
    height: 80px;
    min-height: 80px;
    width: 100%;
    display: block;
    background: transparent;
  }
  .invoice-seal-img {
    max-width: 140px;
    max-height: 78px;
    width: auto;
    height: auto;
    display: block;
    margin: 0 auto;
    object-fit: contain;
  }

  @media print {
    .seal-modal-backdrop, .no-print-bar {
      display: none !important;
    }
    .invoice-seal-slot {
      height: 80px !important;
      min-height: 80px !important;
    }
    .seal-empty-space {
      height: 80px !important;
      min-height: 80px !important;
      display: block !important;
      width: 100% !important;
    }
    .invoice-seal-img {
      max-height: 78px !important;
    }
  }
</style>

<!-- MODAL MARKUP -->
<div id="sealModal" class="seal-modal-backdrop" onclick="handleSealBackdropClick(event)">
  <div class="seal-modal-box">
    <div class="seal-modal-header">
      <h3><i class="fas fa-stamp text-warning"></i> Invoice Seal &amp; Authorized Signatory</h3>
      <button type="button" class="seal-modal-close" onclick="closeSealModal()">&times;</button>
    </div>

    <div class="seal-modal-body">
      <div class="seal-option-cards">
        <!-- Option 1: Blank for Manual Rubber Stamp & Pen Sign -->
        <label class="seal-option-card" id="card-mode-blank" onclick="selectSealMode('blank')">
          <input type="radio" name="seal_display_mode" value="blank" id="radio-mode-blank">
          <div class="seal-option-info">
            <div class="seal-option-title">
              <i class="fas fa-pen-nib text-secondary"></i> Blank (Empty White Space)
              <span style="font-size: 11px; background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">Recommended</span>
            </div>
            <div class="seal-option-desc">
              Leaves an 80px clean empty white space on the invoice. You can take a physical printout and manually affix the rubber stamp seal and pen signature before handing to customer.
            </div>
          </div>
        </label>

        <!-- Option 2: Digital Seal & Signature -->
        <label class="seal-option-card" id="card-mode-digital" onclick="selectSealMode('digital')">
          <input type="radio" name="seal_display_mode" value="digital" id="radio-mode-digital">
          <div class="seal-option-info">
            <div class="seal-option-title">
              <i class="fas fa-file-image text-primary"></i> Digital Seal &amp; Signature (Uploaded Image)
            </div>
            <div class="seal-option-desc">
              Displays your manually uploaded official seal and signature image on the invoice.
            </div>
          </div>
        </label>
      </div>

      <!-- Upload Section (Shown when digital mode or user wants to upload) -->
      <div id="sealUploadSection" style="display: none; border-top: 1px solid #e2e8f0; padding-top: 14px;">
        <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 6px;">
          <i class="fas fa-upload me-1 text-primary"></i> Upload Custom Seal &amp; Signature Image
        </div>
        <div class="seal-upload-zone" onclick="document.getElementById('sealFileInput').click()">
          <i class="fas fa-cloud-upload-alt"></i>
          <div style="font-weight: 600; font-size: 13px; color: #334155;">Click to browse or drag &amp; drop seal image</div>
          <div style="font-size: 11.5px; color: #64748b; margin-top: 3px;">Supports PNG, JPG, JPEG, WEBP (Max 5MB • Transparent PNG recommended)</div>
          <input type="file" id="sealFileInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display: none;" onchange="handleSealFileSelected(event)">
        </div>

        <div id="sealUploadLoading" style="display: none; text-align: center; padding: 10px; color: #2563eb; font-weight: 600; font-size: 13px;">
          <i class="fas fa-spinner fa-spin me-1"></i> Uploading and saving seal...
        </div>

        <div id="sealPreviewContainer" class="seal-preview-box" style="display: none;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <img id="sealPreviewImg" src="" alt="Seal Preview">
            <div>
              <div style="font-weight: 700; font-size: 12.5px; color: #0f172a;">Active Seal &amp; Signature</div>
              <div style="font-size: 11px; color: #15803d;"><i class="fas fa-check-circle"></i> Ready to print</div>
            </div>
          </div>
          <button type="button" class="seal-btn-danger" onclick="deleteCustomSeal()">
            <i class="fas fa-trash-alt"></i> Remove
          </button>
        </div>
      </div>
    </div>

    <div class="seal-modal-footer">
      <span style="font-size: 12px; color: #64748b;" id="sealActiveModeHint">
        Mode: <strong>Blank for Manual Stamping</strong>
      </span>
      <button type="button" class="seal-btn-primary" onclick="closeSealModal()">
        <i class="fas fa-check"></i> Apply &amp; Close
      </button>
    </div>
  </div>
</div>

<script>
  (function() {
    // Current Seal State
    var serverSealUrl = "{{ $sealImageUrl ?? '' }}";
    var currentSealUrl = localStorage.getItem('gtms_invoice_seal_url') || serverSealUrl;
    var currentSealMode = localStorage.getItem('gtms_invoice_seal_mode');

    // Default mode: If no image has been uploaded, default strictly to 'blank'!
    if (!currentSealMode) {
      currentSealMode = currentSealUrl ? 'digital' : 'blank';
    }

    window.openSealModal = function() {
      var modal = document.getElementById('sealModal');
      if (modal) {
        syncSealModalUI();
        modal.classList.add('show');
      }
    };

    window.closeSealModal = function() {
      var modal = document.getElementById('sealModal');
      if (modal) {
        modal.classList.remove('show');
      }
    };

    window.handleSealBackdropClick = function(event) {
      if (event.target && event.target.id === 'sealModal') {
        closeSealModal();
      }
    };

    window.selectSealMode = function(mode) {
      if (mode === 'digital' && !currentSealUrl) {
        // Show upload section and auto-trigger picker
        document.getElementById('sealUploadSection').style.display = 'block';
        document.getElementById('sealFileInput').click();
      }
      currentSealMode = mode;
      localStorage.setItem('gtms_invoice_seal_mode', mode);
      applySealToInvoices();
      syncSealModalUI();
    };

    window.handleSealFileSelected = function(event) {
      var file = event.target.files && event.target.files[0];
      if (!file) return;

      var formData = new FormData();
      formData.append('seal_image', file);
      formData.append('_token', '{{ csrf_token() }}');

      var loading = document.getElementById('sealUploadLoading');
      if (loading) loading.style.display = 'block';

      fetch('{{ route("accounts.invoices.seal.upload") }}', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (loading) loading.style.display = 'none';
        if (data.success && data.url) {
          currentSealUrl = data.url;
          currentSealMode = 'digital';
          localStorage.setItem('gtms_invoice_seal_url', data.url);
          localStorage.setItem('gtms_invoice_seal_mode', 'digital');
          applySealToInvoices();
          syncSealModalUI();
        } else {
          alert(data.message || 'Error uploading seal image.');
        }
      })
      .catch(function(err) {
        if (loading) loading.style.display = 'none';
        // Client-side fallback via FileReader if offline
        var reader = new FileReader();
        reader.onload = function(e) {
          currentSealUrl = e.target.result;
          currentSealMode = 'digital';
          localStorage.setItem('gtms_invoice_seal_url', e.target.result);
          localStorage.setItem('gtms_invoice_seal_mode', 'digital');
          applySealToInvoices();
          syncSealModalUI();
        };
        reader.readAsDataURL(file);
      });
    };

    window.deleteCustomSeal = function() {
      if (!confirm('Are you sure you want to remove the uploaded seal? Invoices will revert to blank empty white space for manual physical stamping.')) {
        return;
      }

      fetch('{{ route("accounts.invoices.seal.remove") }}', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      }).catch(function() {});

      currentSealUrl = '';
      currentSealMode = 'blank';
      localStorage.removeItem('gtms_invoice_seal_url');
      localStorage.setItem('gtms_invoice_seal_mode', 'blank');
      applySealToInvoices();
      syncSealModalUI();
    };

    function syncSealModalUI() {
      var radioBlank = document.getElementById('radio-mode-blank');
      var radioDigital = document.getElementById('radio-mode-digital');
      var cardBlank = document.getElementById('card-mode-blank');
      var cardDigital = document.getElementById('card-mode-digital');
      var uploadSec = document.getElementById('sealUploadSection');
      var previewBox = document.getElementById('sealPreviewContainer');
      var previewImg = document.getElementById('sealPreviewImg');
      var hint = document.getElementById('sealActiveModeHint');

      if (currentSealMode === 'blank' || !currentSealUrl) {
        if (radioBlank) radioBlank.checked = true;
        if (cardBlank) cardBlank.classList.add('selected');
        if (cardDigital) cardDigital.classList.remove('selected');
        if (hint) hint.innerHTML = 'Mode: <strong>Blank for Manual Stamping &amp; Signing</strong>';
      } else {
        if (radioDigital) radioDigital.checked = true;
        if (cardDigital) cardDigital.classList.add('selected');
        if (cardBlank) cardBlank.classList.remove('selected');
        if (hint) hint.innerHTML = 'Mode: <strong style="color:#059669;">Digital Seal Attached</strong>';
      }

      // Show upload section always if digital, or if preview exists
      if (uploadSec) {
        uploadSec.style.display = (currentSealMode === 'digital' || currentSealUrl) ? 'block' : 'block';
      }

      if (currentSealUrl) {
        if (previewBox) previewBox.style.display = 'flex';
        if (previewImg) previewImg.src = currentSealUrl;
      } else {
        if (previewBox) previewBox.style.display = 'none';
      }
    }

    function applySealToInvoices() {
      var slots = document.querySelectorAll('.invoice-seal-slot');
      slots.forEach(function(slot) {
        var img = slot.querySelector('.invoice-seal-img');
        var space = slot.querySelector('.seal-empty-space');

        if (currentSealMode === 'digital' && currentSealUrl) {
          if (img) {
            img.src = currentSealUrl;
            img.style.display = 'block';
          }
          if (space) {
            space.style.display = 'none';
          }
        } else {
          // BLANK MODE (Default & Empty White Space)
          if (img) {
            img.style.display = 'none';
          }
          if (space) {
            space.style.display = 'block';
          }
        }
      });

      // Update toolbar action button badge
      var btn = document.getElementById('sealActionBtn');
      var label = document.getElementById('sealStatusLabel');
      if (btn && label) {
        if (currentSealMode === 'digital' && currentSealUrl) {
          btn.classList.remove('blank-mode');
          label.textContent = 'Seal: Digital Attached';
        } else {
          btn.classList.add('blank-mode');
          label.textContent = 'Seal: Blank (Manual)';
        }
      }
    }

    // Initialize immediately on DOM load
    document.addEventListener('DOMContentLoaded', function() {
      applySealToInvoices();
    });

    // Also apply right away in case DOM is already ready
    applySealToInvoices();
  })();
</script>
