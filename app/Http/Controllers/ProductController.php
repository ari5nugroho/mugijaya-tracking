<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Product::with('category');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->orderBy('name', 'asc')->paginate(6)->withQueryString();
        $categories = Category::where('status', true)->orderBy('name', 'asc')->get();

        return view('product.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $request->input('status', true);

        $product = Product::create($data);

        return redirect()->route('product.index')
            ->with('success', 'Produk baru berhasil ditambahkan!')
            ->with('log_action', [
                'category' => 'Product Management',
                'action' => 'Create Product',
                'details' => "Menambahkan produk baru SKU: {$product->sku} - {$product->name}"
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['status'] = $request->input('status', true);

        $product->update($data);

        return redirect()->route('product.index')
            ->with('success', 'Detail produk berhasil diperbarui!')
            ->with('log_action', [
                'category' => 'Product Management',
                'action' => 'Update Product',
                'details' => "Mengubah detail produk SKU: {$product->sku}"
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $sku = $product->sku;
        $name = $product->name;
        
        $product->delete();

        return redirect()->route('product.index')
            ->with('success', 'Produk berhasil dihapus!')
            ->with('log_action', [
                'category' => 'Product Management',
                'action' => 'Delete Product',
                'details' => "Menghapus produk SKU: {$sku} - {$name}"
            ]);
    }
}
