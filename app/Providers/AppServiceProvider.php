<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Menu;
use App\Models\Setting;
use App\Models\User;
use App\Support\CmsCache;
use App\Support\PluginManager;
use App\Support\SeoManager;
use App\Support\ThemeManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->app->singleton(PluginManager::class, fn ($app) => new PluginManager($app['view']));
        $this->app->singleton(SeoManager::class, fn () => new SeoManager());
        $this->app->singleton(CmsCache::class, fn () => new CmsCache());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->app->make(ThemeManager::class)->registerActiveThemeNamespace();
        $pluginManager = $this->app->make(PluginManager::class);
        $cmsCache = $this->app->make(CmsCache::class);
        $pluginManager->registerDiscoveredPluginNamespaces();
        $pluginManager->syncDiscoveredPlugins();
        $pluginManager->registerActivePluginRoutes();
        $pluginManager->loadActivePluginBootstraps();
        $pluginManager->registerDefaultShortcodes();

        View::composer(['welcome', 'pages.*', 'posts.*', 'theme::*'], function ($view) use ($cmsCache): void {
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

            $siteSettings = $cmsCache->siteSettings($defaults);
            $seoSettings = $cmsCache->seoSettings(fn (): array => $this->app->make(SeoManager::class)->settings());

            $view->with([
                'headerMenu' => $cmsCache->menu('header'),
                'footerMenu' => $cmsCache->menu('footer'),
                'siteSettings' => $siteSettings,
                'seoSettings' => $seoSettings,
            ]);
        });

        View::composer('layouts.navigation', function ($view) use ($pluginManager): void {
            $pluginItems = $pluginManager->runHook('admin.navigation.items', $pluginManager->adminNavigationItems());

            $view->with([
                'pluginNavigationItems' => is_array($pluginItems) ? $pluginItems : [],
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
