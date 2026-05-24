<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Exam') — {{ config('app.name', 'MockDasher') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet" />

    {{-- Tailwind CDN + Configuration --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {
            --shadow-soft: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-premium: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07);
            --shadow-lift: 0 25px 50px -12px rgb(0 0 0 / 0.15);
        }
        .font-display { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4F46E5",
                        "primary-hover": "#4338CA",
                        "background-light": "#F8FAFC",
                        "background-dark": "#0F172A",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E293B",
                        "error": "#EF4444",
                        "success": "#10B981",
                    },
                    fontFamily: { "display": ["Inter", "sans-serif"] },
                    borderRadius: {
                        "xs": "0.375rem", "sm": "0.5rem", "base": "0.75rem", "DEFAULT": "0.75rem",
                        "md": "0.75rem", "lg": "1rem", "xl": "1.5rem", "2xl": "2rem",
                        "3xl": "2.5rem", "full": "9999px"
                    },
                    boxShadow: {
                        'soft': 'var(--shadow-soft)',
                        'premium': 'var(--shadow-premium)',
                        'lift': 'var(--shadow-lift)',
                    }
                },
            },
        }
    </script>

    {{-- Vite: App CSS + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="h-full overflow-hidden bg-background-light dark:bg-background-dark font-sans text-slate-800 dark:text-slate-100 antialiased flex flex-col">

    {{-- ═══════════════════════════════════════════════════════════════════════
         TOP BAR — Flat, distraction-free
         ═══════════════════════════════════════════════════════════════════════ --}}
    <header class="z-50 flex h-14 shrink-0 items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark px-4 sm:px-6 lg:px-8">

        {{-- Left: Brand + Test Info --}}
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary text-white shadow-soft">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                    <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-black uppercase tracking-widest text-primary leading-none">@yield('test_type', 'Exam')</p>
                <h1 class="truncate text-sm font-extrabold text-slate-900 dark:text-white mt-0.5 tracking-tight">@yield('test_title', 'Mock Test')</h1>
            </div>
        </div>

        {{-- Center: Timer (injected by child views) --}}
        <div class="flex items-center justify-center">
            @yield('timer_area')
        </div>

        {{-- Right: Actions + Candidate --}}
        <div class="flex items-center gap-4">
            @yield('top_right_actions')

            <div class="hidden items-center gap-3 border-l border-slate-200 dark:border-slate-800 pl-4 sm:flex">
                <div class="text-right">
                    <p class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 leading-none">Candidate</p>
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">{{ auth()->user()->name }}</p>
                </div>
                <div class="flex size-8 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-xs font-bold text-primary overflow-hidden border border-indigo-100 dark:border-indigo-800/80 shadow-sm shrink-0">
                    @if(auth()->user()->profile_photo_path)
                        <img class="h-full w-full object-cover" src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════════════════════
         MAIN CONTENT — Full remaining height
         ═══════════════════════════════════════════════════════════════════════ --}}
    <main class="relative flex flex-1 flex-col overflow-hidden">
        @yield('content')
    </main>

    {{-- Proctoring Warning Overlay Modal --}}
    <div id="proctoring-warning-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-red-200 dark:border-red-900/50 bg-white/95 dark:bg-slate-900/95 p-6 shadow-lift text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-950/30 text-red-600 mb-4 border border-red-100 dark:border-red-900/40">
                <svg class="h-8 w-8 animate-bounce text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 uppercase tracking-wide">Proctoring Violation Alert!</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 font-medium">
                You switched tabs or lost focus on the exam window. This is strictly prohibited. 
                <br/><br/>
                Warnings remaining: <span id="proctoring-warning-count" class="font-bold text-red-600 text-base">3</span> / 3.
                <br/>
                On the next violation, your exam will be <strong class="text-red-500">automatically submitted</strong> and locked.
            </p>
            <button type="button" onclick="resumeProctoredExam()" class="w-full bg-primary hover:bg-primary-hover text-white py-3 rounded-xl text-sm font-bold shadow-soft transition-all duration-200 focus:outline-none">
                I Understand & Resume Exam
            </button>
        </div>
    </div>

    <script>
        (function() {
            // Issue 9: violation count is now authoritative on the server.
            // localStorage is kept only as a display cache; the server count governs termination.
            let isWarningActive = false;
            window.isAutoSubmitting = false;
            let isUnloading = false;
            let serverViolations = 0; // updated from server responses

            // Derive the violation-report URL from the current path pattern:
            // /tests/attempts/{id}/writing  →  /tests/attempts/{id}/violation
            const pathParts = window.location.pathname.split('/');
            // pathParts: ['', 'tests', 'attempts', '{id}', 'writing'|'speaking'|...]
            let violationUrl = null;
            if (pathParts[1] === 'tests' && pathParts[2] === 'attempts' && pathParts[3]) {
                violationUrl = '/tests/attempts/' + pathParts[3] + '/violation';
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').content
                : '';

            window.addEventListener('beforeunload', () => { isUnloading = true; });
            window.addEventListener('pagehide',     () => { isUnloading = true; });
            window.addEventListener('unload',       () => { isUnloading = true; });

            function playWarningBeep() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(440, audioCtx.currentTime);
                    gainNode.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    oscillator.start();
                    setTimeout(() => { oscillator.stop(); audioCtx.close(); }, 300);
                } catch(e) {}
            }

            function autoSubmitActiveExam() {
                if (window.isAutoSubmitting) return;
                window.isAutoSubmitting = true;
                window.onbeforeunload = null;

                const speakingForm = document.getElementById('speaking-submit-form');
                if (speakingForm) {
                    if (typeof window.prepareSpeakingSubmit === 'function') window.prepareSpeakingSubmit();
                    speakingForm.submit(); return;
                }
                const writingForm = document.getElementById('writing-submit-form');
                if (writingForm) {
                    if (typeof window.prepareWritingSubmit === 'function') window.prepareWritingSubmit();
                    writingForm.submit(); return;
                }
                const readingForm = document.getElementById('final-submit-form');
                if (readingForm) {
                    if (typeof populateHiddenInputs === 'function') populateHiddenInputs();
                    readingForm.submit(); return;
                }
                const listeningForm = document.getElementById('listening-submit-form');
                if (listeningForm) {
                    if (typeof window.populateListeningInputs === 'function') window.populateListeningInputs();
                    listeningForm.submit(); return;
                }
                window.location.href = '/dashboard';
            }

            // Issue 9: report the violation to the server; act on the server's authoritative count.
            async function reportViolationToServer() {
                if (!violationUrl || !csrfToken) return { violations: 1, status: 'warned', remaining: 2 };
                try {
                    const resp = await fetch(violationUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    });
                    return await resp.json();
                } catch (e) {
                    // Network failure: fall back to local counter (best-effort)
                    return null;
                }
            }

            async function triggerViolation() {
                if (isWarningActive || window.isAutoSubmitting || isUnloading) return;
                isWarningActive = true;

                playWarningBeep();

                // Report to server and use server's authoritative violation count
                const result = await reportViolationToServer();
                const violations = result ? result.violations : (serverViolations + 1);
                serverViolations = violations;

                const modal = document.getElementById('proctoring-warning-modal');
                const warningCountEl = document.getElementById('proctoring-warning-count');
                const terminated = result ? result.status === 'terminated' : violations >= 3;

                if (terminated) {
                    if (warningCountEl) warningCountEl.textContent = '0';
                    if (modal) {
                        modal.querySelector('h3').textContent = 'EXAM TERMINATED!';
                        modal.querySelector('p').innerHTML = 'You have exceeded the maximum of 3 focus-loss violations. Your exam is being automatically submitted now...';
                        modal.querySelector('button').style.display = 'none';
                        modal.classList.remove('hidden');
                    }
                    setTimeout(() => { autoSubmitActiveExam(); }, 1500);
                } else {
                    const remaining = result ? result.remaining : (3 - violations);
                    if (warningCountEl) warningCountEl.textContent = remaining.toString();
                    if (modal) modal.classList.remove('hidden');
                }
            }

            window.resumeProctoredExam = function() {
                const modal = document.getElementById('proctoring-warning-modal');
                if (modal) {
                    modal.classList.add('hidden');
                }
                setTimeout(() => {
                    isWarningActive = false;
                }, 300);
            };

            // Initialize examHasChanges flag
            window.examHasChanges = false;

            // Anti-cheat event triggers: Only trigger on actual visibility changes (tab switches or minimizing browser)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    triggerViolation();
                }
            });

            // Prevent shortcuts and context menu
            document.addEventListener('contextmenu', e => e.preventDefault());
            document.addEventListener('copy', e => e.preventDefault());
            document.addEventListener('paste', e => e.preventDefault());

            // Override form submit to bypass warning on programmatic submits
            const originalSubmit = HTMLFormElement.prototype.submit;
            HTMLFormElement.prototype.submit = function() {
                window.isAutoSubmitting = true;
                window.onbeforeunload = null;
                originalSubmit.apply(this, arguments);
            };

            // Disable warning when submitting any form via standard submit button
            document.addEventListener('submit', function() {
                window.isAutoSubmitting = true;
                window.onbeforeunload = null;
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
