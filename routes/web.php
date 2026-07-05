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

/*
|--------------------------------------------------------------------------
| Rutas de administración (requieren estar logueado, sin roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('home-slides', \App\Http\Controllers\AdminHomeSlideController::class)->except(['show']);

    Route::resource('categories', \App\Http\Controllers\AdminCategoryController::class);
    Route::resource('talents', \App\Http\Controllers\AdminTalentController::class);

    Route::post('talents/{talent}/generate-content', [\App\Http\Controllers\AdminTalentContentController::class, 'generate'])
    ->name('talents.generate-content');

    Route::post('talents/{talent}/generate-extra', [\App\Http\Controllers\AdminTalentContentController::class, 'generateExtra'])
    ->name('talents.generate-extra');

    Route::get('talents/batch/all-ids', [\App\Http\Controllers\AdminTalentContentController::class, 'allIds'])
    ->name('talents.all-ids');
});

/*
|--------------------------------------------------------------------------
| Rutas públicas por slug (deben ir al final, son comodín)
|--------------------------------------------------------------------------
*/
Route::get('/{category:slug}', [\App\Http\Controllers\TalentoController::class, 'showCategory'])->name('categories.show');
Route::get('/{category:slug}/{talent:slug}', [\App\Http\Controllers\TalentoController::class, 'showTalent'])->name('talents.show')->withoutScopedBindings();