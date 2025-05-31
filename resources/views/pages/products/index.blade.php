@extends('layouts.main')

@section('header')
    <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Produk</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Produk</li>
                </ol>
              </div>
            </div>
            </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-header d-flex justify-content-end">
              <a href="{{ route('products.create') }}">
                <button class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i> Tambah Produk
                </button>
              </a>

            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>NO</th>
                    <th>Nama Produk</th>
                    <th>Deskripsi</th>
                    <th>Kode</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($products as $product )
                    <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->category->name }}</td>
                    
                  </tr>
                  @endforeach
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
@endsection