<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                ['label' => 'Users', 'value' => User::count(), 'hint' => 'Registered accounts'],
                ['label' => 'Roles', 'value' => Role::count(), 'hint' => 'Access profiles seeded'],
                ['label' => 'Themes', 'value' => Theme::count(), 'hint' => 'Discoverable theme records'],
                ['label' => 'Plugins', 'value' => Plugin::count(), 'hint' => 'Plugin registry entries'],
                ['label' => 'Logs', 'value' => ActivityLog::count(), 'hint' => 'Audit events captured'],
            ],
            'recentActivity' => ActivityLog::with('user')->latest()->limit(8)->get(),
            'quickActions' => [
                ['label' => 'Review theme registry', 'route' => 'admin.themes.index'],
                ['label' => 'Inspect plugin architecture', 'route' => 'admin.plugins.index'],
                ['label' => 'Open settings foundation', 'route' => 'admin.settings.index'],
                ['label' => 'Manage your profile', 'route' => 'profile.edit'],
            ],
            'settingsCount' => Setting::count(),
        ]);
    }
}
