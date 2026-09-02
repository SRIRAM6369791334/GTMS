document.addEventListener('DOMContentLoaded', () => {

  /* ---------- Mobile sidebar toggle ---------- */
  const sidebar = document.querySelector('.admin-sidebar');
  const backdrop = document.querySelector('.sidebar-backdrop');
  document.querySelectorAll('.sidebar-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      backdrop.classList.toggle('show');
    });
  });
  if (backdrop) backdrop.addEventListener('click', () => {
    sidebar.classList.remove('open');
    backdrop.classList.remove('show');
  });

  /* ---------- Folder tab switching ---------- */
  const tabs = document.querySelectorAll('.admin-tab');
  const tabPanels = document.querySelectorAll('.tab-panel');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const target = tab.getAttribute('data-target');
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      tabPanels.forEach(p => p.style.display = (p.id === target) ? 'block' : 'none');
    });
  });

  /* ---------- Status cycling on table rows (Pending -> Uploaded -> Approved) ---------- */
  const cycle = { pending: 'uploaded', uploaded: 'approved', approved: 'pending' };
  const labels = { pending: 'Pending', uploaded: 'Uploaded', approved: 'Approved' };
  const icons = { pending: 'bi-hourglass-split', uploaded: 'bi-cloud-check', approved: 'bi-check-circle' };

  document.querySelectorAll('.badge-status[data-cyclable]').forEach(badge => {
    badge.style.cursor = 'pointer';
    badge.addEventListener('click', () => {
      let state = badge.getAttribute('data-state') || 'pending';
      state = cycle[state] || 'pending';
      badge.setAttribute('data-state', state);
      badge.className = 'badge-status ' + state;
      badge.setAttribute('data-cyclable', '1');
      badge.innerHTML = `<i class="bi ${icons[state]}"></i> ${labels[state]}`;
      updatePanelStats(badge.closest('.tab-panel'));
    });
  });

  function updatePanelStats(panel) {
    if (!panel) return;
    const chip = panel.querySelector('.panel-progress-chip');
    if (!chip) return;
    const rows = panel.querySelectorAll('.badge-status[data-cyclable]');
    const approved = panel.querySelectorAll('.badge-status[data-state="approved"]').length;
    chip.textContent = `${approved} / ${rows.length} approved`;
  }
  document.querySelectorAll('.tab-panel').forEach(updatePanelStats);

  /* ---------- Mini process-flow stepper ---------- */
  document.querySelectorAll('.mini-stepper').forEach(stepper => {
    const steps = Array.from(stepper.querySelectorAll('.mini-step'));
    steps.forEach((step, idx) => {
      step.addEventListener('click', () => {
        steps.forEach((s, i) => {
          s.classList.remove('current', 'done');
          if (i < idx) s.classList.add('done');
          if (i === idx) s.classList.add('current');
        });
        const connectors = stepper.querySelectorAll('.mini-connector');
        connectors.forEach((c, i) => c.classList.toggle('done', i < idx));
      });
    });
  });

  /* ---------- Bootstrap tooltips / dropdowns already handled by bootstrap.bundle ---------- */
});
