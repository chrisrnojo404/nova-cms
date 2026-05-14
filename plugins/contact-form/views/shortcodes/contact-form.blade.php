<section class="mt-8 rounded-[2rem] border border-cyan-200 bg-cyan-50/60 p-6 shadow-sm dark:border-cyan-900/50 dark:bg-cyan-950/20">
    <div class="flex flex-col gap-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700 dark:text-cyan-300">Plugin shortcode</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Reach Out</h2>
            <p class="mt-2 text-sm leading-7 text-slate-600 dark:text-slate-300">This form is rendered by the active `contact-form` plugin via the `[contact_form]` shortcode.</p>
        </div>

        @if (session('plugin_status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('plugin_status') }}
            </div>
        @endif

        <form method="POST" action="{{ url('/plugins/contact-form/submit') }}" class="grid gap-4">
            @csrf
            <div>
                <label for="plugin-contact-name" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Name</label>
                <input id="plugin-contact-name" name="name" type="text" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label for="plugin-contact-email" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Email</label>
                <input id="plugin-contact-email" name="email" type="email" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
            </div>
            <div>
                <label for="plugin-contact-message" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Message</label>
                <textarea id="plugin-contact-message" name="message" rows="4" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required></textarea>
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                Send message
            </button>
        </form>
    </div>
</section>
