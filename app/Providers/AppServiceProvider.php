<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app('events')->listen(Registered::class, function (Registered $event): void {
            if ($event->user instanceof User && ! $event->user->hasRole('author')) {
                $event->user->assignRole('author');
            }

            ActivityLog::create([
                'user_id' => $event->user->id,
                'event' => 'user.registered',
                'subject_type' => User::class,
                'subject_id' => $event->user->id,
                'description' => 'A new user account was created.',
                'properties' => [
                    'email' => $event->user->email,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        app('events')->listen(Login::class, function (Login $event): void {
            if (! $event->user instanceof User) {
                return;
            }

            $event->user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ])->saveQuietly();

            ActivityLog::create([
                'user_id' => $event->user->id,
                'event' => 'auth.login',
                'subject_type' => User::class,
                'subject_id' => $event->user->id,
                'description' => 'User logged into the Nova CMS admin panel.',
                'properties' => [
                    'guard' => $event->guard,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        app('events')->listen(Logout::class, function (Logout $event): void {
            if (! $event->user instanceof User) {
                return;
            }

            ActivityLog::create([
                'user_id' => $event->user->id,
                'event' => 'auth.logout',
                'subject_type' => User::class,
                'subject_id' => $event->user->id,
                'description' => 'User logged out of the Nova CMS admin panel.',
                'properties' => [
                    'guard' => $event->guard,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
