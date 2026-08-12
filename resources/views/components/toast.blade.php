<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type: toast.type || 'success', message: toast.message });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
    }"
    x-on:toast.window="add($event.detail)"
    class="fixed top-4 right-4 z-[60] space-y-2 w-full max-w-sm"
>
    @if (session('success'))
        <div x-init="add({ type: 'success', message: @js(session('success')) })"></div>
    @endif

    @if (session('error'))
        <div x-init="add({ type: 'error', message: @js(session('error')) })"></div>
    @endif

    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-start gap-3 rounded-xl border shadow-lg px-4 py-3 bg-white"
            :class="{
                'border-brand-light': toast.type === 'success',
                'border-red-100': toast.type === 'error',
            }"
        >
            <svg x-show="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 text-brand mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 2.25 2.25 6-6M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <svg x-show="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 text-red-500 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>

            <p class="text-sm text-ink flex-1" x-text="toast.message"></p>

            <button type="button" x-on:click="remove(toast.id)" class="shrink-0 text-slate-400 hover:text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
