<div
    x-data="toastSystem()"
    @notify.window="addToast($event.detail.message, $event.detail.type)"
    class="fixed top-6 right-6 z-[100] flex flex-col space-y-4 w-96 max-w-full pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-10 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-10 scale-95"
            class="pointer-events-auto rounded-[var(--radius-base)] p-[16px] shadow-lg flex items-start space-x-3 text-[var(--color-white)]"
            :class="{
                'bg-[var(--color-success)]': toast.type === 'success',
                'bg-[var(--color-error)]': toast.type === 'error',
                'bg-[var(--color-primary)]': toast.type === 'info',
            }"
        >
            <div class="flex-shrink-0 mt-0.5">
                <i x-show="toast.type === 'success'" class="fas fa-check-circle text-[20px]"></i>
                <i x-show="toast.type === 'error'" class="fas fa-exclamation-circle text-[20px]"></i>
                <i x-show="toast.type === 'info'" class="fas fa-info-circle text-[20px]"></i>
            </div>
            <div class="flex-1 w-0">
                <p class="text-[14px] font-bold leading-relaxed" x-text="toast.message"></p>
            </div>
            <div class="flex-shrink-0 ml-4">
                <button @click="removeToast(toast.id)" class="text-white opacity-70 hover:opacity-100 transition-opacity focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastSystem', () => ({
            toasts: [],
            addToast(message, type = 'success') {
                const id = Date.now() + Math.random().toString(36).substr(2, 9);
                this.toasts.push({ id, message, type, show: true });
                
                setTimeout(() => {
                    this.removeToast(id);
                }, 4000); // Auto-dismiss after 4 seconds
            },
            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300); // Wait for exit animation
                }
            }
        }));
    });
    
    // Global helper object for non-alpine scripts to summon toasts
    window.Toast = {
        show: function(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: message, type: type }
            }));
        },
        success: function(message) { this.show(message, 'success'); },
        error: function(message) { this.show(message, 'error'); },
        info: function(message) { this.show(message, 'info'); }
    };
</script>
