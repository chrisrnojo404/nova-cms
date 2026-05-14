<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Media;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                ['label' => 'Users', 'value' => User::count(), 'hint' => 'Registered accounts'],
                ['label' => 'Pages', 'value' => Page::count(), 'hint' => 'Managed content pages'],
                ['label' => 'Posts', 'value' => Post::count(), 'hint' => 'Editorial blog entries'],
                ['label' => 'Media', 'value' => Media::count(), 'hint' => 'Uploaded library assets'],
                ['label' => 'Menus', 'value' => Menu::count(), 'hint' => 'Navigation groups and placements'],
                ['label' => 'Themes', 'value' => Theme::count(), 'hint' => 'Discoverable theme records'],
                ['label' => 'Plugins', 'value' => Plugin::count(), 'hint' => 'Plugin registry entries'],
            ],
            'recentActivity' => ActivityLog::with('user')->latest()->limit(8)->get(),
            'quickActions' => [
                ['label' => 'Create a new page', 'route' => 'admin.pages.create'],
                ['label' => 'Write a new post', 'route' => 'admin.posts.create'],
                ['label' => 'Review all pages', 'route' => 'admin.pages.index'],
                ['label' => 'Manage categories', 'route' => 'admin.categories.index'],
                ['label' => 'Open media library', 'route' => 'admin.media.index'],
                ['label' => 'Build navigation menus', 'route' => 'admin.menus.index'],
                ['label' => 'Manage your profile', 'route' => 'profile.edit'],
            ],
            'settingsCount' => Setting::count(),
        ]);
    }
}
