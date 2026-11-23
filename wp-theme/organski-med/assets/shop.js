/* Filter kategorija na početnoj i količina na stranici Proizvoda */
(() => {
  const filters = document.querySelector('.filters');
  const grid = document.getElementById('shop');
  if (filters && grid) {
    filters.addEventListener('click', e => {
      const b = e.target.closest('button');
      if (!b) return;
      filters.querySelectorAll('button').forEach(x => { x.classList.remove('active'); x.setAttribute('aria-pressed', 'false'); });
      b.classList.add('active');
      b.setAttribute('aria-pressed', 'true');
      const f = b.dataset.f;
      grid.querySelectorAll('.card').forEach(c => { c.hidden = !(f === 'all' || c.dataset.cat === f); });
    });
  }

  const qty = document.querySelector('.qty');
  if (qty) {
    const out = document.getElementById('q'), input = document.getElementById('qty-input');
    let q = 1;
    qty.addEventListener('click', e => {
      const b = e.target.closest('button');
      if (!b) return;
      q = Math.max(1, Math.min(20, q + +b.dataset.d));
      out.textContent = q;
      input.value = q;
    });
  }
})();
