<?php

namespace App\View\Composers;

use App\Models\Event;
use Illuminate\View\View;

class PublicLayoutComposer
{
    public function compose(View $view): void
    {
        $view->with('headerEvents', Event::where('is_active', true)
            ->orderBy('orden')
            ->orderBy('title')
            ->get(['title', 'slug']));
    }
}