/* Public site: stagger reveals, count-up numbers, cursor light on cards, header shadow. */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  // 1. Stagger siblings that reveal together (grids of cards, stats...)
  document.querySelectorAll('.reveal').forEach((el) => {
    const group = [...el.parentElement.children].filter((c) => c.classList.contains('reveal'));
    if (group.length > 1) el.style.setProperty('--d', group.indexOf(el) * 90 + 'ms');
  });

  // 2. Hero entrance: first reveal blocks in the hero fly in on load
  document.querySelectorAll('section:first-of-type .reveal').forEach((el, i) => {
    el.classList.remove('reveal');
    el.classList.add('hero-in');
    el.style.setProperty('--d', 120 + i * 160 + 'ms');
  });
  document.querySelectorAll('.blur-3xl').forEach((el) => { el.style.filter = 'none'; el.style.opacity = '.35'; });

  // 3. Count-up numbers like "15+", "30k+", "4.9/5", "98%"
  const run = (el) => {
    const m = el.textContent.trim().match(/^(\d+(?:\.\d+)?)(.*)$/);
    if (!m || reduce) return;
    const end = parseFloat(m[1]), dec = (m[1].split('.')[1] || '').length, tail = m[2], t0 = performance.now(), dur = 1400;
    const tick = (t) => {
      const k = Math.min(1, (t - t0) / dur), v = end * (1 - Math.pow(1 - k, 3));
      el.textContent = v.toFixed(dec) + tail;
      if (k < 1) requestAnimationFrame(tick);
    };
    el.textContent = (0).toFixed(dec) + tail;
    requestAnimationFrame(tick);
  };
  const io = new IntersectionObserver((entries) => entries.forEach((e) => {
    if (e.isIntersecting) { run(e.target); io.unobserve(e.target); }
  }), { threshold: 0.6 });
  document.querySelectorAll('.text-3xl.font-extrabold, .text-4xl.font-extrabold').forEach((el) => {
    if (/^\d/.test(el.textContent.trim())) io.observe(el);
  });

  // 4. Cursor light on cards
  let pc = null, pe = null, pt = false;
  document.addEventListener('pointermove', (e) => {
    pc = e.target.closest && e.target.closest('article.reveal, figure.reveal'); pe = e;
    if (!pc || pt) return; pt = true;
    requestAnimationFrame(() => { pt = false; if (!pc) return; const r = pc.getBoundingClientRect(); pc.style.setProperty('--mx', pe.clientX - r.left + 'px'); pc.style.setProperty('--my', pe.clientY - r.top + 'px'); });
  }, { passive: true });

  // 5. Header shadow after scrolling
  const header = document.getElementById('site-header');
  const onScroll = () => header && header.classList.toggle('scrolled', scrollY > 8);
  addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // 6. Fast page changes: show a progress bar and prefetch links on hover
  const bar = document.createElement('div');
  bar.id = 'topbar-progress';
  document.body.appendChild(bar);
  addEventListener('beforeunload', () => { bar.style.opacity = 1; bar.style.width = '70%'; });
  const seen = new Set();
  const prefetch = (a) => {
    const u = a.href;
    if (!u || seen.has(u) || a.origin !== location.origin || u.split('#')[0] === location.href.split('#')[0]) return;
    seen.add(u);
    const l = document.createElement('link');
    l.rel = 'prefetch'; l.href = u; l.as = 'document';
    document.head.appendChild(l);
  };
  document.addEventListener('pointerover', (e) => { const a = e.target.closest && e.target.closest('a[href]'); if (a) prefetch(a); }, { passive: true });
  document.addEventListener('touchstart', (e) => { const a = e.target.closest && e.target.closest('a[href]'); if (a) prefetch(a); }, { passive: true });
})();
