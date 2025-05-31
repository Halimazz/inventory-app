<div class="product-item border p-3 mb-3 rounded" data-index="{{ $index }}">
    <h5 class="text-primary">Produk #<span class="product-number">{{ $index + 1 }}</span></h5>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="products_{{ $index }}_name" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="products_{{ $index }}_name" name="products[{{ $index }}][name]" required value="{{ old('products.' . $index . '.name') }}">
            @error('products.' . $index . '.name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="products_{{ $index }}_sku" class="form-label">SKU</label>
            <input type="text" class="form-control" id="products_{{ $index }}_sku" name="products[{{ $index }}][sku]" required value="{{ old('products.' . $index . '.sku') }}">
            @error('products.' . $index . '.sku') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="products_{{ $index }}_price" class="form-label">Harga</label>
            <input type="number" class="form-control" id="products_{{ $index }}_price" name="products[{{ $index }}][price]" step="0.01" min="0" required value="{{ old('products.' . $index . '.price') }}">
            @error('products.' . $index . '.price') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="products_{{ $index }}_stock" class="form-label">Stok</label>
            <input type="number" class="form-control" id="products_{{ $index }}_stock" name="products[{{ $index }}][stock]" min="0" required value="{{ old('products.' . $index . '.stock') }}">
            @error('products.' . $index . '.stock') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="products_{{ $index }}_category_id" class="form-label">Kategori</label>
            <select class="form-control" id="products_{{ $index }}_category_id" name="products[{{ $index }}][category_id]" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('products.' . $index . '.category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('products.' . $index . '.category_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="mb-3">
        <label for="products_{{ $index }}_description" class="form-label">Deskripsi (Opsional)</label>
        <textarea class="form-control" id="products_{{ $index }}_description" name="products[{{ $index }}][description]" rows="2">{{ old('products.' . $index . '.description') }}</textarea>
        @error('products.' . $index . '.description') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>
    <button type="button" class="btn btn-danger btn-sm remove-product-row"><i class="bi bi-trash"></i> Remove this form</button>
</div>