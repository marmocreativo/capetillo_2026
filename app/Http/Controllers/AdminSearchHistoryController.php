<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSearchHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SearchHistory::query()->latest();

        if ($search = $request->input('search')) {
            $query->where('query', 'like', "%{$search}%");
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $searches = $query->paginate(20)->withQueryString();

        $from = $request->input('from')
            ? \Illuminate\Support\Carbon::parse($request->input('from'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->input('to')
            ? \Illuminate\Support\Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $topSearches = SearchHistory::select(
                'normalized_query',
                DB::raw('count(*) as total'),
                DB::raw('MAX(query) as ejemplo'),
                DB::raw('AVG(results_count) as promedio_resultados'),
                DB::raw('SUM(CASE WHEN results_count = 0 THEN 1 ELSE 0 END) as sin_resultados')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('normalized_query')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        return view('admin.search-history.index', compact('searches', 'topSearches', 'from', 'to'));
    }

    public function destroy(SearchHistory $searchHistory)
    {
        $searchHistory->delete();

        return redirect()->route('admin.search-history.index')->with('status', 'Registro eliminado correctamente.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.search-history.index')->with('status', 'No seleccionaste ningún registro.');
        }

        $count = SearchHistory::whereIn('id', $ids)->count();
        SearchHistory::whereIn('id', $ids)->delete();

        return redirect()->route('admin.search-history.index')->with('status', "{$count} registro(s) eliminado(s) correctamente.");
    }
}