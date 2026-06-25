<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Category::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name', 'asc')->paginate(6)->withQueryString();

        return view('category.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $request->input('status', true);

        $category = Category::create($data);

        return redirect()->route('category.index')
            ->with('success', 'Kategori baru berhasil ditambahkan!')
            ->with('log_action', [
                'category' => 'Product Management',
                'action' => 'Create Category',
                'details' => "Membuat kategori baru: {$category->name} ({$category->slug})"
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['status'] = $request->input('status', true);

        $category->update($data);

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil diperbarui!')
            ->with('log_action', [
                'category' => 'Product Management',
                'action' => 'Update Category',
                'details' => "Mengubah kategori ID #{$category->id}: {$category->name}"
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil dihapus!')
            ->with('log_action', [
                'category' => 'Product Management',
                'action' => 'Delete Category',
                'details' => "Menghapus kategori: {$name}"
            ]);
    }
}
