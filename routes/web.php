<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/dashboard', function (): RedirectResponse {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'panel.access'])->name('dashboard');

Route::view('/preview', 'welcome');

Route::middleware(['auth', 'verified', 'panel.access'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::view('/pages', 'admin.placeholder', [
            'title' => 'Pages',
            'description' => 'The Pages module lands in Phase 2 with templates, revisions, SEO fields, and publishing controls.',
        ])->name('pages.index');

        Route::view('/posts', 'admin.placeholder', [
            'title' => 'Posts',
            'description' => 'The Blog module is queued for Phase 2 with drafts, scheduling, categories, and featured media.',
        ])->name('posts.index');

        Route::view('/categories', 'admin.placeholder', [
            'title' => 'Categories',
            'description' => 'Taxonomy management arrives with the blog and content modules in the next phase.',
        ])->name('categories.index');

        Route::view('/media', 'admin.placeholder', [
            'title' => 'Media Library',
            'description' => 'Media uploads, folders, previews, and file optimization are planned for Phase 3.',
        ])->name('media.index');

        Route::view('/menus', 'admin.placeholder', [
            'title' => 'Menus',
            'description' => 'Drag-and-drop navigation management will be added once page and post content models are in place.',
        ])->name('menus.index');

        Route::view('/themes', 'admin.placeholder', [
            'title' => 'Themes',
            'description' => 'Theme discovery and activation are scaffolded in the codebase and become interactive in Phase 4.',
        ])->name('themes.index');

        Route::view('/plugins', 'admin.placeholder', [
            'title' => 'Plugins',
            'description' => 'The plugin lifecycle, hooks, and widgets are prepared structurally and land in Phase 5.',
        ])->name('plugins.index');

        Route::view('/users', 'admin.placeholder', [
            'title' => 'Users',
            'description' => 'User management will build on the seeded roles and permissions foundation added in this phase.',
        ])->name('users.index');

        Route::view('/settings', 'admin.placeholder', [
            'title' => 'Settings',
            'description' => 'General, branding, email, and reading settings are backed by the settings table and will get forms next.',
        ])->name('settings.index');

        Route::view('/seo', 'admin.placeholder', [
            'title' => 'SEO',
            'description' => 'Meta management, sitemaps, robots, and canonical handling are scheduled for Phase 6.',
        ])->name('seo.index');

        Route::view('/logs', 'admin.placeholder', [
            'title' => 'Activity Logs',
            'description' => 'Authentication and registration events are already tracked. A full audit viewer follows with the user module.',
        ])->name('logs.index');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
