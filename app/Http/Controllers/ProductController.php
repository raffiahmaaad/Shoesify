<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Request $request, Product $product): View
    {
        $product->load(['brand', 'category', 'variants']);

        $relatedProducts = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'discount', 'images', 'rating'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->where('is_active', true)
            ->limit(6)
            ->get();

        $recentlyViewed = $this->storeRecentlyViewed($request, $product);

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'recentlyViewed' => $recentlyViewed,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Product>
     */
    protected function storeRecentlyViewed(Request $request, Product $product): Collection
    {
        $sessionKey = 'recently_viewed_products';
        $recent = collect($request->session()->get($sessionKey, []))
            ->prepend($product->id)
            ->unique()
            ->take(10)
            ->values();

        $request->session()->put($sessionKey, $recent->all());

        if ($recent->count() <= 1) {
            return collect();
        }

        return Product::query()
            ->select(['id', 'name', 'slug', 'price', 'discount', 'images', 'rating'])
            ->whereIn('id', $recent->reject(fn ($id) => $id === $product->id)->take(6))
            ->get();
    }
}

