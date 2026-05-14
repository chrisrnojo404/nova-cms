<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Menu;
use App\Models\Setting;
use App\Models\User;
use App\Support\ThemeManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, fn ($app) => new ThemeManager($app['view']));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->make(ThemeManager::class)->registerActiveThemeNamespace();

        View::composer(['welcome', 'pages.*', 'posts.*', 'theme::*'], function ($view): void {
            $defaults = [
                'site_name' => 'Nova CMS',
                'site_tagline' => 'Commercial-ready Laravel CMS foundation',
                'brand_accent' => '#22d3ee',
                'media_upload_directory' => 'media/uploads',
            ];

            if (! Schema::hasTable('menus')) {
                $view->with([
                    'headerMenu' => null,
                    'footerMenu' => null,
                    'siteSettings' => $defaults,
                ]);

                return;
            }

            $siteSettings = Schema::hasTable('settings')
                ? array_merge($defaults, [
                    'site_name' => Setting::valueFor('site_name', $defaults['site_name']),
                    'site_tagline' => Setting::valueFor('site_tagline', $defaults['site_tagline']),
                    'brand_accent' => Setting::valueFor('brand_accent', $defaults['brand_accent']),
                    'media_upload_directory' => Setting::valueFor('media_upload_directory', $defaults['media_upload_directory']),
                ])
                : $defaults;

            $view->with([
                'headerMenu' => Menu::query()
                    ->active()
                    ->where('location', 'header')
                    ->with([
                        'rootItems' => fn ($query) => $query->with('children.children'),
                        'rootItems.page',
                        'rootItems.post',
                        'rootItems.category',
                        'rootItems.children.page',
                        'rootItems.children.post',
                        'rootItems.children.category',
                        'rootItems.children.children.page',
                        'rootItems.children.children.post',
                        'rootItems.children.children.category',
                    ])
                    ->first(),
                'footerMenu' => Menu::query()
                    ->active()
                    ->where('location', 'footer')
                    ->with([
                        'rootItems' => fn ($query) => $query->with('children.children'),
                        'rootItems.page',
                        'rootItems.post',
                        'rootItems.category',
                        'rootItems.children.page',
                        'rootItems.children.post',
                        'rootItems.children.category',
                        'rootItems.children.children.page',
                        'rootItems.children.children.post',
                        'rootItems.children.children.category',
                    ])
                    ->first(),
                'siteSettings' => $siteSettings,
            ]);
        });

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
