<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;


class ProductController extends Controller
{
    private $views      = 'pages/products';
    private $url        = '/products';
    private $title      = 'Halaman Data Products';
    protected $mProduct;
    protected $mCategory;
    // protected $mPasien;

    public function __construct()
    {
       $this->mProduct = new Product();
         $this->mCategory = new Category();
    }
    

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->mProduct->get();
        $data =[
            'title' => $this->title,
            'url' => $this->url,
            'views' => $this->views,
            'products' => $products
        ];
        return view($this->views . "/index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->mCategory->get();
        $data = [
            'categories' => $categories,
            'title' => $this->title,
            'url' => $this->url,
            'views' => $this->views
        ];
        return view($this->views . "/create", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // 1. Validasi Input untuk Setiap Produk di dalam Array
        // Aturan validasi untuk setiap item dalam array 'products'
        $rules = [
            'products'                => 'required|array', // Pastikan ada array products
            'products.*.name'         => 'required|string|max:255',
            'products.*.description'  => 'nullable|string',
            'products.*.price'        => 'required|numeric|min:0',
            'products.*.stock'        => 'required|integer|min:0',
            'products.*.category_id'  => 'required|exists:categories,id',
        ];
        
        // Aturan unique untuk SKU harus disesuaikan jika ingin unique per item dalam batch
        // Ini memastikan SKU unik di antara semua produk yang di-submit DAN di database
        foreach ($request->input('products', []) as $index => $productData) {
            $rules["products.{$index}.sku"] = [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku') // Unique di tabel products
                    ->where(function ($query) use ($productData, $index, $request) {
                        // Ini memastikan SKU unik bahkan di antara item lain yang dikirim dalam batch yang sama
                        $skusInRequest = collect($request->input('products'))
                                         ->pluck('sku')
                                         ->filter()
                                         ->values()
                                         ->toArray();
                        // Filter SKU yang sama dari request, kecuali SKU di indeks ini
                        $duplicateSkusInRequest = array_diff_assoc($skusInRequest, array_unique($skusInRequest));

                        if (in_array($productData['sku'], $duplicateSkusInRequest) && array_search($productData['sku'], $skusInRequest) !== $index) {
                            // Jika SKU ini duplikat di antara item lain dalam request,
                            // tambahkan kondisi yang selalu gagal untuk trigger validasi custom error
                            // Laravel 9+ juga punya Rule::unique('table')->ignore($id, 'column')->where(...)->whereNot('id', $id)
                            // Tapi untuk batch create, kita perlu cek di dalam request juga.
                            return $query->whereRaw('1 = 0'); // Ini akan membuat validasi selalu gagal jika ada duplikat dalam request
                        }
                    }),
            ];
        }

        $validatedData = $request->validate($rules);

        // 2. Simpan Setiap Produk ke Database dalam Transaksi
        DB::beginTransaction();
        try {
            foreach ($validatedData['products'] as $productData) {
                // Membuat produk baru menggunakan mass assignment
                // mProduct di-inject via constructor, jadi kita pakai $this->productModel
                $this->mProduct->create($productData);
            }
        DB::commit(); // Commit transaksi jika semua produk berhasil disimpan

            // Respon setelah berhasil disimpan
            // Untuk form web biasa, redirect dengan pesan sukses
            return redirect()->route('products.index')->with('success', 'Semua produk berhasil ditambahkan!');

            // Untuk API/AJAX, kembalikan respons JSON
            // return response()->json([
            //     'status'  => true,
            //     'message' => 'Semua produk berhasil ditambahkan!'
            // ], 201); // 201 Created
        } catch (\Exception $e) {
            DB::rollback(); // Rollback transaksi jika ada error
            \Log::error('Error creating multiple products: ' . $e->getMessage(), ['exception' => $e, 'request' => $request->all()]);

            // Respon jika terjadi error
            // Untuk form web biasa, kembali ke form dengan input lama dan pesan error
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());

            // Untuk API/AJAX
            // return response()->json([
            //     'status'  => false,
            //     'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
            // ], 500); // 500 Internal Server Error
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
