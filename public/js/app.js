// Sidebar toggle (mobile)
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('sidebarToggle');
  var sidebar = document.getElementById('sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function () { sidebar.classList.toggle('open'); });
  }

  // ---- Wizard step navigation (new-application.html) ----
  var wizardPanes = document.querySelectorAll('.wizard-pane');
  if (wizardPanes.length) {
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
