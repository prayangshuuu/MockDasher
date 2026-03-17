<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Speaking & Writing Test - MockDasher</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js for lightweight interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[var(--color-bg)] font-sans antialiased text-[var(--color-text)]" x-data="writingTest({{ $remainingSeconds }}, '{{ route('user.writing.autosave', $attempt->id) }}', '{{ route('user.writing.submit', $attempt->id) }}')">
    
    <!-- Top Bar -->
    <div class="bg-white shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] border-b border-[var(--color-divider)] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-[var(--color-primary)] rounded-md flex items-center justify-center font-bold text-white text-lg shadow-sm">M</div>
                <div>
                    <p class="text-[10px] text-gray-500 font-bold leading-tight uppercase tracking-wider">IELTS Writing</p>
                    <span class="text-sm font-bold truncate block max-w-[200px]">{{ $test->title }}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-6">
                <!-- Timer -->
                <div class="flex items-center gap-2 bg-[var(--color-bg)] border border-[var(--color-divider)] rounded-[var(--radius-base)] px-4 py-2 shadow-sm" :class="timeRemaining <= 300 ? 'text-[var(--color-error)] font-bold animate-pulse' : 'text-[var(--color-text)]'">
                    <i class="fas fa-clock" :class="timeRemaining <= 300 ? 'text-[var(--color-error)]' : 'text-[var(--color-primary)]'"></i>
                    <div class="flex items-end gap-2">
                        <span class="text-lg font-bold font-mono leading-none tracking-widest" x-text="formattedTime"></span>
                        <p class="text-[10px] text-gray-500 font-medium leading-tight mb-0.5">Left</p>
                    </div>
                </div>
                
                <button type="button" @click="submitTest" class="bg-[var(--color-bg)] hover:bg-[#e8e4dc] text-[var(--color-text)] border border-[var(--color-divider)] px-6 py-2 rounded-[var(--radius-base)] font-bold transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane text-[var(--color-primary)]"></i> Submit Exam
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <form id="writing-form" action="{{ route('user.writing.submit', $attempt->id) }}" method="POST">
            @csrf

            <!-- Tasks Container -->
            <div class="space-y-12">
                @foreach($tasks as $task)
                <div class="bg-white rounded-[var(--radius-base)] shadow-sm border border-[var(--color-divider)] p-8 mb-8" x-data="wordCounter('{{ $answers[$task->id]->answer_text ?? '' }}', {{ $task->minimum_word_count }})">
                    
                    <div class="border-b border-[var(--color-divider)] pb-4 mb-6">
                        <h2 class="text-2xl font-bold text-[var(--color-text)]">Writing Task {{ $task->task_number }}</h2>
                        <span class="text-sm text-gray-500 mt-1 block font-medium">Recommended minimum words: {{ $task->minimum_word_count }}</span>
                    </div>

                    @if($task->instruction_text)
                    <div class="bg-[var(--color-bg)] text-[var(--color-text)] p-5 rounded-[var(--radius-base)] mb-6 text-sm font-medium border border-[var(--color-divider)] border-l-4 border-l-[var(--color-primary)]">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Instructions</p>
                        {{ $task->instruction_text }}
                    </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left side: Question / Prompt -->
                        <div class="prose max-w-none text-gray-700">
                            @if($task->task_title)
                                <h3 class="text-lg font-semibold">{{ $task->task_title }}</h3>
                            @endif

                            @if($task->task_description)
                                <p class="whitespace-pre-wrap">{{ $task->task_description }}</p>
                            @endif

                            @if($task->images->count() > 0)
                                <div class="my-6">
                                    <img src="{{ Storage::url($task->images->first()->image_path) }}" class="rounded shadow object-contain max-h-96 w-full bg-gray-50" />
                                </div>
                            @endif

                            @if($task->task_prompt)
                                <div class="bg-gray-100 p-6 rounded border font-medium mt-4">
                                    <p class="whitespace-pre-wrap">{{ $task->task_prompt }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Right side: Input text -->
                        <div class="flex flex-col h-full">
                            <div class="flex justify-between items-center mb-2">
                                <label class="font-semibold text-gray-700">Your Answer</label>
                                <div class="text-sm font-medium px-3 py-1 rounded" 
                                     :class="customColorClass()">
                                    Word Count: <span x-text="count"></span> / {{ $task->minimum_word_count }}
                                </div>
                            </div>
                            
                            <textarea 
                                name="answers[{{ $task->id }}]"
                                x-model="text"
                                @input="updateCount"
                                class="writing-answer-input w-full flex-grow min-h-[400px] p-5 border border-[var(--color-divider)] rounded-[var(--radius-base)] focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] bg-white resize-y text-[var(--color-text)] leading-relaxed outline-none transition-colors shadow-sm"
                                placeholder="Start typing your answer here..."
                            ></textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Hidden Submit Button for fallback -->
            <button type="submit" id="hidden-submit" class="hidden"></button>
        </form>
    </div>

    <!-- Auto-save notification anchor -->
    <div x-show="showAutosaveNotice" 
         x-transition.opacity.duration.500ms
         class="fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg text-sm flex items-center space-x-2 z-50"
         style="display: none;">
        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>Saved as draft</span>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('writingTest', (initialSeconds, autosaveUrl, submitUrl) => ({
                timeRemaining: initialSeconds,
                autosaveUrl: autosaveUrl,
                submitUrl: submitUrl,
                showAutosaveNotice: false,
                autosaveInterval: null,
                timerInterval: null,

                init() {
                    // Timer logic
                    this.timerInterval = setInterval(() => {
                        this.timeRemaining--;
                        if (this.timeRemaining <= 0) {
                            clearInterval(this.timerInterval);
                            this.forceSubmitTest();
                        }
                    }, 1000);

                    // Autosave logic (Every 10 seconds)
                    this.autosaveInterval = setInterval(() => {
                        this.autosave();
                    }, 10000);
                },

                get formattedTime() {
                    let m = Math.floor(this.timeRemaining / 60);
                    let s = this.timeRemaining % 60;
                    return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                },

                autosave() {
                    const form = document.getElementById('writing-form');
                    const formData = new FormData(form);
                    
                    // Simple fetch post to autosave endpoint
                    fetch(this.autosaveUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        if(response.ok) {
                            this.showAutosaveNotice = true;
                            setTimeout(() => this.showAutosaveNotice = false, 2000);
                        }
                    }).catch(err => console.error('Autosave failed', err));
                },

                submitTest() {
                    if(confirm("Are you sure you want to completely finish and submit your writing test? You cannot change your answers after this point.")) {
                        clearInterval(this.autosaveInterval);
                        document.getElementById('hidden-submit').click();
                    }
                },

                forceSubmitTest() {
                    alert("Time is up! Your test will now be submitted automatically.");
                    clearInterval(this.autosaveInterval);
                    document.getElementById('hidden-submit').click();
                }
            }));

            Alpine.data('wordCounter', (initialText, minWords) => ({
                text: initialText,
                count: 0,
                minWords: minWords,
                
                init() {
                    this.updateCount();
                },

                updateCount() {
                    const trimmed = this.text.trim();
                    this.count = trimmed === '' ? 0 : trimmed.split(/\s+/).length;
                },

                customColorClass() {
                    if (this.count === 0) return 'bg-gray-200 text-gray-700';
                    if (this.count < this.minWords) return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                    return 'bg-green-100 text-green-800 border border-green-200';
                }
            }));
        });
    </script>
</body>
</html>
