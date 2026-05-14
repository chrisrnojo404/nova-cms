<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-600 dark:text-cyan-400">Plugin admin</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Contact Form</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Plugin-owned admin area demonstrating menu registration and hook-driven dashboard integration.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Shortcode usage</p>
            <div class="mt-5 rounded-2xl bg-slate-100 px-4 py-4 text-sm text-slate-700 dark:bg-slate-950 dark:text-slate-200">
                <code>[contact_form]</code>
            </div>
            <p class="mt-4 text-sm leading-7 text-slate-500 dark:text-slate-400">
                Add the shortcode to any page or post body and the active plugin system will render the contact form block automatically.
            </p>
        </section>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Plugin routes</p>
            <dl class="mt-5 space-y-3 text-sm text-slate-500 dark:text-slate-400">
                <div class="flex items-center justify-between gap-4">
                    <dt>Public submit route</dt>
                    <dd>/plugins/contact-form/submit</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt>Admin route</dt>
                    <dd>/admin/plugins/contact-form</dd>
                </div>
            </dl>
        </aside>
    </div>
</x-app-layout>
