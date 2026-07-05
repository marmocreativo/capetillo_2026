<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Category::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $categories = $query->orderBy('order')->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->imageUploadService->store($request->file('cover_image'), 'categories');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Categoría creada correctamente.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            if ($category->cover_image) {
                Storage::disk('public')->delete($category->cover_image);
            }

            $data['cover_image'] = $this->imageUploadService->store($request->file('cover_image'), 'categories');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->cover_image) {
            Storage::disk('public')->delete($category->cover_image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Categoría eliminada correctamente.');
    }
}