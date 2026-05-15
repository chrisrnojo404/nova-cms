<div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <section class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid gap-6">
                <div>
                    <label for="name" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $managedUser->name) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    @error('name') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $managedUser->email) }}" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" required>
                    @error('email') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Security</p>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $isEditing ? 'New password' : 'Password' }}</label>
                    <input id="password" name="password" type="password" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" @if (! $isEditing) required @endif>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        {{ $isEditing ? 'Leave blank to keep the current password.' : 'Use at least 8 characters.' }}
                    </p>
                    @error('password') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white" @if (! $isEditing) required @endif>
                </div>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Access</p>
            <div class="mt-5 grid gap-5">
                <div>
                    <label for="role" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Role</label>
                    <select id="role" name="role" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(old('role', $managedUser->getRoleNames()->first()) === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-300">
                    <input type="checkbox" name="email_verified" value="1" @checked(old('email_verified', (bool) $managedUser->email_verified_at)) class="mt-1 rounded border-slate-300 text-cyan-500 focus:ring-cyan-400">
                    <span>Mark this email address as verified.</span>
                </label>
                @error('email_verified') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Account context</p>
            <div class="mt-4 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                <p>Created: {{ $managedUser->created_at?->diffForHumans() ?? 'New account' }}</p>
                <p>Last login: {{ $managedUser->last_login_at?->diffForHumans() ?? 'Never' }}</p>
                <p>Current IP: {{ $managedUser->last_login_ip ?? 'Unavailable' }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                {{ $submitLabel }}
            </button>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                Cancel
            </a>
        </div>
    </aside>
</div>
