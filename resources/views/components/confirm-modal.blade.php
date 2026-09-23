<div
    x-data="{
        open: false,
        title: 'Konfirmasi Aksi',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        variant: 'danger',
        action: '',
        method: 'POST',
        formId: '',
        loading: false,

        show(detail) {
            this.title = detail.title || 'Konfirmasi Aksi';
            this.message = detail.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.confirmText = detail.confirmText || 'Ya, Lanjutkan';
            this.cancelText = detail.cancelText || 'Batal';
            this.variant = detail.variant || 'danger';
            this.action = detail.action || '';
            this.method = (detail.method || 'POST').toUpperCase();
            this.formId = detail.formId || '';
            this.loading = false;
            this.open = true;
        },

        close() {
            if (this.loading) return;
            this.open = false;
        },

        confirm() {
            if (this.loading) return;
            this.loading = true;

            if (this.formId) {
                const targetForm = document.getElementById(this.formId);
                if (targetForm) {
                    targetForm.submit();
                    return;
                }
            }

            if (this.action) {
                const form = document.getElementById('global-confirm-form');
                const methodInput = document.getElementById('global-confirm-form-method');
                form.action = this.action;
                methodInput.value = this.method;
                form.submit();
                return;
            }

            this.open = false;
            this.loading = false;
        }
    }"
    @open-confirm-modal.window="show($event.detail)"
    @keydown.escape.window="close()"
    x-show="open"
    x-cloak
    class="relative z-50"
    aria-labelledby="confirm-modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        @click="close()"
    ></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 flex min-h-full items-center justify-center text-center">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.outside="close()"
            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl border border-slate-200/80 transition-all w-full max-w-md p-6 sm:p-7"
        >
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <template x-if="variant === 'danger'">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 ring-1 ring-rose-200/80">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="variant === 'warning'">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-200/80">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="variant === 'sky' || variant === 'key'">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-[#033F63] ring-1 ring-sky-200/80">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="variant === 'primary' || variant === 'info'">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-[#033F63] ring-1 ring-slate-200/80">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="16" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                            </svg>
                        </div>
                    </template>
                </div>

                <div class="flex-1 min-w-0">
                    <h3 id="confirm-modal-title" class="text-base font-extrabold text-slate-900 tracking-tight" x-text="title"></h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500" x-text="message"></p>
                </div>
            </div>

            <div class="mt-6 sm:mt-7 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                <button
                    type="button"
                    @click="close()"
                    :disabled="loading"
                    class="inline-flex h-10 w-full sm:w-auto items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer select-none disabled:opacity-50"
                    x-text="cancelText"
                ></button>

                <button
                    type="button"
                    @click="confirm()"
                    :disabled="loading"
                    class="inline-flex h-10 w-full sm:w-auto items-center justify-center gap-2 rounded-xl px-4 text-xs font-bold text-white shadow-xs transition cursor-pointer select-none disabled:opacity-50"
                    :class="{
                        'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-rose-600/20': variant === 'danger',
                        'bg-amber-600 hover:bg-amber-700 active:bg-amber-800 shadow-amber-600/20': variant === 'warning',
                        'bg-[#033F63] hover:bg-[#022B44] active:bg-[#011724] shadow-[#033F63]/20': variant === 'primary' || variant === 'sky' || variant === 'key' || variant === 'info'
                    }"
                >
                    <svg x-show="loading" class="h-4 w-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<form id="global-confirm-form" method="POST" action="" style="display: none;">
    @csrf
    <input type="hidden" name="_method" id="global-confirm-form-method" value="POST">
</form>
