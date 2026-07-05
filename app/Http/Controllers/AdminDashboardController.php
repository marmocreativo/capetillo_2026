<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Talent;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalTalents = Talent::count();
        $activeTalents = Talent::where('is_active', true)->count();
        $inactiveTalents = $totalTalents - $activeTalents;

        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();

        $talentsWithoutContent = Talent::where(function ($q) {
            $q->whereNull('content')->orWhere('content', '');
        })->count();

        $talentsWithoutSummary = Talent::where(function ($q) {
            $q->whereNull('summary')->orWhere('summary', '');
        })->count();

        $talentsWithoutImages = Talent::doesntHave('images')->count();

        $categoriesWithCounts = Category::withCount('talents')
            ->orderByDesc('talents_count')
            ->take(6)
            ->get();

        $recentTalents = Talent::with('categories')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalTalents',
            'activeTalents',
            'inactiveTalents',
            'totalCategories',
            'activeCategories',
            'talentsWithoutContent',
            'talentsWithoutSummary',
            'talentsWithoutImages',
            'categoriesWithCounts',
            'recentTalents'
        ));
    }
}