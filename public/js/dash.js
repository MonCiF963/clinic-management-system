/* Dashboard: hex background, spotlight, fast SPA-style navigation, count-up, stagger. */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];

  /* ---------- hex background (cheap: static grid cached once, only the glow near the cursor is redrawn) ---------- */
  const cv = $('#hexbg');
  const lowEnd = reduce || (navigator.hardwareConcurrency || 4) <= 2 || innerWidth < 700;
  let px = -999, py = -999, ticking = false;
  const spot = $('.spot');
  if (cv && !lowEnd) {
    const ctx = cv.getContext('2d'); let w, h, grid; const R = 34, dx = R * Math.sqrt(3), dy = R * 1.5, GLOW = 200;
    const path = (c, x, y) => { c.beginPath(); for (let i = 0; i < 6; i++) { const a = Math.PI / 3 * i + Math.PI / 6; c[i ? 'lineTo' : 'moveTo'](x + R * Math.cos(a), y + R * Math.sin(a)); } c.closePath(); };
    const build = () => {
      w = cv.width = innerWidth; h = cv.height = innerHeight;
      grid = document.createElement('canvas'); grid.width = w; grid.height = h;
      const g = grid.getContext('2d'); g.strokeStyle = 'rgba(45,212,191,.11)'; g.lineWidth = 1; g.beginPath();
      for (let r = 0, y = 0; y < h + R; r++, y += dy) for (let x = (r % 2 ? dx / 2 : 0); x < w + R; x += dx) { for (let i = 0; i < 6; i++) { const a = Math.PI / 3 * i + Math.PI / 6; g[i ? 'lineTo' : 'moveTo'](x + R * Math.cos(a), y + R * Math.sin(a)); } g.closePath(); }
      g.stroke(); ctx.drawImage(grid, 0, 0);
    };
    build(); let rt; addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(build, 200); });
    var drawGlow = () => {
      ctx.clearRect(0, 0, w, h); ctx.drawImage(grid, 0, 0);
      ctx.lineWidth = 1.5;
      for (let r = Math.max(0, Math.floor((py - GLOW) / dy)); r * dy < py + GLOW; r++) {
        const y = r * dy; for (let x = (r % 2 ? dx / 2 : 0); x < w + R; x += dx) {
          const d = Math.hypot(x - px, y - py); if (d > GLOW) continue;
          ctx.strokeStyle = `rgba(45,212,191,${(1 - d / GLOW) * 0.45})`; path(ctx, x, y); ctx.stroke();
        }
      }
    };
  }

  /* ---------- pointer effects: one rAF per frame, transform only ---------- */
  let cardEl = null, cx = 0, cy = 0;
  addEventListener('pointermove', (e) => {
    px = e.clientX; py = e.clientY;
    const c = e.target.closest && e.target.closest('.gcard'); cardEl = c || null;
    if (c) { const r = c.getBoundingClientRect(); cx = e.clientX - r.left; cy = e.clientY - r.top; }
    if (ticking) return; ticking = true;
    requestAnimationFrame(() => {
      ticking = false;
      if (spot) spot.style.transform = `translate3d(${px}px,${py}px,0)`;
      if (typeof drawGlow === 'function') drawGlow();
      if (cardEl) { cardEl.style.setProperty('--mx', cx + 'px'); cardEl.style.setProperty('--my', cy + 'px'); }
    });
  }, { passive: true });

  /* ---------- per-page effects ---------- */
  const countUp = (el) => {
    const raw = el.textContent.trim(), m = raw.match(/^([^\d-]*)([\d,]+(?:\.\d+)?)(.*)$/); if (!m || reduce) return;
    const end = parseFloat(m[2].replace(/,/g, '')), dec = (m[2].split('.')[1] || '').length, t0 = performance.now();
    const fmt = (v) => v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec });
    const tick = (t) => { const k = Math.min(1, (t - t0) / 1100); el.textContent = m[1] + fmt(end * (1 - Math.pow(1 - k, 3))) + m[3]; if (k < 1) requestAnimationFrame(tick); };
    requestAnimationFrame(tick);
  };
  const enhance = () => {
    const page = $('#page'); if (!page) return;
    $$('.shadow-sm, .shadow, .rounded-2xl.border, .rounded-xl.border', page).forEach((c) => { if (c.tagName !== 'INPUT' && c.tagName !== 'SELECT' && !c.closest('table')) c.classList.add('gcard'); });
    const items = [...new Set([...page.children, ...$$('.gcard', page)])];
    items.forEach((el, i) => { el.classList.remove('in'); void el.offsetWidth; el.style.setProperty('--d', Math.min(i, 14) * 60 + 'ms'); el.classList.add('in'); });
    $$('.stat-val', page).forEach(countUp);
    $$('[data-dismiss], .toast-flash', page).forEach((el) => setTimeout(() => { el.style.transition = 'opacity .5s'; el.style.opacity = 0; setTimeout(() => el.remove(), 500); }, 5000));
  };

  /* ---------- fast navigation (no full reloads) ---------- */
  const bar = $('#dash-progress'); let barT;
  const prog = (on) => { clearTimeout(barT); if (on) { bar.style.opacity = 1; bar.style.width = '70%'; } else { bar.style.width = '100%'; barT = setTimeout(() => { bar.style.opacity = 0; bar.style.width = 0; }, 250); } };
  const cache = new Map(); // url -> Promise<string>, short-lived prefetch cache
  const get = (url) => {
    if (!cache.has(url)) { cache.set(url, fetch(url, { headers: { 'X-Requested-With': 'fetch' }, credentials: 'same-origin' }).then((r) => r.ok || r.status === 422 ? r.text().then((t) => ({ t, url: r.url })) : Promise.reject(r))); setTimeout(() => cache.delete(url), 30000); }
    return cache.get(url);
  };
  const setActive = () => {
    const here = location.pathname;
    $$('[data-nav]').forEach((a) => { const p = new URL(a.href).pathname; a.classList.toggle('on', p === here || (p !== '/dashboard' && here.startsWith(p + '/'))); });
  };
  const swap = (html, finalUrl, push) => {
    const doc = new DOMParser().parseFromString(html, 'text/html'), next = $('#page', doc);
    if (!next || !doc.body.classList.contains('dash')) { location.href = finalUrl; return; }
    const page = $('#page');
    setTimeout(() => {
      page.innerHTML = next.innerHTML; page.classList.remove('leaving', 'busy');
      document.title = doc.title; const t = $('#page-title'), nt = $('#page-title', doc); if (t && nt) t.textContent = nt.textContent;
      if (push) history.pushState({ p: 1 }, '', finalUrl);
      scrollTo(0, 0); setActive(); enhance(); prog(false);
    }, 0);
  };
  const go = (url, push = true) => {
    prog(true);
    // instant feedback: highlight the clicked item and dim the page right away
    const u = new URL(url, location.href).pathname;
    $$('[data-nav]').forEach((a) => a.classList.toggle('on', new URL(a.href).pathname === u));
    const page = $('#page'); if (page) page.classList.add('busy');
    get(url).then(({ t, url: fu }) => swap(t, fu, push)).catch(() => { location.href = url; });
  };
  const local = (a) => a.origin === location.origin && !a.target && !a.hasAttribute('download') && !a.hasAttribute('data-full') && !a.getAttribute('href').startsWith('#');
  document.addEventListener('click', (e) => {
    const a = e.target.closest && e.target.closest('a[href]');
    if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button || !local(a) || a.pathname === '/' || !$('#page')) return;
    if (a.pathname === location.pathname && a.search === location.search) { e.preventDefault(); return; }
    e.preventDefault(); go(a.href);
  });
  const warm = (e) => { const a = e.target.closest && e.target.closest('a[href]'); if (a && local(a) && a.pathname !== '/' && a.href !== location.href) get(a.href).catch(() => cache.delete(a.href)); };
  document.addEventListener('pointerdown', warm, { passive: true });
  document.addEventListener('pointerover', warm, { passive: true });
  document.addEventListener('touchstart', warm, { passive: true });

  document.addEventListener('submit', async (e) => {
    const f = e.target; if (f.hasAttribute('data-full') || f.action.endsWith('/logout') || !$('#page')) return;
    e.preventDefault(); prog(true);
    const btn = f.querySelector('[type=submit]'); if (btn) { btn.disabled = true; btn.classList.add('btn-busy'); } $('#page').classList.add('busy');
    try {
      const method = (f.method || 'get').toUpperCase(), body = new FormData(f);
      let url = f.action, opt = { credentials: 'same-origin', headers: { 'X-Requested-With': 'fetch' } };
      if (method === 'GET') { url += (url.includes('?') ? '&' : '?') + new URLSearchParams(body).toString(); }
      else { opt.method = 'POST'; opt.body = body; }
      const r = await fetch(url, opt), t = await r.text();
      if (!r.ok && r.status !== 422) throw r;
      cache.clear(); swap(t, r.url, true);
    } catch (err) { f.setAttribute('data-full', ''); if (btn) btn.disabled = false; f.submit(); }
  });
  addEventListener('popstate', () => go(location.href, false));

  setActive(); enhance();
})();
