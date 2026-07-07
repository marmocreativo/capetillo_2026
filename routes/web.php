<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas fijas
|--------------------------------------------------------------------------
*/
Route::get('/', [\App\Http\Controllers\TalentoController::class, 'home'])->name('home');

Route::get('/talento', [\App\Http\Controllers\TalentoController::class, 'index'])->name('categories.index');
Route::get('/buscar', [\App\Http\Controllers\TalentoController::class, 'search'])->name('search');
Route::view('/golden-party', 'public.golden-party')->name('golden-party');
Route::view('/live-media', 'public.live-media')->name('live-media');
Route::view('/contacto', 'public.contact')->name('contact.page');
Route::post('/contacto', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
Route::view('/aviso-de-privacidad', 'public.privacy')->name('privacy');
Route::get('/roster/{roster:public_token}', [\App\Http\Controllers\PublicRosterController::class, 'show'])->name('roster.public');


/*
|--------------------------------------------------------------------------
| Rutas de administración (requieren estar logueado, sin roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('home-slides', \App\Http\Controllers\AdminHomeSlideController::class)->except(['show']);
    Route::post('home-slides/reorder', [\App\Http\Controllers\AdminHomeSlideController::class, 'reorder'])
    ->name('home-slides.reorder');

    Route::resource('categories', \App\Http\Controllers\AdminCategoryController::class);
    Route::resource('talents', \App\Http\Controllers\AdminTalentController::class);
    Route::resource('rosters', \App\Http\Controllers\AdminRosterController::class);
    Route::get('rosters/{roster}/export', [\App\Http\Controllers\AdminRosterExportController::class, 'export'])
    ->name('rosters.export');

    Route::post('talents/reorder', [\App\Http\Controllers\AdminTalentController::class, 'reorder'])
    ->name('talents.reorder');

    Route::post('talents/{talent}/toggle-active', [\App\Http\Controllers\AdminTalentController::class, 'toggleActive'])
    ->name('talents.toggle-active');

    Route::post('talents/{talent}/toggle-destacado', [\App\Http\Controllers\AdminTalentController::class, 'toggleDestacado'])
    ->name('talents.toggle-destacado');

    Route::post('talents/{talent}/generate-content', [\App\Http\Controllers\AdminTalentContentController::class, 'generate'])
    ->name('talents.generate-content');

    Route::post('talents/{talent}/generate-extra', [\App\Http\Controllers\AdminTalentContentController::class, 'generateExtra'])
    ->name('talents.generate-extra');

    Route::post('talents/{talent}/generate-studio-image', [\App\Http\Controllers\AdminTalentContentController::class, 'generateStudioImage'])
    ->name('talents.generate-studio-image');

    Route::get('talents/batch/all-ids', [\App\Http\Controllers\AdminTalentContentController::class, 'allIds'])
    ->name('talents.all-ids');

    Route::delete('contact-messages/bulk-destroy', [\App\Http\Controllers\AdminContactMessageController::class, 'bulkDestroy'])
    ->name('contact-messages.bulk-destroy');

    Route::resource('contact-messages', \App\Http\Controllers\AdminContactMessageController::class)
        ->only(['index', 'edit', 'update', 'destroy']);

    Route::resource('users', \App\Http\Controllers\AdminUserController::class);

    Route::get('settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/run-command', [\App\Http\Controllers\AdminSettingsController::class, 'runCommand'])->name('settings.run-command');
});

/*
|--------------------------------------------------------------------------
| Rutas públicas por slug (deben ir al final, son comodín)
|--------------------------------------------------------------------------
*/
Route::get('/{category:slug}', [\App\Http\Controllers\TalentoController::class, 'showCategory'])->name('categories.show');
Route::get('/{category:slug}/{talent:slug}', [\App\Http\Controllers\TalentoController::class, 'showTalent'])->name('talents.show')->withoutScopedBindings();