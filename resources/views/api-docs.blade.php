<!DOCTYPE html>
<html lang="en" x-data="apiDocs()" :class="{ 'dark': darkMode }" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Reference — MockDasher</title>
    <meta name="description" content="Complete REST API reference for the MockDasher IELTS mock test platform.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        indigo: {
                            50:  '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe',
                            300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1',
                            600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81',
                        },
                    },
                },
            },
        };
    </script>
    <style>
        :root { --sidebar-w: 260px; }
        body { font-family: 'Inter', sans-serif; }
        code, pre, .mono { font-family: 'JetBrains Mono', monospace; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 3px; }

        /* Sidebar nav active link */
        .nav-link.active { color: #4f46e5; font-weight: 600; background: #eef2ff; }
        .dark .nav-link.active { color: #818cf8; background: #1e1b4b; }

        /* Code block */
        .code-block { background: #0f172a; border-radius: 0.5rem; overflow-x: auto; }
        .code-block .k  { color: #818cf8; }   /* keys / indigo */
        .code-block .s  { color: #34d399; }   /* strings / emerald */
        .code-block .n  { color: #fbbf24; }   /* numbers / amber */
        .code-block .b  { color: #f87171; }   /* booleans / red */
        .code-block .c  { color: #64748b; }   /* comments / slate */

        /* Section */
        .section-anchor { scroll-margin-top: 80px; }

        /* Endpoint card */
        .endpoint-card {
            border-radius: 0.75rem;
            border: 1px solid;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: box-shadow 0.15s;
        }
        .dark .endpoint-card { border-color: #1e293b; background: #0f172a; }
        .endpoint-card { border-color: #e2e8f0; background: #f8fafc; }
        .endpoint-card:hover { box-shadow: 0 4px 24px rgba(79,70,229,0.08); }

        /* Method badges */
        .method-get    { background:#dbeafe; color:#1d4ed8; }
        .method-post   { background:#dcfce7; color:#15803d; }
        .method-put    { background:#fef3c7; color:#b45309; }
        .method-delete { background:#fee2e2; color:#b91c1c; }
        .dark .method-get    { background:#1e3a5f; color:#93c5fd; }
        .dark .method-post   { background:#14532d; color:#86efac; }
        .dark .method-put    { background:#451a03; color:#fcd34d; }
        .dark .method-delete { background:#450a0a; color:#fca5a5; }

        .method-badge {
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em;
            padding: 0.2rem 0.6rem; border-radius: 0.3rem;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Auth badge */
        .badge-auth   { background:#ede9fe; color:#6d28d9; font-size:0.7rem; padding:0.2rem 0.55rem; border-radius:9999px; }
        .badge-public { background:#f0fdf4; color:#15803d; font-size:0.7rem; padding:0.2rem 0.55rem; border-radius:9999px; }
        .dark .badge-auth   { background:#2e1065; color:#c4b5fd; }
        .dark .badge-public { background:#14532d; color:#86efac; }

        /* Param table */
        .param-table th { font-size:0.7rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; }
        .required-dot { display:inline-block; width:6px; height:6px; border-radius:50%; background:#ef4444; margin-right:4px; vertical-align:middle; }
        .optional-dot { display:inline-block; width:6px; height:6px; border-radius:50%; background:#94a3b8; margin-right:4px; vertical-align:middle; }

        /* Overlay */
        .sidebar-overlay { display:none; }
        @media (max-width: 1023px) {
            .sidebar-overlay.open { display:block; }
        }

        /* Sticky top bar */
        .top-bar { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased">

<!-- ── TOP NAVBAR ──────────────────────────────────────────────────────────── -->
<header class="top-bar fixed top-0 left-0 right-0 z-50 border-b border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 h-16 flex items-center px-4 lg:px-6">
    <div class="flex items-center gap-3 flex-1">
        <!-- Mobile menu toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2 font-bold text-indigo-600 dark:text-indigo-400 text-lg tracking-tight select-none">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 32 32">
                <rect width="32" height="32" rx="8" fill="#4f46e5"/>
                <path d="M8 22L14 10l4 8 3-5 3 9" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            MockDasher
        </a>
        <span class="hidden sm:block text-slate-300 dark:text-slate-600 font-light text-lg">/</span>
        <span class="hidden sm:block font-semibold text-slate-700 dark:text-slate-300">API Reference</span>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-xs font-mono font-semibold bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2.5 py-1 rounded-full">v1</span>
        <!-- Dark mode toggle -->
        <button @click="toggleDark()" class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition" title="Toggle dark mode">
            <svg x-show="!darkMode" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="darkMode" class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
    </div>
</header>

<!-- ── MOBILE SIDEBAR OVERLAY ─────────────────────────────────────────────── -->
<div class="sidebar-overlay fixed inset-0 z-40 bg-black/50" :class="{ open: sidebarOpen }" @click="sidebarOpen = false" x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

<!-- ── LAYOUT ──────────────────────────────────────────────────────────────── -->
<div class="flex pt-16 min-h-screen">

    <!-- ── SIDEBAR ─────────────────────────────────────────────────────────── -->
    <aside class="fixed top-16 left-0 h-[calc(100vh-4rem)] w-64 overflow-y-auto z-40 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-transform duration-200"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <nav class="p-4 space-y-1">
            <p class="px-3 py-1 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Navigation</p>

            <template x-for="section in sections" :key="section.id">
                <a :href="'#' + section.id"
                   class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                   :class="{ 'active': activeSection === section.id }"
                   @click="sidebarOpen = false">
                    <span x-text="section.icon" class="text-base"></span>
                    <span x-text="section.label"></span>
                </a>
            </template>

            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                <p class="px-3 py-1 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Quick Links</p>
                <a href="#errors" class="nav-link flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-slate-800 transition-colors" @click="sidebarOpen = false">
                    <span>⚠️</span> Error Codes
                </a>
            </div>
        </nav>
    </aside>

    <!-- ── MAIN CONTENT ────────────────────────────────────────────────────── -->
    <main class="flex-1 lg:ml-64 min-w-0">
        <div class="max-w-4xl mx-auto px-4 sm:px-8 py-10">

            <!-- ── HERO ─────────────────────────────────────────────────────── -->
            <div class="mb-12 pb-10 border-b border-slate-200 dark:border-slate-800">
                <div class="inline-flex items-center gap-2 text-xs font-mono font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/40 px-3 py-1.5 rounded-full mb-4">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    REST API · v1 · Sanctum Auth
                </div>
                <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-3">MockDasher API</h1>
                <p class="text-lg text-slate-500 dark:text-slate-400 max-w-2xl">
                    Complete programmatic access to the MockDasher IELTS mock-test platform — attempts, all four modules (Writing, Speaking, Listening, Reading), AI evaluation, and user profiles.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 rounded-lg px-4 py-2 text-sm">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="text-slate-600 dark:text-slate-300">Base URL: <code class="font-mono text-indigo-600 dark:text-indigo-400">{{ url('/api/v1') }}</code></span>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 rounded-lg px-4 py-2 text-sm">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span class="text-slate-600 dark:text-slate-300">Laravel Sanctum · Bearer Token</span>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 rounded-lg px-4 py-2 text-sm">
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-slate-600 dark:text-slate-300">Content-Type: application/json</span>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 1 — GETTING STARTED -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="getting-started" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>🚀</span> Getting Started
                </h2>

                <!-- Base URL card -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/50 dark:to-purple-950/50 rounded-xl p-5 mb-6 border border-indigo-100 dark:border-indigo-900">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200 mb-2">Base URL</h3>
                    <code class="mono text-sm text-indigo-700 dark:text-indigo-300 break-all">{{ url('/api/v1') }}</code>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">All endpoints are relative to this base. Always use HTTPS in production.</p>
                </div>

                <!-- Authentication -->
                <div class="bg-white dark:bg-slate-900 rounded-xl p-5 mb-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200 mb-3">Authentication</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        The API uses <strong>Laravel Sanctum</strong> Bearer token authentication. Obtain a token by posting credentials to <code class="mono text-indigo-600 dark:text-indigo-400">/api/v1/auth/login</code>, then include the token on every subsequent request:
                    </p>
                    <div class="code-block p-4 text-xs">
                        <pre class="text-slate-300"><span class="c"># Login to get a token</span>
curl -X POST {{ url('/api/v1/auth/login') }} \
  -H <span class="s">"Content-Type: application/json"</span> \
  -d <span class="s">'{"email":"user@example.com","password":"secret"}'</span>

<span class="c"># Use the token on protected endpoints</span>
curl {{ url('/api/v1/auth/me') }} \
  -H <span class="s">"Authorization: Bearer YOUR_TOKEN_HERE"</span></pre>
                    </div>
                </div>

                <!-- Response format -->
                <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200 mb-3">Response Format</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">All responses are JSON. Successful responses return the resource directly or wrapped in <code class="mono text-indigo-600 dark:text-indigo-400">data</code>. Error responses include <code class="mono text-indigo-600 dark:text-indigo-400">message</code> and optionally <code class="mono text-indigo-600 dark:text-indigo-400">errors</code>.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-green-600 dark:text-green-400 mb-2">Success (2xx)</p>
                            <div class="code-block p-3 text-xs"><pre class="text-slate-300">{
  <span class="k">"data"</span>: { <span class="c">/* resource */</span> },
  <span class="k">"meta"</span>: { <span class="c">/* pagination */</span> }
}</pre></div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Error (4xx / 5xx)</p>
                            <div class="code-block p-3 text-xs"><pre class="text-slate-300">{
  <span class="k">"message"</span>: <span class="s">"Validation failed."</span>,
  <span class="k">"errors"</span>: {
    <span class="k">"field"</span>: [<span class="s">"reason"</span>]
  }
}</pre></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 2 — AUTH -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="auth" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>🔑</span> Authentication
                </h2>

                <!-- POST /auth/login -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/auth/login</code>
                        <span class="badge-public">Public</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Authenticate and obtain a 30-day Bearer token.</p>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Request Body</h4>
                    <div class="overflow-x-auto mb-5">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Description</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400">
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">email</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">User email address</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">password</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Account password</td></tr>
                                <tr><td class="py-2 pr-4 mono text-xs">device_name</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">Token label (default: User-Agent)</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Response <span class="text-green-600">200</span></h4>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"token"</span>: <span class="s">"1|abc123..."</span>,
  <span class="k">"token_type"</span>: <span class="s">"Bearer"</span>,
  <span class="k">"expires_at"</span>: <span class="s">"2026-06-25T10:00:00.000000Z"</span>,
  <span class="k">"user"</span>: {
    <span class="k">"id"</span>: <span class="n">42</span>,
    <span class="k">"name"</span>: <span class="s">"Jane Doe"</span>,
    <span class="k">"email"</span>: <span class="s">"jane@example.com"</span>,
    <span class="k">"country"</span>: <span class="s">"BD"</span>,
    <span class="k">"target_band_score"</span>: <span class="n">7.5</span>,
    <span class="k">"exam_type"</span>: <span class="s">"Academic"</span>,
    <span class="k">"exam_date"</span>: <span class="s">"2026-07-15"</span>,
    <span class="k">"is_admin"</span>: <span class="b">false</span>
  }
}</pre></div>
                </div>

                <!-- GET /auth/me -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/auth/me</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return the currently authenticated user's profile.</p>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Response <span class="text-green-600">200</span></h4>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"id"</span>: <span class="n">42</span>, <span class="k">"name"</span>: <span class="s">"Jane Doe"</span>, <span class="k">"email"</span>: <span class="s">"jane@example.com"</span>,
  <span class="k">"country"</span>: <span class="s">"BD"</span>, <span class="k">"target_band_score"</span>: <span class="n">7.5</span>,
  <span class="k">"exam_type"</span>: <span class="s">"Academic"</span>, <span class="k">"exam_date"</span>: <span class="s">"2026-07-15"</span>,
  <span class="k">"is_admin"</span>: <span class="b">false</span>, <span class="k">"avatar_url"</span>: <span class="s">"https://..."</span>
}</pre></div>
                </div>

                <!-- POST /auth/logout -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/auth/logout</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Revoke the current access token (single device logout).</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"message"</span>: <span class="s">"Logged out successfully."</span> }</pre></div>
                </div>

                <!-- POST /auth/logout-all -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/auth/logout-all</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Revoke all tokens for this user (all devices).</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"message"</span>: <span class="s">"All sessions revoked."</span> }</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 3 — PROFILE -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="profile" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>👤</span> Profile
                </h2>

                <!-- GET /profile -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/profile</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return the authenticated user's full profile.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"id"</span>: <span class="n">42</span>, <span class="k">"name"</span>: <span class="s">"Jane Doe"</span>,
  <span class="k">"first_name"</span>: <span class="s">"Jane"</span>, <span class="k">"last_name"</span>: <span class="s">"Doe"</span>,
  <span class="k">"email"</span>: <span class="s">"jane@example.com"</span>, <span class="k">"country"</span>: <span class="s">"BD"</span>,
  <span class="k">"target_band_score"</span>: <span class="n">7.5</span>, <span class="k">"exam_type"</span>: <span class="s">"Academic"</span>,
  <span class="k">"exam_date"</span>: <span class="s">"2026-07-15"</span>, <span class="k">"avatar_url"</span>: <span class="s">"https://..."</span>,
  <span class="k">"is_admin"</span>: <span class="b">false</span>, <span class="k">"has_gemini_key"</span>: <span class="b">true</span>,
  <span class="k">"email_verified_at"</span>: <span class="s">"2026-01-01T00:00:00Z"</span>,
  <span class="k">"created_at"</span>: <span class="s">"2026-01-01T00:00:00Z"</span>
}</pre></div>
                </div>

                <!-- PUT /profile -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-put">PUT</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/profile</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Update profile info. Send as <code class="mono text-xs">multipart/form-data</code> if uploading a photo.</p>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Request Body</h4>
                    <div class="overflow-x-auto mb-5">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">first_name</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">max:100</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">last_name</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">max:100</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">email</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Unique, ignores own record</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">country</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">max:100</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">target_band_score</td><td class="py-2 pr-4">number</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">1–9</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">exam_type</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">e.g. "Academic" / "General"</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">exam_date</td><td class="py-2 pr-4">date</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">YYYY-MM-DD</td></tr>
                                <tr><td class="py-2 pr-4 mono text-xs">photo</td><td class="py-2 pr-4">file</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">jpg/jpeg/png/webp, max 2 MB</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"message"</span>: <span class="s">"Profile updated successfully."</span>, <span class="k">"user"</span>: { <span class="c">/* updated user */</span> } }</pre></div>
                </div>

                <!-- PUT /profile/password -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-put">PUT</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/profile/password</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Change password. Requires correct current password.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">current_password</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Must match account password</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">password</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">min:8</td></tr>
                                <tr><td class="py-2 pr-4 mono text-xs">password_confirmation</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Must match password</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"message"</span>: <span class="s">"Password updated successfully."</span> }</pre></div>
                </div>

                <!-- PUT /profile/gemini-key -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-put">PUT</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/profile/gemini-key</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Save or remove personal Gemini API key. Key is verified via a live Google API call before saving. Send empty string or omit to clear.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr><td class="py-2 pr-4 mono text-xs">gemini_api_key</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">Must start with "AIza". max:200. Empty to clear.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"message"</span>: <span class="s">"Gemini API key saved and verified successfully."</span>, <span class="k">"has_gemini_key"</span>: <span class="b">true</span> }</pre></div>
                </div>

                <!-- DELETE /profile -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-delete">DELETE</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/profile</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Permanently delete account and revoke all tokens. Irreversible.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr><td class="py-2 pr-4 mono text-xs">current_password</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Confirmation password</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"message"</span>: <span class="s">"Account deleted successfully."</span> }</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 4 — TESTS -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="tests" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>📋</span> Tests
                </h2>

                <!-- GET /tests -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/tests</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return paginated list of published tests.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"data"</span>: [
    { <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"title"</span>: <span class="s">"IELTS Academic Mock #1"</span>, <span class="k">"type"</span>: <span class="s">"Academic"</span>,
      <span class="k">"description"</span>: <span class="s">"..."</span>, <span class="k">"sets_count"</span>: <span class="n">3</span> }
  ],
  <span class="k">"meta"</span>: { <span class="k">"current_page"</span>: <span class="n">1</span>, <span class="k">"last_page"</span>: <span class="n">2</span>, <span class="k">"per_page"</span>: <span class="n">15</span>, <span class="k">"total"</span>: <span class="n">24</span> }
}</pre></div>
                </div>

                <!-- GET /tests/{id} -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/tests/{id}</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return a single test with its available sets.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"data"</span>: {
    <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"title"</span>: <span class="s">"IELTS Academic Mock #1"</span>,
    <span class="k">"sets"</span>: [{ <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"set_number"</span>: <span class="n">1</span> }]
  }
}</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 5 — ATTEMPTS -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="attempts" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>📝</span> Attempts
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">A <strong>TestAttempt</strong> is the root record for a full 4-module IELTS test session. Each module (Writing, Speaking, Listening, Reading) is a sub-resource of the attempt.</p>

                <!-- GET /attempts -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Paginated list of the authenticated user's attempts.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"data"</span>: [{
    <span class="k">"id"</span>: <span class="n">5</span>, <span class="k">"test_title"</span>: <span class="s">"IELTS Mock #1"</span>, <span class="k">"status"</span>: <span class="s">"in_progress"</span>,
    <span class="k">"overall_band"</span>: <span class="b">null</span>, <span class="k">"reading_band"</span>: <span class="n">7.0</span>, <span class="k">"listening_band"</span>: <span class="n">6.5</span>,
    <span class="k">"writing_band"</span>: <span class="b">null</span>, <span class="k">"speaking_band"</span>: <span class="b">null</span>,
    <span class="k">"started_at"</span>: <span class="s">"2026-05-26T10:00:00Z"</span>, <span class="k">"completed_at"</span>: <span class="b">null</span>,
    <span class="k">"time_spent"</span>: <span class="b">null</span>
  }],
  <span class="k">"meta"</span>: { <span class="k">"current_page"</span>: <span class="n">1</span>, <span class="k">"last_page"</span>: <span class="n">1</span>, <span class="k">"per_page"</span>: <span class="n">15</span>, <span class="k">"total"</span>: <span class="n">3</span> }
}</pre></div>
                </div>

                <!-- POST /attempts -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Start a new attempt or resume an existing incomplete one for the given test set.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr><td class="py-2 pr-4 mono text-xs">test_set_id</td><td class="py-2 pr-4">integer</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Must exist in test_sets table</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"data"</span>: { <span class="c">/* attempt object */</span> },
  <span class="k">"resumed"</span>: <span class="b">false</span>
}</pre></div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Returns <code class="mono">201</code> for new attempts, <code class="mono">200</code> when resuming. <code class="mono">resumed: true</code> when an existing incomplete attempt is returned.</p>
                </div>

                <!-- GET /attempts/{id} -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{id}</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return full attempt detail including answers and AI evaluations.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"data"</span>: {
  <span class="k">"id"</span>: <span class="n">5</span>, <span class="k">"status"</span>: <span class="s">"in_progress"</span>,
  <span class="k">"writing_answers"</span>: [...], <span class="k">"speaking_answers"</span>: [...],
  <span class="k">"ai_writing"</span>: { <span class="k">"status"</span>: <span class="s">"completed"</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span> },
  <span class="k">"ai_speaking"</span>: <span class="b">null</span>
}}</pre></div>
                </div>

                <!-- GET /attempts/{id}/status -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{id}/status</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Poll AI evaluation status for Writing and Speaking. Useful for long-polling while evaluation is queued.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">5</span>,
  <span class="k">"writing"</span>:  { <span class="k">"status"</span>: <span class="s">"pending"</span>, <span class="k">"band_score"</span>: <span class="b">null</span>, <span class="k">"failure_reason"</span>: <span class="b">null</span> },
  <span class="k">"speaking"</span>: { <span class="k">"status"</span>: <span class="s">"not_started"</span>, <span class="k">"band_score"</span>: <span class="b">null</span>, <span class="k">"failure_reason"</span>: <span class="b">null</span> }
}</pre></div>
                </div>

                <!-- POST /attempts/{id}/finish -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{id}/finish</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Mark the overall test attempt as complete (called after all modules are done).</p>
                </div>

                <!-- POST /attempts/{id}/violation -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{id}/violation</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Record a proctoring violation (tab switch, focus loss, etc.).</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr><td class="py-2 pr-4 mono text-xs">type</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">e.g. "tab_switch", "focus_loss"</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 6 — WRITING -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="writing" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                    <span>✍️</span> Writing Module
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">60-minute timed module. Timer starts on first <code class="mono text-xs">GET …/writing</code> call. Use autosave every 30s. Lock individual tasks with <code class="mono text-xs">POST …/tasks/{task}/submit</code>, then finalize with <code class="mono text-xs">POST …/writing/submit</code> to queue AI evaluation.</p>

                <!-- GET writing/show -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/writing</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Load writing state. Starts the timer on first call. Returns 409 if already completed, 422 if time has expired.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">5</span>,
  <span class="k">"remaining_seconds"</span>: <span class="n">3540</span>,
  <span class="k">"writing_started_at"</span>: <span class="s">"2026-05-26T10:00:00Z"</span>,
  <span class="k">"tasks"</span>: [{
    <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"task_number"</span>: <span class="n">1</span>, <span class="k">"task_title"</span>: <span class="s">"Task 1"</span>,
    <span class="k">"task_prompt"</span>: <span class="s">"Describe the graph..."</span>, <span class="k">"precontext"</span>: <span class="s">"..."</span>,
    <span class="k">"min_words"</span>: <span class="n">150</span>,
    <span class="k">"answer"</span>: { <span class="k">"text"</span>: <span class="s">"..."</span>, <span class="k">"word_count"</span>: <span class="n">200</span>, <span class="k">"submitted"</span>: <span class="b">false</span>, <span class="k">"band_score"</span>: <span class="b">null</span> }
  }]
}</pre></div>
                </div>

                <!-- POST writing/autosave -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/writing/autosave</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Silently save in-progress text. Will not overwrite already-locked (submitted) answers.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr><td class="py-2 pr-4 mono text-xs">answers</td><td class="py-2 pr-4">object</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">Keys = task IDs, values = answer text (max 65535 chars)</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300"><span class="c">// Request</span>
{ <span class="k">"answers"</span>: { <span class="k">"1"</span>: <span class="s">"The graph shows..."</span>, <span class="k">"2"</span>: <span class="s">"Some argue..."</span> } }

<span class="c">// Response</span>
{ <span class="k">"success"</span>: <span class="b">true</span>, <span class="k">"saved_at"</span>: <span class="s">"10:35:22"</span> }</pre></div>
                </div>

                <!-- POST writing/tasks/{task}/submit -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/writing/tasks/{task}/submit</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Lock a single task answer. Once locked, autosave will no longer overwrite it. Returns 409 if already locked.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr><td class="py-2 pr-4 mono text-xs">answer</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">Final answer text, max 65535 chars</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"success"</span>: <span class="b">true</span> }</pre></div>
                </div>

                <!-- POST writing/submit -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/writing/submit</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Finalize writing and dispatch Gemini AI evaluation job to the queue. Poll <code class="mono text-xs">GET …/writing/result</code> for status.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"success"</span>: <span class="b">true</span>,
  <span class="k">"evaluation_status"</span>: <span class="s">"pending"</span>,
  <span class="k">"message"</span>: <span class="s">"Writing submitted. AI evaluation queued."</span>
}</pre></div>
                </div>

                <!-- GET writing/result -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/writing/result</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return writing evaluation result. Poll until <code class="mono text-xs">evaluation_status</code> is <code class="mono text-xs">"completed"</code> or <code class="mono text-xs">"failed"</code>.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">5</span>,
  <span class="k">"writing_band"</span>: <span class="n">7.0</span>,
  <span class="k">"evaluation_status"</span>: <span class="s">"completed"</span>,
  <span class="k">"failure_reason"</span>: <span class="b">null</span>,
  <span class="k">"tasks"</span>: [{
    <span class="k">"task_number"</span>: <span class="n">1</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>, <span class="k">"word_count"</span>: <span class="n">212</span>,
    <span class="k">"evaluation"</span>: { <span class="k">"task_achievement"</span>: <span class="n">7</span>, <span class="k">"coherence"</span>: <span class="n">7</span>, <span class="c">/* ... */</span> }
  }]
}</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 7 — SPEAKING -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="speaking" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                    <span>🎙️</span> Speaking Module
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">20-minute timed module. Upload audio recordings per question (WebM/OGG/MP4/WAV, max 10 MB). Include a Speech-to-Text transcript for AI evaluation accuracy.</p>

                <!-- GET speaking/show -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/speaking</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Load speaking state with questions grouped by part.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">5</span>, <span class="k">"remaining_seconds"</span>: <span class="n">1140</span>,
  <span class="k">"speaking_started_at"</span>: <span class="s">"2026-05-26T11:00:00Z"</span>,
  <span class="k">"parts"</span>: [{
    <span class="k">"part"</span>: <span class="n">1</span>,
    <span class="k">"questions"</span>: [{
      <span class="k">"id"</span>: <span class="n">10</span>, <span class="k">"question_text"</span>: <span class="s">"Tell me about your hometown."</span>, <span class="k">"time_limit"</span>: <span class="n">60</span>,
      <span class="k">"answer"</span>: { <span class="k">"audio_path"</span>: <span class="s">"speaking_recordings/5/q10.webm"</span>,
                  <span class="k">"transcript_text"</span>: <span class="s">"I grew up in..."</span>, <span class="k">"submitted"</span>: <span class="b">false</span> }
    }]
  }]
}</pre></div>
                </div>

                <!-- POST speaking/upload -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/speaking/upload</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Upload audio for a question. Send as <code class="mono text-xs">multipart/form-data</code>.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">question_id</td><td class="py-2 pr-4">integer</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Must belong to this attempt's test set</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">audio</td><td class="py-2 pr-4">file</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">webm/ogg/mp4/mpeg/mp3/wav, max 10 MB</td></tr>
                                <tr class="border-b border-slate-100 dark:border-slate-800"><td class="py-2 pr-4 mono text-xs">transcript</td><td class="py-2 pr-4">string</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">STT transcript, max 10000 chars</td></tr>
                                <tr><td class="py-2 pr-4 mono text-xs">duration</td><td class="py-2 pr-4">integer</td><td class="py-2 pr-4"><span class="optional-dot"></span>No</td><td class="py-2">Recording duration in seconds, max 600</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"success"</span>: <span class="b">true</span>, <span class="k">"path"</span>: <span class="s">"speaking_recordings/5/audio_abc123.webm"</span> }</pre></div>
                </div>

                <!-- POST speaking/questions/{question}/submit -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/speaking/questions/{question}/submit</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Lock a question answer. Returns 409 if already locked.</p>
                    <div class="mt-3 code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"success"</span>: <span class="b">true</span> }</pre></div>
                </div>

                <!-- POST speaking/submit -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/speaking/submit</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Finalize speaking and dispatch AI evaluation job.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"success"</span>: <span class="b">true</span>, <span class="k">"evaluation_status"</span>: <span class="s">"pending"</span>,
  <span class="k">"message"</span>: <span class="s">"Speaking submitted. AI evaluation queued."</span>
}</pre></div>
                </div>

                <!-- GET speaking/result -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/speaking/result</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return speaking evaluation result per question.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">5</span>, <span class="k">"speaking_band"</span>: <span class="n">7.5</span>, <span class="k">"evaluation_status"</span>: <span class="s">"completed"</span>,
  <span class="k">"questions"</span>: [{
    <span class="k">"id"</span>: <span class="n">10</span>, <span class="k">"part"</span>: <span class="n">1</span>, <span class="k">"question_text"</span>: <span class="s">"Tell me about your hometown."</span>,
    <span class="k">"transcript"</span>: <span class="s">"I grew up in Dhaka..."</span>, <span class="k">"band_score"</span>: <span class="n">7.5</span>,
    <span class="k">"evaluation"</span>: { <span class="k">"fluency"</span>: <span class="n">7.5</span>, <span class="k">"vocabulary"</span>: <span class="n">7.5</span>, <span class="c">/* ... */</span> }
  }]
}</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 8 — LISTENING -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="listening" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                    <span>🎧</span> Listening Module
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">30-minute listening phase (4 sections) followed by a 10-minute transfer phase. Call <code class="mono text-xs">complete-section</code> after each section. Automated band scoring — no AI queue.</p>

                <!-- GET listening/show -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/listening</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Load listening state with sections, questions, saved answers, and timer info.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">7</span>, <span class="k">"status"</span>: <span class="s">"in_progress"</span>,
  <span class="k">"current_section"</span>: <span class="n">2</span>, <span class="k">"remaining_seconds"</span>: <span class="n">900</span>,
  <span class="k">"transfer_remaining_seconds"</span>: <span class="b">null</span>,
  <span class="k">"sections"</span>: [{
    <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"section_number"</span>: <span class="n">1</span>, <span class="k">"title"</span>: <span class="s">"Section 1"</span>,
    <span class="k">"audio_url"</span>: <span class="s">"https://.../section1.mp3"</span>,
    <span class="k">"questions"</span>: [{
      <span class="k">"id"</span>: <span class="n">100</span>, <span class="k">"question_text"</span>: <span class="s">"What is the man's name?"</span>,
      <span class="k">"options"</span>: [{ <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"label"</span>: <span class="s">"A"</span>, <span class="k">"text"</span>: <span class="s">"John"</span> }],
      <span class="k">"saved_answer"</span>: <span class="s">"John"</span>, <span class="k">"is_flagged"</span>: <span class="b">false</span>
    }]
  }]
}</pre></div>
                    <p class="text-xs text-slate-400 mt-2">Note: <code class="mono">correct_answer</code> is never exposed in the show response.</p>
                </div>

                <!-- POST listening/autosave -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/listening/autosave</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Save answers and flagged state without scoring.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300"><span class="c">// Request</span>
{ <span class="k">"answers"</span>: { <span class="k">"100"</span>: <span class="s">"John"</span>, <span class="k">"101"</span>: <span class="s">"B"</span> }, <span class="k">"flagged"</span>: { <span class="k">"102"</span>: <span class="b">true</span> } }
<span class="c">// Response</span>
{ <span class="k">"success"</span>: <span class="b">true</span>, <span class="k">"saved_at"</span>: <span class="s">"11:22:10"</span> }</pre></div>
                </div>

                <!-- POST listening/complete-section -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/listening/complete-section</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-5">Signal that the user finished a section's audio. After section 4, switches to transfer phase.</p>
                    <div class="overflow-x-auto mb-4">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b border-slate-200 dark:border-slate-700"><th class="text-left py-2 pr-4 text-slate-500">Field</th><th class="text-left py-2 pr-4 text-slate-500">Type</th><th class="text-left py-2 pr-4 text-slate-500">Required</th><th class="text-left py-2 text-slate-500">Notes</th></tr></thead>
                            <tbody class="text-slate-600 dark:text-slate-400 text-sm">
                                <tr><td class="py-2 pr-4 mono text-xs">section</td><td class="py-2 pr-4">integer</td><td class="py-2 pr-4"><span class="required-dot"></span>Yes</td><td class="py-2">Section number (1–4)</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300"><span class="c">// After sections 1–3:</span>
{ <span class="k">"status"</span>: <span class="s">"next"</span>, <span class="k">"next_section"</span>: <span class="n">2</span> }

<span class="c">// After section 4:</span>
{ <span class="k">"status"</span>: <span class="s">"transfer"</span>, <span class="k">"transfer_seconds"</span>: <span class="n">600</span> }</pre></div>
                </div>

                <!-- POST listening/submit -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/listening/submit</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Submit final answers, evaluate immediately, return band score.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"success"</span>: <span class="b">true</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>, <span class="k">"score"</span>: <span class="n">30</span> }</pre></div>
                </div>

                <!-- GET listening/result -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/listening/result</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return per-question correctness. <code class="mono text-xs">correct_answer</code> is revealed post-submission.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">7</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>, <span class="k">"score"</span>: <span class="n">30</span>, <span class="k">"total_questions"</span>: <span class="n">40</span>,
  <span class="k">"sections"</span>: [{
    <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"section_number"</span>: <span class="n">1</span>,
    <span class="k">"questions"</span>: [{ <span class="k">"id"</span>: <span class="n">100</span>, <span class="k">"user_answer"</span>: <span class="s">"John"</span>, <span class="k">"is_correct"</span>: <span class="b">true</span> }]
  }]
}</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 9 — READING -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="reading" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                    <span>📖</span> Reading Module
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">60-minute timed module with 3 passages. Automated band scoring — no AI queue. Same autosave pattern as Listening.</p>

                <!-- GET reading/show -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/reading</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Load passages with question groups and saved answers.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">8</span>, <span class="k">"remaining_seconds"</span>: <span class="n">3200</span>,
  <span class="k">"passages"</span>: [{
    <span class="k">"id"</span>: <span class="n">1</span>, <span class="k">"passage_number"</span>: <span class="n">1</span>, <span class="k">"title"</span>: <span class="s">"The Science of Sleep"</span>,
    <span class="k">"content"</span>: <span class="s">"..."</span>,
    <span class="k">"question_groups"</span>: [{
      <span class="k">"id"</span>: <span class="n">5</span>, <span class="k">"group_type"</span>: <span class="s">"multiple_choice"</span>,
      <span class="k">"instruction"</span>: <span class="s">"Choose one letter A-D"</span>,
      <span class="k">"questions"</span>: [{ <span class="k">"id"</span>: <span class="n">200</span>, <span class="k">"question_text"</span>: <span class="s">"..."</span>,
        <span class="k">"options"</span>: [...], <span class="k">"saved_answer"</span>: <span class="s">"B"</span>, <span class="k">"is_flagged"</span>: <span class="b">false</span> }]
    }]
  }]
}</pre></div>
                </div>

                <!-- POST reading/autosave -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/reading/autosave</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Save answers and flag state without scoring.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"success"</span>: <span class="b">true</span>, <span class="k">"saved_at"</span>: <span class="s">"11:40:00"</span> }</pre></div>
                </div>

                <!-- POST reading/submit -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-post">POST</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/reading/submit</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Submit final answers, evaluate, return band score.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"success"</span>: <span class="b">true</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>, <span class="k">"score"</span>: <span class="n">30</span> }</pre></div>
                </div>

                <!-- GET reading/result -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/attempts/{attempt}/reading/result</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Return per-question correctness across all passages.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"attempt_id"</span>: <span class="n">8</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>, <span class="k">"score"</span>: <span class="n">30</span>, <span class="k">"total_questions"</span>: <span class="n">40</span>,
  <span class="k">"passages"</span>: [{ <span class="k">"question_groups"</span>: [{ <span class="k">"questions"</span>: [
    { <span class="k">"id"</span>: <span class="n">200</span>, <span class="k">"user_answer"</span>: <span class="s">"B"</span>, <span class="k">"is_correct"</span>: <span class="b">true</span> }
  ]}]}]
}</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 10 — HISTORY -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="history" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>📊</span> History
                </h2>

                <!-- GET /history -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/history</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Paginated list of user's test attempts with all band scores.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"data"</span>: [{
    <span class="k">"id"</span>: <span class="n">5</span>, <span class="k">"test_title"</span>: <span class="s">"IELTS Mock #1"</span>, <span class="k">"status"</span>: <span class="s">"in_progress"</span>,
    <span class="k">"overall_band"</span>: <span class="b">null</span>, <span class="k">"reading_band"</span>: <span class="n">7.0</span>, <span class="k">"listening_band"</span>: <span class="n">6.5</span>,
    <span class="k">"writing_band"</span>: <span class="b">null</span>, <span class="k">"speaking_band"</span>: <span class="b">null</span>,
    <span class="k">"reading_score"</span>: <span class="n">30</span>, <span class="k">"listening_score"</span>: <span class="n">27</span>,
    <span class="k">"started_at"</span>: <span class="s">"2026-05-26T10:00:00Z"</span>, <span class="k">"time_spent"</span>: <span class="s">"2h 15m"</span>
  }],
  <span class="k">"meta"</span>: { <span class="k">"current_page"</span>: <span class="n">1</span>, <span class="k">"last_page"</span>: <span class="n">1</span>, <span class="k">"per_page"</span>: <span class="n">15</span>, <span class="k">"total"</span>: <span class="n">3</span> }
}</pre></div>
                </div>

                <!-- GET /history/{id} -->
                <div class="endpoint-card">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="method-badge method-get">GET</span>
                        <code class="mono text-sm text-slate-700 dark:text-slate-300">/api/v1/history/{id}</code>
                        <span class="badge-auth">Auth required</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Full attempt detail with all module results, AI evaluations, and per-answer breakdowns.</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{
  <span class="k">"data"</span>: {
    <span class="k">"id"</span>: <span class="n">5</span>, <span class="k">"overall_band"</span>: <span class="n">7.0</span>,
    <span class="k">"writing"</span>: {
      <span class="k">"evaluation_status"</span>: <span class="s">"completed"</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>,
      <span class="k">"answers"</span>: [{ <span class="k">"task_number"</span>: <span class="n">1</span>, <span class="k">"word_count"</span>: <span class="n">212</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>,
        <span class="k">"evaluation"</span>: { <span class="c">/* Gemini rubric */</span> } }]
    },
    <span class="k">"speaking"</span>: { <span class="k">"evaluation_status"</span>: <span class="s">"pending"</span>, <span class="k">"band_score"</span>: <span class="b">null</span>, <span class="k">"answers"</span>: [] },
    <span class="k">"listening"</span>: { <span class="k">"status"</span>: <span class="s">"completed"</span>, <span class="k">"band_score"</span>: <span class="n">6.5</span>, <span class="k">"score"</span>: <span class="n">27</span> },
    <span class="k">"reading"</span>:   { <span class="k">"status"</span>: <span class="s">"completed"</span>, <span class="k">"band_score"</span>: <span class="n">7.0</span>, <span class="k">"score"</span>: <span class="n">30</span> }
  }
}</pre></div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- SECTION 11 — ERROR CODES -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <section id="errors" class="section-anchor mb-16">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>⚠️</span> Error Codes
                </h2>
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                                <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-300">Code</th>
                                <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-300">Meaning</th>
                                <th class="text-left py-3 px-4 font-semibold text-slate-600 dark:text-slate-300 hidden sm:table-cell">When it occurs</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-600 dark:text-slate-400 divide-y divide-slate-100 dark:divide-slate-800">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-blue-600 dark:text-blue-400">400</span></td>
                                <td class="py-3 px-4 font-medium">Bad Request</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Malformed JSON or invalid request structure</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-orange-600 dark:text-orange-400">401</span></td>
                                <td class="py-3 px-4 font-medium">Unauthenticated</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Missing or expired Bearer token</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-red-600 dark:text-red-400">403</span></td>
                                <td class="py-3 px-4 font-medium">Forbidden / IDOR</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Attempt or resource belongs to another user</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-slate-600 dark:text-slate-400">404</span></td>
                                <td class="py-3 px-4 font-medium">Not Found</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Resource does not exist</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-purple-600 dark:text-purple-400">409</span></td>
                                <td class="py-3 px-4 font-medium">Conflict</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Module already completed; answer already locked; evaluation in progress</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-yellow-600 dark:text-yellow-400">422</span></td>
                                <td class="py-3 px-4 font-medium">Unprocessable</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Validation errors (<code class="mono text-xs">errors</code> key present) or time expired</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-amber-600 dark:text-amber-400">429</span></td>
                                <td class="py-3 px-4 font-medium">Too Many Requests</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Rate limit exceeded (Laravel default throttle)</td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4"><span class="mono font-bold text-red-700 dark:text-red-500">500</span></td>
                                <td class="py-3 px-4 font-medium">Server Error</td>
                                <td class="py-3 px-4 hidden sm:table-cell text-sm">Unexpected exception — report with request ID</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Time-expired special case -->
                <div class="mt-6 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl p-5">
                    <h3 class="font-semibold text-amber-800 dark:text-amber-300 mb-2">Time Expired (422)</h3>
                    <p class="text-sm text-amber-700 dark:text-amber-400 mb-3">When a module timer runs out, the server auto-submits and returns:</p>
                    <div class="code-block p-4 text-xs"><pre class="text-slate-300">{ <span class="k">"error"</span>: <span class="s">"time_expired"</span>, <span class="k">"message"</span>: <span class="s">"Writing time has expired. Please submit now."</span> }</pre></div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="pt-8 pb-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-sm text-slate-400 dark:text-slate-500">
                    MockDasher API Reference · Built with Laravel 11 + Sanctum ·
                    <a href="/" class="text-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Back to app</a>
                </p>
            </footer>

        </div>
    </main>
</div>

<script>
function apiDocs() {
    return {
        darkMode: localStorage.getItem('mockdasher-dark') === 'true',
        sidebarOpen: false,
        activeSection: 'getting-started',
        sections: [
            { id: 'getting-started', label: 'Getting Started', icon: '🚀' },
            { id: 'auth',            label: 'Authentication',  icon: '🔑' },
            { id: 'profile',         label: 'Profile',         icon: '👤' },
            { id: 'tests',           label: 'Tests',           icon: '📋' },
            { id: 'attempts',        label: 'Attempts',        icon: '📝' },
            { id: 'writing',         label: 'Writing',         icon: '✍️' },
            { id: 'speaking',        label: 'Speaking',        icon: '🎙️' },
            { id: 'listening',       label: 'Listening',       icon: '🎧' },
            { id: 'reading',         label: 'Reading',         icon: '📖' },
            { id: 'history',         label: 'History',         icon: '📊' },
        ],

        init() {
            this.setupIntersectionObserver();
        },

        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('mockdasher-dark', this.darkMode);
        },

        setupIntersectionObserver() {
            const ids = [...this.sections.map(s => s.id), 'errors'];
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.activeSection = entry.target.id;
                        }
                    });
                },
                { rootMargin: '-20% 0px -70% 0px', threshold: 0 }
            );

            this.$nextTick(() => {
                ids.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) observer.observe(el);
                });
            });
        },
    };
}
</script>
</body>
</html>
