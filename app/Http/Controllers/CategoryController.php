<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController
{
    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);

        return view('products.index', [
            'category' => $category,
        ]);
    }
}
