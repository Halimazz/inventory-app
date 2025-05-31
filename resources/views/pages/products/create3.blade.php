@extends('layouts.main')

@section('header')
    <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Tambah Produk</h3></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><div class="card-title">Tambah Produk</div></div>
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div id="product-items-container">
                            {{-- Baris produk awal. Pastikan $categories tersedia dari controller `ProductController@create` --}}
                            @include('pages.products._product_item_template', ['index' => 1, 'categories' => $categories ?? []])
                        </div>

                        <button type="button" id="add-product-row" class="btn btn-sm btn-info mt-3">
                            <i class="bi bi-plus-circle-fill"></i> Add Product Form
                        </button>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Simpan Semua Produk</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary ms-2">Batal</a>
                    </div>
                    </form>
                </div>
        </div>
    </div>

    {{-- Skrip JavaScript untuk menambah/menghapus baris --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi productIndex berdasarkan jumlah elemen yang sudah ada jika ada old() data, atau 0
            let productIndex = {{ old('products') ? count(old('products')) - 1 : 0 }};

            const container = document.getElementById('product-items-container');
            const addRowBtn = document.getElementById('add-product-row');

            console.log('--- DOM Content Loaded ---');
            console.log('Initial productIndex:', productIndex);
            console.log('Container element:', container);
            console.log('Add Row Button element:', addRowBtn);

            // Fungsi untuk mengupdate nomor produk dan atribut name/id
            function updateProductNumbers() {
                const productItems = container.querySelectorAll('.product-item');
                console.log('updateProductNumbers called. Number of product items found:', productItems.length);

                productItems.forEach((item, idx) => {
                    item.setAttribute('data-index', idx);
                    item.querySelector('.product-number').textContent = idx + 1;

                    item.querySelectorAll('[name], [id]').forEach(element => {
                        const originalName = element.getAttribute('name');
                        if (originalName) {
                            const newName = originalName.replace(/products\[\d+\]/, `products[${idx}]`);
                            element.setAttribute('name', newName);
                        }

                        const originalId = element.getAttribute('id');
                        if (originalId) {
                            const newId = originalId.replace(/products_\d+_/, `products_${idx}_`);
                            element.setAttribute('id', newId);

                            const label = item.querySelector(`label[for="${originalId}"]`);
                            if (label) {
                                label.setAttribute('for', newId);
                            }
                        }
                    });
                    // Logika untuk menghapus pesan error lama (jika ada)
                    item.querySelectorAll('.text-danger').forEach(errorDiv => {
                        if (errorDiv && errorDiv.parentNode) { // Pastikan elemen dan parent-nya ada sebelum dihapus
                            errorDiv.remove();
                        } else {
                            console.warn('Attempted to remove non-existent or parentless errorDiv:', errorDiv);
                        }
                    });
                });
                console.log('updateProductNumbers completed.');
            }

            // Fungsi untuk mengambil template dari server via AJAX
            async function getProductItemTemplate(index) {
                console.log('--- getProductItemTemplate called ---');
                console.log('Attempting to fetch template for index:', index);
                try {
                    const url = `{{ route('products.template-row') }}?index=${index}`;
                    console.log('Fetching from URL:', url);
                    const response = await fetch(url);

                    console.log('Fetch response received. Status:', response.status, 'OK:', response.ok);

                    if (!response.ok) {
                        const errorText = await response.text(); // Ambil teks error dari response
                        console.error('HTTP Error Details:', errorText);
                        throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
                    }
                    const html = await response.text();
                    console.log('Raw Template HTML received (full content):', html); // Log seluruh HTML untuk inspeksi

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html.trim();
                    console.log('tempDiv.innerHTML after html.trim():', tempDiv.innerHTML); // Apa yang ada di dalam tempDiv setelah parse

                    const newElement = tempDiv.firstChild;
                    console.log('newElement (tempDiv.firstChild):', newElement); // Ini harusnya elemen DOM

                    // Pastikan newElement tidak null atau undefined
                    if (!newElement) {
                        console.error("CRITICAL: tempDiv.firstChild is null or undefined. The HTML might be empty, malformed, or not a single root element.");
                        throw new Error("Failed to extract a valid DOM element from the fetched template HTML.");
                    }

                    console.log('--- getProductItemTemplate returning newElement ---');
                    return newElement;

                } catch (error) {
                    console.error('CRITICAL ERROR in getProductItemTemplate (caught):', error);
                    alert('Gagal memuat template produk. Silakan coba lagi. Lihat konsol untuk detail error.');
                    console.log('--- getProductItemTemplate returning null due to error ---');
                    return null; // Akan mengembalikan null jika ada error
                }
            }

            

            // PENTING: Panggil updateProductNumbers saat halaman dimuat pertama kali
            // untuk memastikan penomoran awal dan id/name sudah benar untuk baris yang di-render oleh Blade
            updateProductNumbers(); // Ini dipanggil setelah DOM siap, jadi harusnya aman

            // Event listener untuk tombol "Tambah Baris Produk"
            addRowBtn.addEventListener('click', async function () {
                console.log('\n--- Add Product Row button clicked! ---');
                productIndex++;
                console.log('Incremented productIndex to:', productIndex);

                const newRow = await getProductItemTemplate(productIndex);
                console.log('Result from getProductItemTemplate (after await):', newRow);

                if (newRow) {
                    container.appendChild(newRow);
                    console.log('New row successfully appended to container.');
                    updateProductNumbers();
                } else {
                    console.warn('newRow is null, not appending to container. Check getProductItemTemplate errors.');
                }
            });

            // Event listener untuk tombol "Hapus Produk Ini"
            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-product-row')) {
                    console.log('Remove Product Row button clicked!');
                    if (container.children.length > 1) {
                        e.target.closest('.product-item').remove();
                        console.log('Product row removed from DOM.');
                        updateProductNumbers(); // Update numbers, names, and IDs for remaining rows
                        productIndex--; // Decrement index when a row is removed
                        console.log('productIndex after row removal:', productIndex);
                    } else {
                        alert('Minimal harus ada satu baris produk.');
                        console.warn('Attempted to remove the last product row. Operation blocked.');
                    }
                }
            });
        });
    </script>
    @endpush
@endsection