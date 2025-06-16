@extends('layouts.admin')

@section('title', 'Thêm sản phẩm')

@section('content')
<div class="container mt-4">
    <h2>Thêm sản phẩm mới</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Thông tin sản phẩm chính -->
        <div class="mb-3">
            <label class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá</label>
            <input type="number" name="price" class="form-control" step="0.01" required value="{{ old('price') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng tồn kho</label>
            <input type="number" name="stock_quantity" class="form-control" required value="{{ old('stock_quantity') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <!-- Phần quản lý biến thể sản phẩm -->
        <h4>Biến thể sản phẩm</h4>
        <small class="text-muted">Thêm các biến thể cho sản phẩm với tên, giá thêm và tồn kho tương ứng.</small>

        <div id="variants-wrapper">
            <div class="variant-item border p-3 mb-3">
                <div class="mb-3">
                    <label>Tên biến thể</label>
                    <input type="text" name="variants[0][variant_name]" class="form-control" placeholder="Ví dụ: Màu đỏ" required>
                </div>
                <div class="mb-3">
                    <label>Giá thêm (USD)</label>
                    <input type="number" name="variants[0][additional_price]" class="form-control" step="0.01" value="0" required>
                </div>
                <div class="mb-3">
                    <label>Số lượng tồn kho</label>
                    <input type="number" name="variants[0][stock_quantity]" class="form-control" value="0" required>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
            </div>
        </div>

        <button type="button" class="btn btn-primary mb-3" id="add-variant">Thêm biến thể</button>

        <br>
        <button type="submit" class="btn btn-success">Thêm sản phẩm</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let variantIndex = 1;
    document.getElementById('add-variant').addEventListener('click', function () {
        const wrapper = document.getElementById('variants-wrapper');
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
        });
    });

    document.querySelectorAll('.remove-variant').forEach(btn => {
        btn.addEventListener('click', function () {
            btn.closest('.variant-item').remove();
        });
    });
});
</script>
@endsection
