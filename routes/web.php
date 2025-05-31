<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('layouts/main');
});

Route::resource('products', ProductController::class)->names([
    'index'   => 'products.index',
    'create'  => 'products.create',    
    'store'   => 'products.store',
    'show'    => 'products.view',
    'edit'    => 'products.edit',
    'update'  => 'products.update',
    'destroy' => 'products.delete',
]);

Route::get('/products/template-row', function (Request $request) {
    // Ambil kategori dari database, karena template butuh data ini
    $categories = Category::all();
    $index = $request->query('index', 0); // Dapatkan index dari query string, default 0

    // Render template Blade dan kembalikan sebagai string HTML
    return view('pages.products._product_item_template', compact('index', 'categories'))->render();
})->name('products.template-row');
