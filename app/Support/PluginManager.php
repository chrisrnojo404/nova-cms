<?php

namespace App\Support;

use App\Models\Plugin;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PluginManager
{
    private array $shortcodes = [];

    private array $hooks = [];

    private array $adminNavigationItems = [];

    public function __construct(private readonly ViewFactory $view)
    {
    }

    public function discoverPlugins(): array
    {
        $plugins = [];

        foreach (File::directories(base_path('plugins')) as $directory) {
            $slug = basename($directory);
            $manifestPath = $directory.'/plugin.json';

            if (! File::exists($manifestPath)) {
                continue;
            }

            $manifest = json_decode((string) File::get($manifestPath), true);

            if (! is_array($manifest)) {
                continue;
            }

            $plugins[] = [
                'name' => $manifest['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'version' => $manifest['version'] ?? '1.0.0',
                'author' => $manifest['author'] ?? null,
                'description' => $manifest['description'] ?? null,
                'path' => 'plugins/'.$slug,
                'is_active' => false,
                'meta' => $manifest,
            ];
        }

        return $plugins;
    }

    public function syncDiscoveredPlugins(): void
    {
        if (! Schema::hasTable('plugins')) {
            return;
        }

        foreach ($this->discoverPlugins() as $plugin) {
            Plugin::updateOrCreate(
                ['slug' => $plugin['slug']],
                array_diff_key($plugin, ['is_active' => true])
            );
        }
    }

    public function registerDiscoveredPluginNamespaces(): void
    {
        foreach ($this->discoverPlugins() as $plugin) {
            $viewsPath = base_path($plugin['path'].'/views');

            if (is_dir($viewsPath)) {
                $this->view->addNamespace($this->viewNamespace($plugin['slug']), $viewsPath);
            }
        }
    }

    public function registerActivePluginRoutes(): void
    {
        if (! Schema::hasTable('plugins') || app()->routesAreCached()) {
            return;
        }

        foreach ($this->discoverPlugins() as $plugin) {
            $routePath = base_path($plugin['path'].'/routes.php');

            if (File::exists($routePath)) {
                require $routePath;
            }
        }
    }

    public function registerShortcode(string $shortcode, callable $handler): void
    {
        $this->shortcodes[$shortcode] = $handler;
    }

    public function registerAdminNavigationItem(array $item): void
    {
        $defaults = [
            'label' => 'Plugin',
            'route' => 'admin.plugins.index',
            'pattern' => 'admin.plugins.*',
            'order' => 100,
        ];

        $this->adminNavigationItems[] = array_merge($defaults, $item);
    }

    public function adminNavigationItems(): array
    {
        $items = $this->adminNavigationItems;

        usort($items, static fn (array $left, array $right): int => ($left['order'] ?? 100) <=> ($right['order'] ?? 100));

        return $items;
    }

    public function addHook(string $hook, callable $handler): void
    {
        $this->hooks[$hook][] = $handler;
    }

    public function runHook(string $hook, mixed $payload = null): mixed
    {
        foreach ($this->hooks[$hook] ?? [] as $handler) {
            $payload = $handler($payload);
        }

        return $payload;
    }

    public function registerDefaultShortcodes(): void
    {
        if (! Schema::hasTable('plugins')) {
            return;
        }

        foreach (Plugin::query()->where('is_active', true)->get() as $plugin) {
            $shortcodes = $plugin->meta['shortcodes'] ?? [];

            foreach ($shortcodes as $shortcode) {
                $view = $this->viewNamespace($plugin->slug).'::shortcodes.'.Str::of($shortcode)->replace('_', '-');

                if ($this->view->exists($view)) {
                    $this->registerShortcode(
                        $shortcode,
                        fn (): string => view($view, ['plugin' => $plugin])->render()
                    );
                }
            }
        }
    }

    public function renderContent(?string $content): string
    {
        $content = $content ?? '';

        if ($content === '') {
            return $content;
        }

        $rendered = preg_replace_callback('/\[([a-z0-9_\-]+)\]/i', function (array $matches): string {
            $shortcode = $matches[1];

            if (! isset($this->shortcodes[$shortcode])) {
                return $matches[0];
            }

            return (string) call_user_func($this->shortcodes[$shortcode]);
        }, $content);

        return (string) $this->runHook('content.rendered', $rendered);
    }

    public function loadActivePluginBootstraps(): void
    {
        if (! Schema::hasTable('plugins')) {
            return;
        }

        foreach (Plugin::query()->where('is_active', true)->get() as $plugin) {
            $bootstrapPath = base_path($plugin->path.'/services/bootstrap.php');

            if (! File::exists($bootstrapPath)) {
                continue;
            }

            $bootstrap = require $bootstrapPath;

            if (is_callable($bootstrap)) {
                $bootstrap($this, $plugin);
            }
        }
    }

    public function activate(Plugin $plugin): void
    {
        $plugin->update(['is_active' => true]);
    }

    public function deactivate(Plugin $plugin): void
    {
        $plugin->update(['is_active' => false]);
    }

    public function viewNamespace(string $slug): string
    {
        return 'plugin_'.str_replace('-', '_', $slug);
    }
}
