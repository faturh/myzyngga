<div
    x-data="{ 
        show: false, 
        message: '', 
        type: 'info',
        timeout: null,
        showToast(detail) {
            this.message = detail.message || '';
            this.type = detail.type || 'info';
            this.show = true;
            
            if (this.timeout) clearTimeout(this.timeout);
            this.timeout = setTimeout(() => { this.show = false }, detail.duration || 3000);
        }
    }"
    @toast.window="showToast($event.detail)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-8"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-8"
    class="fixed bottom-24 left-1/2 -translate-x-1/2 z-[9999] w-[calc(100%-40px)] max-w-md"
    style="display: none;"
>
    <div 
        class="flex items-center gap-3 p-4 rounded-2xl shadow-[0_0_24px_rgba(0,0,0,0.08)] backdrop-blur-md bg-white"
    >
        <div 
            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
            :class="{
                'bg-zyngga-blue-50 text-zyngga-blue-300': type === 'info',
                'bg-red-100 text-red-500': type === 'error',
                'bg-green-100 text-green-500': type === 'success',
                'bg-amber-100 text-amber-500': type === 'warning'
            }"
        >
            {{-- Error Icon --}}
            <svg x-show="type === 'error'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16.01"></line>
            </svg>
            
            {{-- Success Icon --}}
            <svg x-show="type === 'success'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>

            {{-- Warning Icon (Alert Triangle) --}}
            <svg x-show="type === 'warning'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-0.5">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12" y2="17.01"></line>
            </svg>

            {{-- Info Icon --}}
            <svg x-show="type === 'info'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="8.01"></line>
            </svg>
        </div>

        <div class="flex-1">
            <x-zyngga-text variant="sm" weight="medium" ::class="{
                'text-zyngga-neutral-900': type === 'info',
                'text-red-900': type === 'error',
                'text-green-900': type === 'success',
                'text-amber-900': type === 'warning'
            }">
                <span x-text="message"></span>
            </x-zyngga-text>
        </div>

        <button @click="show = false" class="text-neutral-400 hover:text-neutral-600 transition-colors">
            <i data-feather="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof feather !== 'undefined') feather.replace();
    });
</script>
