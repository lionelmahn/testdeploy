@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container mt-4">
    <h2>Sửa sản phẩm</h2>

    <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Thông tin sản phẩm chính -->
        <div class="mb-3">
            <label class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá</label>
            <input type="number" name="price" class="form-control" step="0.01" required value="{{ old('price', $product->price) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng tồn kho</label>
            <input type="number" name="stock_quantity" class="form-control" required value="{{ old('stock_quantity', $product->stock_quantity) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh</label>
            @if($product->image_url)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image_url) }}" width="100" alt="Current Image">
                </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <!-- Biến thể sản phẩm -->
        <h4>Biến thể sản phẩm</h4>
        <small class="text-muted">Sửa các biến thể sản phẩm hoặc thêm mới.</small>

        <div id="variants-wrapper">
            @foreach(old('variants', $product->variants->toArray()) as $index => $variant)
                <div class="variant-item border p-3 mb-3">
                    @if(!empty($variant['variant_id']))
                        <input type="hidden" name="variants[{{ $index }}][variant_id]" value="{{ $variant['variant_id'] }}">
                    @endif
                    <div class="mb-3">
                        <label>Tên biến thể</label>
                        <input type="text" name="variants[{{ $index }}][variant_name]" class="form-control" placeholder="Tên biến thể" required value="{{ $variant['variant_name'] }}">
                    </div>
                    <div class="mb-3">
                        <label>Giá thêm (USD)</label>
                        <input type="number" name="variants[{{ $index }}][additional_price]" class="form-control" step="0.01" value="{{ $variant['additional_price'] ?? 0 }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Số lượng tồn kho</label>
                        <input type="number" name="variants[{{ $index }}][stock_quantity]" class="form-control" value="{{ $variant['stock_quantity'] ?? 0 }}" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-primary mb-3" id="add-variant">Thêm biến thể</button>

        <br>
        <button type="submit" class="btn btn-success">Cập nhật sản phẩm</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let wrapper = document.getElementById('variants-wrapper');

    // Hàm cập nhật lại index các biến thể trong form
    function refreshVariantIndexes() {
        const variants = wrapper.querySelectorAll('.variant-item');
        variants.forEach((variant, index) => {
            // variant_id input nếu có
            const hiddenInput = variant.querySelector('input[type="hidden"][name$="[variant_id]"]');
            if (hiddenInput) {
                hiddenInput.name = `variants[${index}][variant_id]`;
            }

            // Tên biến thể
            const variantNameInput = variant.querySelector('input[name$="[variant_name]"]');
            variantNameInput.name = `variants[${index}][variant_name]`;

            // Giá thêm
            const additionalPriceInput = variant.querySelector('input[name$="[additional_price]"]');
            additionalPriceInput.name = `variants[${index}][additional_price]`;

            // Số lượng tồn kho
            const stockQuantityInput = variant.querySelector('input[name$="[stock_quantity]"]');
            stockQuantityInput.name = `variants[${index}][stock_quantity]`;
        });
    }

    let variantIndex = wrapper.querySelectorAll('.variant-item').length;

    // Thêm biến thể mới
    document.getElementById('add-variant').addEventListener('click', function () {
        const newVariant = document.createElement('div');
        newVariant.classList.add('variant-item', 'border', 'p-3', 'mb-3');
        newVariant.innerHTML = `
            <div class="mb-3">
                <label>Tên biến thể</label>
                <input type="text" name="variants[${variantIndex}][variant_name]" class="form-control" placeholder="Ví dụ: Màu xanh" required>
            </div>
            <div class="mb-3">
                <label>Giá thêm (USD)</label>
                <input type="number" name="variants[${variantIndex}][additional_price]" class="form-control" step="0.01" value="0" required>
            </div>
            <div class="mb-3">
                <label>Số lượng tồn kho</label>
                <input type="number" name="variants[${variantIndex}][stock_quantity]" class="form-control" value="0" required>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
        `;
        wrapper.appendChild(newVariant);
        variantIndex++;

        newVariant.querySelector('.remove-variant').addEventListener('click', function () {
            newVariant.remove();
            refreshVariantIndexes();
        });
    });

    // Xóa biến thể hiện có
    wrapper.querySelectorAll('.remove-variant').forEach(btn => {
        btn.addEventListener('click', function () {
            btn.closest('.variant-item').remove();
            refreshVariantIndexes();
        });
    });
});
</script>
@endsection
