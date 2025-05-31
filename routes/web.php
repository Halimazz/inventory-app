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
    try {
        $categories = Category::all();
        $index = $request->query('index', 0);
        return view('pages.products._product_item_template', compact('index', 'categories'))->render();
    } catch (\Exception $e) {
        return response('Gagal render template: ' . $e->getMessage(), 500);
    }
})->name('products.template-row');
