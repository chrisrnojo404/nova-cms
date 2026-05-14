<?php

use App\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function (): void {
    Route::middleware(['auth', 'verified', 'panel.access'])->prefix('admin/plugins/contact-form')->group(function (): void {
        Route::get('/', function () {
            abort_unless(
                Plugin::query()->where('slug', 'contact-form')->where('is_active', true)->exists(),
                404
            );

            return view('plugin_contact_form::admin.index');
        })->name('admin.plugins.contact-form.index');
    });

    Route::post('/plugins/contact-form/submit', function (Request $request) {
        abort_unless(
            Plugin::query()->where('slug', 'contact-form')->where('is_active', true)->exists(),
            404
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        return back()->with('plugin_status', "Thanks {$validated['name']}, your message has been received by the contact-form plugin.");
    })->name('plugins.contact-form.submit');
});
