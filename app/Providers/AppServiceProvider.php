<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.public', \App\View\Composers\PublicLayoutComposer::class);
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $view->with([
                'unreadContactMessages' => \App\Models\ContactMessage::unread()->count(),
                'unreadEventContacts' => \App\Models\EventContact::unread()->count(),
            ]);
        });
    }
}
