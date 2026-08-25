<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas fijas
|--------------------------------------------------------------------------
*/
Route::get('/', [\App\Http\Controllers\TalentoController::class, 'home'])->name('home');

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');


// Tarjetas digitales
Route::view('/tarjeta/alberto-capetillo', 'public.tarjeta-alberto')->name('tarjeta.alberto');
Route::get('/tarjeta/alberto-capetillo/vcard', [\App\Http\Controllers\PublicVCardController::class, 'alberto'])
    ->name('tarjeta.alberto.vcard');

Route::view('/tarjeta/paulina-capetillo', 'public.tarjeta-paulina')->name('tarjeta.paulina');
Route::get('/tarjeta/paulina-capetillo/vcard', [\App\Http\Controllers\PublicVCardController::class, 'paulina'])
    ->name('tarjeta.paulina.vcard');

Route::view('/tarjeta/carlos-jaime', 'public.tarjeta-carlos')->name('tarjeta.carlos');

Route::get('/tarjeta/carlos-jaime/vcard', [\App\Http\Controllers\PublicVCardController::class, 'carlos'])
    ->name('tarjeta.carlos.vcard');

// sitio
Route::get('/talento', [\App\Http\Controllers\TalentoController::class, 'index'])->name('categories.index');
Route::get('/buscar', [\App\Http\Controllers\TalentoController::class, 'search'])->name('search');
Route::get('/golden-party', [\App\Http\Controllers\GoldenPartyController::class, 'index'])->name('golden-party');
Route::view('/live-media', 'public.live-media')->name('live-media');
Route::view('/capetillo-network', 'public.capetillo-network')->name('network');
Route::view('/quienes-somos', 'public.quienes-somos')->name('about');
Route::view('/contacto', 'public.contact')->name('contact.page');
Route::post('/contacto', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
Route::post('/eventos/contacto', [\App\Http\Controllers\EventContactController::class, 'store'])->name('event-contacts.store');
Route::view('/aviso-de-privacidad', 'public.privacy')->name('privacy');
Route::get('/roster/{roster:public_token}', [\App\Http\Controllers\PublicRosterController::class, 'show'])->name('roster.public');

Route::get('/cotizacion/{contactMessage:public_token}', [\App\Http\Controllers\PublicContactMessageController::class, 'show'])
    ->name('contact-messages.public.show');
Route::post('/cotizacion/{contactMessage:public_token}', [\App\Http\Controllers\PublicContactMessageController::class, 'update'])
    ->name('contact-messages.public.update');


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

    Route::post('talents/{talent}/update-honorarios', [\App\Http\Controllers\AdminTalentController::class, 'updateHonorarios'])
    ->name('talents.update-honorarios');

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
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::post('contact-messages/{contactMessage}/talents', [\App\Http\Controllers\AdminContactMessageController::class, 'addTalents'])
        ->name('contact-messages.talents.store');
    Route::put('contact-messages/{contactMessage}/talents/{contactMessageTalent}', [\App\Http\Controllers\AdminContactMessageController::class, 'updateTalent'])
        ->name('contact-messages.talents.update');
    Route::delete('contact-messages/{contactMessage}/talents/{contactMessageTalent}', [\App\Http\Controllers\AdminContactMessageController::class, 'removeTalent'])
        ->name('contact-messages.talents.destroy');
    Route::get('contact-messages/{contactMessage}/export', [\App\Http\Controllers\AdminContactMessageExportController::class, 'export'])
        ->name('contact-messages.export');

    Route::resource('users', \App\Http\Controllers\AdminUserController::class);

    Route::resource('events', \App\Http\Controllers\AdminEventController::class);

    Route::get('event-contacts', [\App\Http\Controllers\AdminEventContactController::class, 'index'])->name('event-contacts.index');
    Route::get('event-contacts/{eventContact}', [\App\Http\Controllers\AdminEventContactController::class, 'show'])->name('event-contacts.show');
    Route::put('event-contacts/{eventContact}', [\App\Http\Controllers\AdminEventContactController::class, 'update'])->name('event-contacts.update');
    Route::delete('event-contacts/{eventContact}', [\App\Http\Controllers\AdminEventContactController::class, 'destroy'])->name('event-contacts.destroy');
    Route::post('events/{event}/generate-content', [\App\Http\Controllers\AdminEventContentController::class, 'generate'])
    ->name('events.generate-content');

    Route::get('search-history', [\App\Http\Controllers\AdminSearchHistoryController::class, 'index'])->name('search-history.index');
    Route::delete('search-history/bulk-destroy', [\App\Http\Controllers\AdminSearchHistoryController::class, 'bulkDestroy'])->name('search-history.bulk-destroy');
    Route::delete('search-history/{searchHistory}', [\App\Http\Controllers\AdminSearchHistoryController::class, 'destroy'])->name('search-history.destroy');

    Route::resource('logos-roster', \App\Http\Controllers\AdminLogoRosterController::class)
        ->only(['index', 'store', 'destroy'])
        ->parameters(['logos-roster' => 'logosRoster']);

    Route::get('settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/run-command', [\App\Http\Controllers\AdminSettingsController::class, 'runCommand'])->name('settings.run-command');
});

/*
|--------------------------------------------------------------------------
| Rutas públicas por slug (deben ir al final, son comodín)
|--------------------------------------------------------------------------
*/
Route::get('/{event:slug}', [\App\Http\Controllers\EventController::class, 'show'])
    ->name('events.show')
    ->where('event', 'organizacion-de-.+-en-[a-z0-9\-]+');

Route::get('/{category:slug}', [\App\Http\Controllers\TalentoController::class, 'showCategory'])->name('categories.show');
Route::get('/{category:slug}/{talent:slug}', [\App\Http\Controllers\TalentoController::class, 'showTalent'])->name('talents.show')->withoutScopedBindings();