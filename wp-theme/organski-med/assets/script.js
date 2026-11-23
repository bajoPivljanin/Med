(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = (s, r = document) => [...r.querySelectorAll(s)];

  /* 1. Glatki skrol (Lenis) */
  let lenis = null;
  if (window.Lenis && !reduce) {
    lenis = new Lenis({ duration: 1.15, easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)) });
    const raf = t => { lenis.raf(t); requestAnimationFrame(raf); };
    requestAnimationFrame(raf);
  }
  document.addEventListener('click', e => {
    const a = e.target.closest('a[href^="#"]');
    if (!a || a.getAttribute('href') === '#') return;
    const t = document.querySelector(a.getAttribute('href'));
    if (!t) return;
    e.preventDefault();
    lenis ? lenis.scrollTo(t, { offset: -72 }) : t.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth' });
  });

  /* 2. Zavjesa pri učitavanju i prelasku na druge stranice */
  const curtain = document.querySelector('.curtain');
  window.addEventListener('load', () => setTimeout(() => {
    document.documentElement.classList.add('loaded');
  }, reduce ? 0 : 450));
  window.addEventListener('pageshow', e => { if (e.persisted) document.documentElement.classList.add('loaded'); });
  document.addEventListener('click', e => {
    const a = e.target.closest('a[href]');
    if (!a || !curtain || reduce) return;
    const u = new URL(a.href, location.href);
    if (u.origin !== location.origin || u.pathname === location.pathname || a.target === '_blank' || e.metaKey || e.ctrlKey) return;
    e.preventDefault();
    document.documentElement.classList.remove('loaded');
    setTimeout(() => (location.href = a.href), 650);
  });

  /* 3. Naslov u hero sekciji: riječ po riječ */
  const h1 = document.querySelector('.hero h1');
  if (h1 && !reduce) {
    let i = 0;
    const wrap = node => {
      [...node.childNodes].forEach(n => {
        if (n.nodeType === 3) {
          const frag = document.createDocumentFragment();
          n.textContent.split(/(\s+)/).forEach(w => {
            if (!w.trim()) { frag.append(w); return; }
            const o = document.createElement('span'); o.className = 'w';
            const s = document.createElement('span'); s.textContent = w;
            s.style.transitionDelay = (0.55 + i++ * 0.09) + 's';
            o.append(s); frag.append(o);
          });
          n.replaceWith(frag);
        } else if (n.nodeType === 1) wrap(n);
      });
    };
    wrap(h1);
  }

  /* 4. Otkrivanje elemenata pri skrolu (sa kašnjenjem) */
  const groups = [
    ['.hero .eyebrow, .hero .lead, .hero-actions', 0],
    ['.hero-visual', 0],
    ['.section-head', 0],
    ['.filters', 0],
    ['.benefit', 1], ['.step', 1], ['.quote', 1], ['.stats .container > div', 1],
    ['.about .container > *', 1], ['details', 1], ['.cta .container > *', 1], ['.foot-grid > div', 1]
  ];
  const io = new IntersectionObserver(es => es.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  }), { threshold: 0.15, rootMargin: '0px 0px -6% 0px' });
  const addReveal = (el, delay) => {
    if (el.classList.contains('reveal')) return;
    el.classList.add('reveal');
    el.style.transitionDelay = delay + 's';
    io.observe(el);
  };
  groups.forEach(([sel, stagger]) => $(sel).forEach((el, i) => addReveal(el, stagger ? (i % 4) * 0.12 : (sel.startsWith('.hero') ? 0.7 + i * 0.12 : 0))));

  /* proizvodi se crtaju JS-om: svaki put ih otkrij ponovo */
  const shop = document.getElementById('shop');
  if (shop) new MutationObserver(() => $('.card', shop).forEach((c, i) => {
    c.style.animationDelay = (i % 4) * 0.08 + 's';
  })).observe(shop, { childList: true });

  /* 5. Brojke u statistici */
  const counters = $('.stats b').map(el => {
    const m = el.textContent.match(/^(\d+)(.*)$/);
    return m ? { el, to: +m[1], suf: m[2] } : null;
  }).filter(Boolean);
  if (counters.length && !reduce) {
    counters.forEach(c => c.el.textContent = '0' + c.suf);
    const cio = new IntersectionObserver(es => es.forEach(e => {
      if (!e.isIntersecting) return;
      cio.disconnect();
      const t0 = performance.now(), D = 1600;
      const tick = t => {
        const p = Math.min(1, (t - t0) / D), k = 1 - Math.pow(1 - p, 3);
        counters.forEach(c => c.el.textContent = Math.round(c.to * k) + c.suf);
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    }), { threshold: 0.4 });
    cio.observe(document.querySelector('.stats'));
  }

  /* 6. Traka napretka, navigacija i paralaksa */
  const bar = document.querySelector('.progress');
  const nav = document.querySelector('.nav');
  const vis = document.querySelector('.hero-visual');
  const jar = document.querySelector('.hero-visual .jar');
  let lastY = 0;
  const onScroll = () => {
    const y = window.scrollY, max = document.documentElement.scrollHeight - innerHeight;
    if (bar) bar.style.transform = `scaleX(${max > 0 ? y / max : 0})`;
    if (nav) {
      nav.classList.toggle('scrolled', y > 40);
      nav.classList.toggle('hide', y > lastY && y > 400);
    }
    lastY = y;
    if (vis && !reduce && y < innerHeight * 1.2) {
      vis.style.setProperty('--py', (y * -0.06) + 'px');
      if (jar) jar.style.transform = `translateY(${y * 0.1}px) rotate(${y * 0.012}deg)`;
    }
  };
  addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* 7. Ističe se kartica na koju pokazujete, ostale se prigušuju */
  ['.benefit-grid', '.quotes', '.grid'].forEach(sel => {
    const wrap = document.querySelector(sel);
    if (!wrap) return;
    wrap.addEventListener('pointerover', e => {
      const c = e.target.closest(':scope > *');
      if (!c) return;
      wrap.classList.add('focusing');
      [...wrap.children].forEach(x => x.classList.toggle('is-focus', x === c));
    });
    wrap.addEventListener('pointerleave', () => {
      wrap.classList.remove('focusing');
      [...wrap.children].forEach(x => x.classList.remove('is-focus'));
    });
  });

  /* 8. Dugmad se blago privlače kursoru */
  if (!reduce && matchMedia('(hover:hover)').matches) {
    $('.btn-primary, .btn-outline').forEach(b => {
      b.addEventListener('pointermove', e => {
        const r = b.getBoundingClientRect();
        b.style.transform = `translate(${(e.clientX - r.left - r.width / 2) * 0.15}px,${(e.clientY - r.top - r.height / 2) * 0.25}px)`;
      });
      b.addEventListener('pointerleave', () => b.style.transform = '');
    });
  }
})();
