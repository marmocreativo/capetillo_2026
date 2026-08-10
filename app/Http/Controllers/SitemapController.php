<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Talent;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            return $this->buildXml();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    protected function buildXml(): string
    {
        $urls = collect();

        // Páginas estáticas
        $staticRoutes = [
            ['route' => 'home', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['route' => 'categories.index', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'golden-party', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'live-media', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'network', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'about', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'contact.page', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'privacy', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticRoutes as $item) {
            $urls->push([
                'loc' => route($item['route']),
                'lastmod' => now()->toAtomString(),
                'changefreq' => $item['changefreq'],
                'priority' => $item['priority'],
            ]);
        }

        // Categorías + talentos por categoría (respeta tu ruta {category:slug}/{talent:slug})
        Category::where('is_active', true)
            ->with(['talents' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('order')
            ->get()
            ->each(function (Category $category) use ($urls) {
                $urls->push([
                    'loc' => route('categories.show', $category->slug),
                    'lastmod' => optional($category->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ]);

                foreach ($category->talents as $talent) {
                    $urls->push([
                        'loc' => route('talents.show', ['category' => $category->slug, 'talent' => $talent->slug]),
                        'lastmod' => optional($talent->updated_at)->toAtomString() ?? now()->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ]);
                }
            });

        // Eventos (organizacion-de-*-en-*)
        Event::where('is_active', true)
            ->orderBy('orden')
            ->get()
            ->each(function (Event $event) use ($urls) {
                $urls->push([
                    'loc' => route('events.show', $event->slug),
                    'lastmod' => optional($event->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ]);
            });

        return view('sitemap.index', ['urls' => $urls])->render();
    }
}