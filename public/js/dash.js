/* Dashboard: fast SPA-style navigation, count-up, staggered entrances, chart tooltip. */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];

  /* ---------- chart tooltip (like the OneCode+ dashboard) ---------- */
  document.addEventListener('pointerover', (e) => {
    const hit = e.target.closest && e.target.closest('.hit');
    const box = hit && hit.closest('.chartbox'); if (!box) return;
    const tip = $('.tip', box), dot = hit.parentNode.querySelector('.dotp'); if (!tip || !dot) return;
    const r = dot.getBoundingClientRect(), b = box.getBoundingClientRect();
    tip.innerHTML = hit.getAttribute('data-tip');
    tip.style.left = r.left - b.left + r.width / 2 + 'px'; tip.style.top = r.top - b.top + 'px';
    tip.classList.add('on');
  }, { passive: true });
  document.addEventListener('pointerout', (e) => {
    const hit = e.target.closest && e.target.closest('.hit'); if (!hit) return;
    const tip = $('.tip', hit.closest('.chartbox')); if (tip) tip.classList.remove('on');
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
  const closeNav = () => { document.body.classList.remove('nav-open'); const t = $('#nav-toggle'); if (t) { t.setAttribute('aria-expanded', 'false'); t.setAttribute('aria-label', 'Open menu'); } };
  const go = (url, push = true) => {
    prog(true); closeNav();
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

  /* phone drawer menu */
  const tgl = $('#nav-toggle');
  if (tgl) tgl.addEventListener('click', () => { const o = !document.body.classList.contains('nav-open'); document.body.classList.toggle('nav-open', o); tgl.setAttribute('aria-expanded', o ? 'true' : 'false'); tgl.setAttribute('aria-label', o ? 'Close menu' : 'Open menu'); });
  const bd = $('#nav-backdrop'); if (bd) bd.addEventListener('click', closeNav);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeNav(); });
  addEventListener('resize', () => { if (innerWidth >= 1024) closeNav(); });

  setActive(); enhance();
})();
