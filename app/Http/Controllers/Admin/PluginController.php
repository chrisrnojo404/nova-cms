<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Plugin;
use App\Support\PluginManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PluginController extends Controller
{
    public function __construct(private readonly PluginManager $pluginManager)
    {
    }

    public function index(): View
    {
        $this->pluginManager->syncDiscoveredPlugins();

        return view('admin.plugins.index', [
            'plugins' => Plugin::query()->orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function activate(Plugin $plugin): RedirectResponse
    {
        $this->pluginManager->activate($plugin);

        ActivityLog::create([
            'user_id' => request()->user()?->id,
            'event' => 'plugin.activated',
            'subject_type' => Plugin::class,
            'subject_id' => $plugin->id,
            'description' => 'Plugin activated.',
            'properties' => [
                'plugin' => $plugin->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.plugins.index')
            ->with('status', "{$plugin->name} activated successfully.");
    }

    public function deactivate(Plugin $plugin): RedirectResponse
    {
        $this->pluginManager->deactivate($plugin);

        ActivityLog::create([
            'user_id' => request()->user()?->id,
            'event' => 'plugin.deactivated',
            'subject_type' => Plugin::class,
            'subject_id' => $plugin->id,
            'description' => 'Plugin deactivated.',
            'properties' => [
                'plugin' => $plugin->slug,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('admin.plugins.index')
            ->with('status', "{$plugin->name} deactivated successfully.");
    }
}
