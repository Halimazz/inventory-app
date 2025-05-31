@extends('layouts.main') {{-- Pastikan ini sesuai dengan layout utama Anda --}}

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
                {{-- Pastikan action route ini benar, sebelumnya kita pakai products.store --}}
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    {{-- @method('POST') tidak diperlukan di sini karena default method HTML form adalah POST --}}

                    <div class="card-body">
                        {{-- Menampilkan pesan sukses/error dari session --}}
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
                            {{-- Baris produk awal. Pastikan $categories tersedia di sini --}}
                            {{-- Jika $categories belum ada di controller create(), tambahkan di sana --}}
                            @include('pages.products._product_item_template', ['index' => 0, 'categories' => $categories ?? []])
                        </div>

                        <button type="button" id="add-product-row" class="btn btn-sm btn-info mt-3">
                            <i class="bi bi-plus-circle-fill"></i> Add product form
                        </button>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save All</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary ms-2">Cancel</a> {{-- Tambah margin kiri sedikit --}}
                    </div>
                    </form>
                </div>
        </div>
    </div>

    {{-- Skrip JavaScript untuk menambah/menghapus baris --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let productIndex = {{ old('products') ? count(old('products')) - 1 : 0 }};
            const container = document.getElementById('product-items-container');
            const addRowBtn = document.getElementById('add-product-row');

            console.log('DOM Content Loaded. Initial productIndex:', productIndex);
            console.log('Container element:', container);
            console.log('Add Row Button element:', addRowBtn);

            function updateProductNumbers() {
                const productItems = container.querySelectorAll('.product-item');
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
                    item.querySelectorAll('.text-danger').forEach(errorDiv => errorDiv.remove());
                });
                console.log('updateProductNumbers called. Current number of product items:', productItems.length);
            }

            async function getProductItemTemplate(index) {
                console.log('Attempting to fetch template for index:', index);
                try {
                    const url = `{{ route('products.template-row') }}?index=${index}`;
                    console.log('Fetching from URL:', url);
                    const response = await fetch(url);

                    console.log('Fetch response received. Status:', response.status, 'OK:', response.ok);

                    if (!response.ok) {
                        const errorText = await response.text(); // Coba ambil teks error dari response
                        throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
                    }
                    const html = await response.text();
                    console.log('Template HTML received (first 200 chars):', html.substring(0, 200));

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html.trim();
                    const newElement = tempDiv.firstChild;
                    console.log('New element created from template:', newElement);
                    return newElement;
                } catch (error) {
                    console.error('Error fetching product template:', error);
                    alert('Gagal memuat template produk. Silakan coba lagi. Lihat konsol untuk detail error.');
                    return null;
                }
            }

            addRowBtn.addEventListener('click', async function () {
                console.log('Add Product Row button clicked!');
                productIndex++;
                console.log('New productIndex:', productIndex);

                const newRow = await getProductItemTemplate(productIndex);
                console.log('Result from getProductItemTemplate:', newRow);

                if (newRow) {
                    container.appendChild(newRow);
                    console.log('New row appended to container.');
                    updateProductNumbers();
                } else {
                    console.warn('newRow is null, not appending.');
                }
            });

            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-product-row')) {
                    console.log('Remove Product Row button clicked!');
                    if (container.children.length > 1) {
                        e.target.closest('.product-item').remove();
                        console.log('Product row removed.');
                        updateProductNumbers();
                        productIndex--; // Decrement index when a row is removed
                        console.log('productIndex after removal:', productIndex);
                    } else {
                        alert('Minimal harus ada satu baris produk.');
                        console.warn('Attempted to remove last product row.');
                    }
                }
            });

            updateProductNumbers(); // Initial call to ensure numbers/ids are correct
        });
    </script>
    @endpush
@endsection