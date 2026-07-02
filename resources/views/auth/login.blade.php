<!DOCTYPE html>
<html lang="en" class="h-full theme-preload">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in - Houseware Inventory</title>
    <script>
      (() => {
        try {
          const storedTheme = localStorage.getItem('theme');
          const isDark = storedTheme
            ? storedTheme === 'dark'
            : window.matchMedia('(prefers-color-scheme: dark)').matches;

          const root = document.documentElement;
          root.classList.toggle('dark', isDark);
          root.style.colorScheme = isDark ? 'dark' : 'light';
          root.dataset.theme = isDark ? 'dark' : 'light';
        } catch (error) {
          console.warn('Theme bootstrap failed', error);
        }
      })();
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
              display: ['Sora', 'Inter', 'sans-serif'],
            },
            fontWeight: { 400: '400', 500: '500', 600: '600', 700: '700', 800: '800' },
            colors: {
              ink: {
                900: '#0b1120',
                800: '#131c31',
                700: '#1c273f',
              },
              brand: {
                50:  '#eef2f8',
                100: '#d9e2ef',
                200: '#b6c7de',
                300: '#8aa4c6',
                400: '#5f80a8',
                500: '#3f608a',
                600: '#324c6e',
                700: '#2b3f59',
                800: '#26364b',
                900: '#222f40',
              },
            },
            keyframes: {
              rise: { '0%': { opacity: 0, transform: 'translateY(12px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
            },
            animation: { rise: 'rise .5s cubic-bezier(.22,1,.36,1) both' },
          },
        },
      };
    </script>
    <style>
      html { background: #f8fafc; color-scheme: light; }
      html.dark { background: #020617; color-scheme: dark; }
      html.theme-preload *, html.theme-preload *::before, html.theme-preload *::after {
        transition: none !important;
      }
      .grid-bg {
        background-image:
          linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
          linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
        background-size: 44px 44px;
      }
      html.dark .grid-bg {
        background-image:
          linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
          linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
      }
      input:-webkit-autofill { -webkit-box-shadow: 0 0 0 30px #ffffff inset; }
    </style>
</head>
<body
  x-data="{ dark: document.documentElement.classList.contains('dark') }"
  x-init="$watch('dark', value => {
      const root = document.documentElement;
      localStorage.setItem('theme', value ? 'dark' : 'light');
      root.classList.toggle('dark', value);
      root.style.colorScheme = value ? 'dark' : 'light';
      root.dataset.theme = value ? 'dark' : 'light';
      $nextTick(() => root.classList.remove('theme-preload'));
    });
    $nextTick(() => document.documentElement.classList.remove('theme-preload'));"
  class="h-full font-sans antialiased text-slate-800 bg-slate-100 dark:text-slate-200 dark:bg-slate-950">
  <div class="min-h-full lg:grid lg:grid-cols-[1.05fr_1fr]">

    <!-- Brand panel -->
    <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-ink-900 text-white p-12 xl:p-16">
      <div class="absolute inset-0 grid-bg"></div>
      <div class="absolute -top-32 -left-24 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
      <div class="absolute -bottom-40 right-0 h-[28rem] w-[28rem] rounded-full bg-brand-700/20 blur-3xl"></div>

      <!-- Logo -->
      <div class="relative flex items-center gap-3">
        <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 shadow-lg shadow-brand-500/30">
          <svg class="h-5 w-5 text-ink-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-6 9 6v10a1 1 0 0 1-1 1h-4v-7H8v7H4a1 1 0 0 1-1-1z"/>
          </svg>
        </div>
        <span class="font-display text-lg font-700 tracking-tight">Houseware<span class="text-brand-400">.</span></span>
      </div>

      <!-- Headline -->
      <div class="relative max-w-md">
        <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-brand-300">
          <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
          Inventory - Sales - Reports
        </p>
        <h1 class="font-display text-[2.6rem] font-800 leading-[1.05] tracking-tight">
          Every shelf,<br>under control.
        </h1>
        <p class="mt-5 text-[15px] leading-relaxed text-slate-400">
          Track stock the moment it moves, ring up sales without friction, and close the day with numbers you can trust.
        </p>

        <dl class="mt-10 grid grid-cols-3 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/5 text-center">
          <div class="px-4 py-5">
            <dt class="font-display text-2xl font-700 text-white">2.4k+</dt>
            <dd class="mt-1 text-xs text-slate-400">SKUs tracked</dd>
          </div>
          <div class="px-4 py-5">
            <dt class="font-display text-2xl font-700 text-white">99.9%</dt>
            <dd class="mt-1 text-xs text-slate-400">Uptime</dd>
          </div>
          <div class="px-4 py-5">
            <dt class="font-display text-2xl font-700 text-white">&lt;1s</dt>
            <dd class="mt-1 text-xs text-slate-400">Checkout</dd>
          </div>
        </dl>
      </div>

      <p class="relative text-xs text-slate-500">&copy; {{ date('Y') }} Houseware Inventory System</p>
    </aside>

    <!-- Form panel -->
    <main class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
      <div class="w-full max-w-sm animate-rise">

        <!-- Mobile logo -->
        <div class="mb-10 flex items-center gap-3 lg:hidden">
          <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-brand-400 to-brand-600">
            <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9l9-6 9 6v10a1 1 0 0 1-1 1h-4v-7H8v7H4a1 1 0 0 1-1-1z"/>
            </svg>
          </div>
          <span class="font-display text-lg font-700 tracking-tight">Houseware<span class="text-brand-600">.</span></span>
        </div>

        <h2 class="font-display text-[1.7rem] font-700 tracking-tight text-slate-900 dark:text-slate-50">Sign in</h2>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Welcome back - let's get you to your dashboard.</p>

        @if ($errors->any())
          <div class="mt-6 flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
            <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></svg>
            <span>{{ $errors->first() }}</span>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
          @csrf

          <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Email address</label>
            <div class="group relative">
              <svg class="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400 transition group-focus-within:text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
              <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                     autocomplete="username" placeholder="you@houseware.test"
                     class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-11 pr-3.5 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/15 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500">
            </div>
          </div>

          <div>
            <div class="mb-1.5 flex items-center justify-between">
              <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
              <a href="#" class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">Forgot?</a>
            </div>
            <div class="group relative">
              <svg class="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400 transition group-focus-within:text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
              <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="password"
                     class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-11 pr-11 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/15 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500">
              <button type="button" onclick="(()=>{const i=document.getElementById('password');const o=i.type==='password';i.type=o?'text':'password';this.dataset.on=o})()"
                      aria-label="Toggle password visibility"
                      class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <label class="flex select-none items-center gap-2.5 text-sm text-slate-600 dark:text-slate-300">
            <input type="checkbox" name="remember"
                   class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30 dark:border-slate-600 dark:bg-slate-900">
            Keep me signed in
          </label>

          <button type="submit"
                  class="group flex w-full items-center justify-center gap-2 rounded-xl bg-ink-900 py-3 text-sm font-semibold text-white shadow-lg shadow-ink-900/20 transition hover:bg-ink-800 focus:outline-none focus:ring-4 focus:ring-ink-900/20 active:scale-[.99]">
            Sign in to dashboard
            <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
          </button>
        </form>

        <!-- Demo accounts -->
        <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
            <span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>
            Demo accounts
            <span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>
          </div>
          <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
            @foreach (['owner' => 'Owner', 'manager' => 'Manager', 'cashier' => 'Cashier', 'inventory' => 'Inventory'] as $user => $role)
              <button type="button"
                      onclick="document.getElementById('email').value='{{ $user }}@houseware.test';document.getElementById('password').value='password';"
                      class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-left font-medium text-slate-600 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700 dark:border-slate-800 dark:text-slate-300 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/10 dark:hover:text-brand-200">
                {{ $role }}
                <svg class="h-3.5 w-3.5 opacity-0 transition group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              </button>
            @endforeach
          </div>
          <p class="mt-3 text-center text-[11px] text-slate-400 dark:text-slate-500">Tap a role to autofill - password is <code class="font-semibold text-slate-500 dark:text-slate-300">password</code></p>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
