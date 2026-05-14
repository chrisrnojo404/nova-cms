<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\MenuItemController as AdminMenuItemController;
use App\Http\Controllers\Admin\PluginController as AdminPluginController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ThemeController as AdminThemeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/dashboard', function (): RedirectResponse {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'panel.access'])->name('dashboard');

Route::view('/preview', 'welcome');

Route::middleware(['auth', 'verified', 'panel.access'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');

        Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [AdminPostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [AdminPostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/media', [AdminMediaController::class, 'index'])->name('media.index');
        Route::post('/media', [AdminMediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{medium}', [AdminMediaController::class, 'destroy'])->name('media.destroy');

        Route::get('/menus', [AdminMenuController::class, 'index'])->name('menus.index');
        Route::get('/menus/create', [AdminMenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [AdminMenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [AdminMenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [AdminMenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [AdminMenuController::class, 'destroy'])->name('menus.destroy');

        Route::post('/menus/{menu}/items', [AdminMenuItemController::class, 'store'])->name('menu-items.store');
        Route::put('/menus/{menu}/items/{item}', [AdminMenuItemController::class, 'update'])->name('menu-items.update');
        Route::delete('/menus/{menu}/items/{item}', [AdminMenuItemController::class, 'destroy'])->name('menu-items.destroy');

        Route::get('/themes', [AdminThemeController::class, 'index'])->name('themes.index');
        Route::post('/themes/{theme}/activate', [AdminThemeController::class, 'activate'])->name('themes.activate');

        Route::get('/plugins', [AdminPluginController::class, 'index'])->name('plugins.index');
        Route::post('/plugins/{plugin}/activate', [AdminPluginController::class, 'activate'])->name('plugins.activate');
        Route::post('/plugins/{plugin}/deactivate', [AdminPluginController::class, 'deactivate'])->name('plugins.deactivate');

        Route::view('/users', 'admin.placeholder', [
            'title' => 'Users',
            'description' => 'User management will build on the seeded roles and permissions foundation added in this phase.',
        ])->name('users.index');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

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

Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
Route::get('/blog/category/{slug}', [PostController::class, 'category'])->name('posts.category');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|api|blog|dashboard|login|logout|register|profile|preview|verify-email|forgot-password|reset-password|sanctum|storage|up$)[A-Za-z0-9\-]+')
    ->name('pages.show');
