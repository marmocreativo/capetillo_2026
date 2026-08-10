<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Talent;
use App\Models\HomeSlide;
use App\Models\SearchHistory;

class TalentoController extends Controller
{
    public function home()
    {
        $featuredTalents = Talent::with('categories')
            ->where('is_active', true)
            ->orderByDesc('destacado')
            ->orderBy('orden')
            ->orderBy('name')
            ->limit(12)
            ->get();

        $homeSlides = HomeSlide::where('is_active', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('public.home', compact('featuredTalents', 'homeSlides'));
    }

    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('public.categories.index', compact('categories'));
    }

    public function showCategory(Category $category)
    {
        abort_unless($category->is_active, 404);

        $talents = $category->talents()
            ->where('is_active', true)
            ->orderByDesc('destacado')
            ->orderBy('orden')
            ->orderBy('name')
            ->get();

        $featuredTalents = $talents->where('destacado', true)->values();
        $regularTalents = $talents->where('destacado', false)->values();

        return view('public.categories.show', compact('category', 'talents', 'featuredTalents', 'regularTalents'));
    }

    public function showTalent(Category $category, Talent $talent)
    {
        abort_unless($category->is_active && $talent->is_active, 404);

        $belongsToCategory = $talent->categories()->where('categories.id', $category->id)->exists();
        abort_unless($belongsToCategory, 404);

        $talent->load('images', 'videos');

        $otherTalents = $category->talents()
            ->where('talents.id', '!=', $talent->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return view('public.talents.show', compact('category', 'talent', 'otherTalents'));
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $talents = collect();

        if ($query !== '') {
            $talents = Talent::with('categories')
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhereHas('categories', function ($cq) use ($query) {
                        $cq->where('name', 'like', "%{$query}%");
                    });
                })
                ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["%{$query}%"])
                ->orderBy('name')
                ->get();

            SearchHistory::log($query, $talents->count(), $request->ip());
        }

        return view('public.search.results', compact('talents', 'query'));
    }
}