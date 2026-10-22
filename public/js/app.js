// Sidebar toggle (mobile)
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('sidebarToggle');
  var sidebar = document.getElementById('sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function () { sidebar.classList.toggle('open'); });
  }

  // ---- Wizard step navigation (new-application.html) ----
  var wizardPanes = document.querySelectorAll('.wizard-pane');
  if (wizardPanes.length && !document.getElementById('gtms-mining-page')) {
    var steps = document.querySelectorAll('.flow-step');
    var connectors = document.querySelectorAll('.flow-connector');
    var current = 0;

        function render() {
      wizardPanes.forEach(function (p, i) { p.classList.toggle('d-none', i !== current); });
      steps.forEach(function (s, i) {
        s.classList.remove('active', 'done');
        if (i < current) s.classList.add('done');
        else if (i === current) s.classList.add('active');
      });
      connectors.forEach(function (c, i) { c.classList.toggle('done', i < current); });
      document.querySelectorAll('.btn-prev').forEach(function (b) { b.disabled = current === 0; });
      document.querySelectorAll('.step-counter').forEach(function (el) {
        el.textContent = 'Step ' + (current + 1) + ' of ' + wizardPanes.length;
      });
      document.querySelectorAll('.btn-next').forEach(function(btn) {
        if (current === wizardPanes.length - 1) {
          btn.innerHTML = 'Submit <i class="bi bi-check-lg ms-1"></i>';
        } else {
          btn.innerHTML = 'Continue <i class="bi bi-arrow-right ms-1"></i>';
        }
      });
    }
    document.querySelectorAll('.btn-next').forEach(function (b) {
      b.addEventListener('click', function () {
        if (current < wizardPanes.length - 1) { current++; render(); window.scrollTo({top:0,behavior:'smooth'}); }
      });
    });
    document.querySelectorAll('.btn-prev').forEach(function (b) {
      b.addEventListener('click', function () {
        if (current > 0) { current--; render(); window.scrollTo({top:0,behavior:'smooth'}); }
      });
    });
    steps.forEach(function (s, i) {
      s.style.cursor = 'pointer';
      s.addEventListener('click', function () { if (i <= current || s.classList.contains('done')) { current = i; render(); } });
    });
    render();
  }

  // ---- Folder tab switching (documents.html) ----
  var tabs = document.querySelectorAll('.folder-tab');
  if (tabs.length) {
    tabs.forEach(function (t) {
      t.addEventListener('click', function () {
        tabs.forEach(function (o) { o.classList.remove('active'); });
        t.classList.add('active');
        var target = t.getAttribute('data-target');
        document.querySelectorAll('.folder-panel').forEach(function (p) {
          p.classList.toggle('d-none', p.id !== target);
        });
        var titleEl = document.getElementById('activeFolderTitle');
        if (titleEl) titleEl.textContent = t.getAttribute('data-name');
      });
    });
  }

  // Fake upload interaction feedback
  document.querySelectorAll('.dropzone').forEach(function (dz) {
    dz.addEventListener('click', function () {
      dz.querySelector('.dz-status') && (dz.querySelector('.dz-status').textContent = 'No file chosen yet — this is a UI preview.');
    });
  });
});

/* ==========================================================================
   GTMS Universal Single-Tooltip & Focus-Sticky Prevention Manager
   Ensures:
   1. Bootstrap tooltips trigger ONLY on 'hover' (never stick on click/focus).
   2. Mutual exclusivity: When one tooltip opens, ALL others close immediately.
   3. Instant dismissal on click & blur: Clicking any button/icon immediately
      hides its tooltip and removes focus.
   4. Instant dismissal on mouseleave, touch, modal open, or dropdown open.
   5. Zero tooltip collisions across all GTMS pages.
   ========================================================================== */
(function() {
  // Set Bootstrap Tooltip default trigger to 'hover' globally
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip && bootstrap.Tooltip.Default) {
    bootstrap.Tooltip.Default.trigger = 'hover';
    bootstrap.Tooltip.Default.delay = { show: 120, hide: 40 };
  }

  // Helper to safely hide a tooltip
  function safeHideTooltip(el) {
    if (!el || typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    try {
      var inst = bootstrap.Tooltip.getInstance(el);
      if (inst) {
        inst.hide();
      }
    } catch (e) {}
  }

  // Helper to purge all active tooltips across DOM
  function purgeAllTooltips(exceptEl) {
    var activeTooltips = document.querySelectorAll('.tooltip.show');
    activeTooltips.forEach(function(tt) {
      var triggerEl = document.querySelector('[aria-describedby="' + tt.id + '"]');
      if (triggerEl && triggerEl !== exceptEl) {
        safeHideTooltip(triggerEl);
      } else if (!triggerEl) {
        tt.remove();
      }
    });
  }

  // 1. Mutual Exclusivity: Whenever ANY tooltip starts to show, close all others immediately
  document.addEventListener('show.bs.tooltip', function (event) {
    purgeAllTooltips(event.target);
  }, true);

  // 2. Click Dismissal & Blur: When user clicks any tool/button/icon, hide tooltip & drop focus
  document.addEventListener('click', function (event) {
    var btn = event.target.closest('[data-bs-toggle="tooltip"], .btn-dossier-tool, .btn-action-icon, .btn-dock-icon, .btn-copy-val, .btn-copy-chip, button, a');
    if (btn) {
      safeHideTooltip(btn);
      var tag = btn.tagName.toLowerCase();
      if (tag !== 'input' && tag !== 'textarea' && tag !== 'select' && typeof btn.blur === 'function') {
        btn.blur();
      }
    }

    // Sweep any lingering visible tooltips from screen
    setTimeout(function() {
      purgeAllTooltips();
    }, 50);
  }, true);

  // 3. Mouseleave Dismissal: When cursor exits a tooltip target, force immediate hide
  document.addEventListener('mouseleave', function (event) {
    var target = event.target;
    if (target && target.nodeType === 1) {
      var trigger = target.hasAttribute && target.hasAttribute('data-bs-toggle') ? target : (target.closest ? target.closest('[data-bs-toggle="tooltip"]') : null);
      if (trigger) {
        safeHideTooltip(trigger);
      }
    }
  }, true);

  // 4. Modal / Offcanvas / Dropdown Dismissal: Hide tooltips when dialogs open
  document.addEventListener('show.bs.modal', function () { purgeAllTooltips(); }, true);
  document.addEventListener('show.bs.dropdown', function () { purgeAllTooltips(); }, true);
  document.addEventListener('show.bs.offcanvas', function () { purgeAllTooltips(); }, true);

  // 5. Scroll & Escape Dismissal
  window.addEventListener('scroll', function () {
    if (document.querySelector('.tooltip.show')) {
      purgeAllTooltips();
    }
  }, { passive: true });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      purgeAllTooltips();
    }
  });

  // 6. Global automatic initializer for dynamically rendered tooltips
  function initGlobalTooltips(root) {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    var container = root || document;
    var list = [].slice.call(container.querySelectorAll('[data-bs-toggle="tooltip"]:not([data-tooltip-ready="true"])'));
    list.forEach(function (el) {
      el.setAttribute('data-tooltip-ready', 'true');
      bootstrap.Tooltip.getOrCreateInstance(el, {
        trigger: 'hover',
        delay: { show: 120, hide: 40 },
        boundary: document.body,
        fallbackPlacements: ['right', 'bottom', 'top', 'left']
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { initGlobalTooltips(); });
  } else {
    initGlobalTooltips();
  }
  window.initGlobalTooltips = initGlobalTooltips;
})();

