<!DOCTYPE html>
<html lang="en" class="theme-preload">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ $shopName }}</title>
    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = stored ? stored === 'dark' : prefersDark;
            const root = document.documentElement;
            root.classList.toggle('dark', isDark);
            root.style.colorScheme = isDark ? 'dark' : 'light';
            root.dataset.theme = isDark ? 'dark' : 'light';
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['Inter', 'sans-serif'],
                    mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
                },
                fontWeight: { 400:'400', 500:'500', 600:'600', 700:'700' },
                colors: {
                    // ONE muted primary — steel blue. Used only for primary actions & active nav.
                    brand: { 50:'#eef2f8',100:'#d9e2ef',200:'#b6c7de',300:'#8aa4c6',400:'#5f80a8',500:'#3f608a',600:'#324c6e',700:'#2b3f59',800:'#26364b',900:'#222f40' },
                    // Neutral surface scale for the dark sidebar / dark mode (true slate, no blue cast)
                    ink: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' },
                },
                borderRadius: { DEFAULT:'6px', md:'6px', lg:'8px', xl:'10px', '2xl':'12px' },
                boxShadow: {
                    'card': '0 1px 2px rgba(15,23,42,.04)',
                    'pop':  '0 4px 12px rgba(15,23,42,.08), 0 1px 3px rgba(15,23,42,.06)',
                },
                transitionDuration: { DEFAULT:'150ms' },
                keyframes: { fade: { '0%':{opacity:0,transform:'translateY(4px)'}, '100%':{opacity:1,transform:'translateY(0)'} } },
                animation: { fade: 'fade .15s ease both' },
            }}
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak]{display:none!important}
        html{ background:#f8fafc; color-scheme:light; }
        html.dark{ background:#020617; color-scheme:dark; }
        html.theme-preload *, html.theme-preload *::before, html.theme-preload *::after{ transition:none!important; }
        html{ font-feature-settings:'cv11','ss01'; }
        body{ -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
        /* tabular figures for all quantities, prices, SKUs so columns align */
        .tnum,table td,table th,.font-mono{ font-variant-numeric:tabular-nums lining-nums; }
        ::selection{ background:rgba(63,96,138,.18); }
        ::-webkit-scrollbar{width:10px;height:10px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:8px;border:2px solid transparent;background-clip:padding-box}
        ::-webkit-scrollbar-thumb:hover{background:#94a3b8;background-clip:padding-box}
        .dark ::-webkit-scrollbar-thumb{background:#334155;background-clip:padding-box}
        .glass{background:rgba(255,255,255,.85);backdrop-filter:blur(8px)}
        .dark .glass{background:rgba(15,23,42,.85)}
        a,button{ -webkit-tap-highlight-color:transparent; }
        [data-lucide]{ width:1em; height:1em; stroke-width:2; }
        #spa-progress{ transform-origin:left center; transition:opacity .2s ease, transform .25s ease; }
        body[data-spa-loading="true"] #spa-main{ opacity:.72; transition:opacity .15s ease; }
    </style>
</head>
<body x-data="{ dark: document.documentElement.classList.contains('dark') }"
      x-init="$watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v); document.documentElement.style.colorScheme = v ? 'dark' : 'light'; document.documentElement.dataset.theme = v ? 'dark' : 'light'; }); $nextTick(() => document.documentElement.classList.remove('theme-preload'))"
      class="font-sans bg-slate-50 dark:bg-ink-950 text-slate-700 dark:text-slate-300 antialiased text-[14px] leading-normal">
<div id="spa-progress" class="fixed inset-x-0 top-0 z-[80] h-0.5 bg-brand-600 opacity-0 scale-x-0 pointer-events-none"></div>
<div id="spa-shell" x-data="{ sidebar: window.innerWidth >= 1024 }" class="min-h-screen lg:flex">

    @include('layouts.sidebar')

    <!-- Main -->
    <div class="flex-1 min-w-0 flex flex-col">
        @include('layouts.topbar')

        <main id="spa-main" class="flex-1 p-4 sm:p-6 max-w-[1600px] w-full mx-auto animate-fade">
            @if (session('success'))
                <div x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,4000)"
                     class="mb-4 flex items-center gap-2.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 px-3.5 py-2.5 text-sm text-emerald-700 dark:text-emerald-300">
                    <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div x-data="{show:true}" x-show="show" x-transition x-init="setTimeout(()=>show=false,5000)"
                     class="mb-4 flex items-center gap-2.5 rounded-md bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 px-3.5 py-2.5 text-sm text-rose-700 dark:text-rose-300">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 px-3.5 py-2.5 text-rose-700 dark:text-rose-300">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
        <footer class="px-6 py-4 text-center text-xs text-slate-400 dark:text-slate-600 border-t border-slate-200/70 dark:border-slate-800">
            {{ $shopName }} · Inventory Management System &copy; {{ date('Y') }}
        </footer>
    </div>
</div>
<script>
    (() => {
        const shellSelector = '#spa-shell';
        const scriptsSelector = '#page-scripts script';
        const titleSelector = '[data-spa-page-title]';
        const cacheTtlMs = 15000;
        const cache = new Map();
        const activePrefetches = new Set();
        let navigationToken = 0;

        const renderIcons = () => window.lucide && window.lucide.createIcons();
        const initAlpine = (root = document.querySelector(shellSelector)) => {
            if (window.Alpine && typeof window.Alpine.initTree === 'function' && root) {
                window.Alpine.initTree(root);
            }
        };
        const setLoading = (isLoading) => {
            const progress = document.getElementById('spa-progress');
            document.body.dataset.spaLoading = isLoading ? 'true' : 'false';
            if (!progress) return;

            if (isLoading) {
                progress.classList.remove('opacity-0', 'scale-x-0');
                progress.classList.add('opacity-100');
                progress.style.transform = 'scaleX(.7)';
            } else {
                progress.style.transform = 'scaleX(1)';
                window.setTimeout(() => {
                    progress.classList.add('opacity-0', 'scale-x-0');
                    progress.classList.remove('opacity-100');
                    progress.style.transform = '';
                }, 120);
            }
        };
        const normalizeUrl = (url) => {
            const next = new URL(url.href);
            next.hash = '';
            return next;
        };
        const sameOrigin = (url) => url.origin === window.location.origin;
        const buildFetchOptions = () => ({
            headers: {
                'Accept': 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
                'X-SPA-Request': '1',
            },
        });
        const extractShell = (html) => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const pageTitle = doc.querySelector(titleSelector)?.textContent?.trim();
            return {
                doc,
                shell: doc.querySelector(shellSelector),
                title: doc.title || document.title,
                pageTitle,
            };
        };
        const runPageScripts = (doc) => {
            doc.querySelectorAll(scriptsSelector).forEach((script) => {
                const next = document.createElement('script');
                for (const attr of script.attributes) {
                    next.setAttribute(attr.name, attr.value);
                }
                if (!next.src) {
                    next.textContent = script.textContent;
                }
                document.body.appendChild(next);
                if (!next.src) {
                    next.remove();
                }
            });
        };
        const syncTitle = (title, pageTitle) => {
            document.title = title;
            const titleNode = document.querySelector(titleSelector);
            if (titleNode) {
                titleNode.textContent = pageTitle || title.split(/\s+[·]\s+/)[0].trim();
            }
        };
        const scrollToTop = () => window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
        const getCachedHtml = (key) => {
            const entry = cache.get(key);
            if (!entry) return null;
            if (Date.now() - entry.ts > cacheTtlMs) {
                cache.delete(key);
                return null;
            }
            return entry.html;
        };
        const setCachedHtml = (key, html) => {
            cache.set(key, { html, ts: Date.now() });
        };
        const loadHtml = async (url) => {
            const key = url.href;
            const cachedHtml = getCachedHtml(key);
            if (cachedHtml) {
                return cachedHtml;
            }

            const response = await fetch(key, buildFetchOptions());
            const html = await response.text();
            if (response.ok) {
                setCachedHtml(key, html);
            }
            return html;
        };
        const swapTo = async (url, pushState = true) => {
            const currentToken = ++navigationToken;
            setLoading(true);

            try {
                const html = await loadHtml(url);
                if (currentToken !== navigationToken) return;

                const { doc, shell, title, pageTitle } = extractShell(html);

                if (!shell) {
                    window.location.assign(url.href);
                    return;
                }

                const currentShell = document.querySelector(shellSelector);
                if (currentShell) {
                    currentShell.replaceWith(shell);
                } else {
                    document.body.appendChild(shell);
                }

                syncTitle(title, pageTitle);
                runPageScripts(doc);
                renderIcons();
                initAlpine(shell);
                schedulePrefetch();
                scrollToTop();

                if (pushState) {
                    history.pushState({ url: url.href }, '', url.href);
                }
            } catch (error) {
                window.location.assign(url.href);
            } finally {
                if (currentToken === navigationToken) {
                    setLoading(false);
                }
            }
        };
        const shouldHandleLink = (anchor, event) => {
            if (!anchor || !anchor.getAttribute('href')) return false;
            if (anchor.hasAttribute('download') || anchor.target && anchor.target !== '_self') return false;
            if (anchor.dataset.spaIgnore !== undefined) return false;
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) return false;

            const href = anchor.getAttribute('href');
            if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return false;

            const url = new URL(href, window.location.href);
            if (!sameOrigin(url)) return false;
            if (normalizeUrl(url).href === normalizeUrl(new URL(window.location.href)).href) return false;
            return true;
        };
        const shouldHandleForm = (form) => {
            if (!form || (form.method || 'get').toLowerCase() !== 'get') return false;
            if (form.dataset.spaIgnore !== undefined) return false;
            return true;
        };
        const prefetch = async (href) => {
            const normalized = normalizeUrl(new URL(href, window.location.href));
            try {
                if (!sameOrigin(normalized) || getCachedHtml(normalized.href) || activePrefetches.has(normalized.href)) return;

                activePrefetches.add(normalized.href);
                const response = await fetch(normalized.href, buildFetchOptions());
                const html = await response.text();
                if (response.ok) {
                    setCachedHtml(normalized.href, html);
                }
            } catch (error) {
                // Prefetch is opportunistic only.
            } finally {
                activePrefetches.delete(normalized.href);
            }
        };

        document.addEventListener('click', (event) => {
            const anchor = event.target.closest('a');
            if (!shouldHandleLink(anchor, event)) return;

            event.preventDefault();
            if (window.innerWidth < 1024) {
                const shell = document.querySelector(shellSelector);
                if (shell && window.Alpine && typeof window.Alpine.$data === 'function') {
                    const data = window.Alpine.$data(shell);
                    if (data && Object.prototype.hasOwnProperty.call(data, 'sidebar')) {
                        data.sidebar = false;
                    }
                }
            }
            swapTo(normalizeUrl(new URL(anchor.getAttribute('href'), window.location.href)));
        });

        document.addEventListener('submit', (event) => {
            const form = event.target.closest('form');
            if (!shouldHandleForm(form)) return;

            event.preventDefault();
            const action = new URL(form.getAttribute('action') || window.location.href, window.location.href);
            const data = new FormData(form);
            const params = new URLSearchParams(data);
            action.search = params.toString();
            swapTo(normalizeUrl(action));
        });

        window.addEventListener('popstate', () => {
            swapTo(normalizeUrl(new URL(window.location.href)), false);
        });

        const prefetchVisibleShellLinks = () => {
            document.querySelectorAll('a[data-spa-prefetch]').forEach((anchor) => {
                if (!shouldHandleLink(anchor, { metaKey: false, ctrlKey: false, shiftKey: false, altKey: false, button: 0 })) return;
                prefetch(anchor.getAttribute('href'));
            });
        };

        const schedulePrefetch = () => {
            if ('requestIdleCallback' in window) {
                window.requestIdleCallback(prefetchVisibleShellLinks, { timeout: 1500 });
            } else {
                window.setTimeout(prefetchVisibleShellLinks, 300);
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            renderIcons();
            initAlpine();
            schedulePrefetch();
            document.body.dataset.spaLoading = 'false';
        });
        document.addEventListener('alpine:initialized', renderIcons);
    })();
</script>
<div id="page-scripts" hidden>
    @stack('scripts')
</div>
</body>
</html>
