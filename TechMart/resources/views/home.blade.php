@extends('layouts.app')

@section('title', 'TechMart - Trang chủ')

@section('content')
<div class="container">
    <!-- Banner -->
    <div class="bg-danger text-white rounded p-5 text-center mb-5">
        <h1 class="display-5 fw-bold">Chào mừng đến với TechMart</h1>
        <p class="lead">Khám phá sản phẩm công nghệ mới nhất với giá siêu tốt</p>
    </div>

    <!-- Danh mục nổi bật -->
    @if($categoriesWithProducts)
        @foreach($categoriesWithProducts as $categoryData)
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 text-dark">
                    <i class="fas fa-tag text-danger me-2"></i>{{ $categoryData['category']->category_name }}
                </h2>
                <a href="{{ route('products.category', $categoryData['category']) }}" class="text-danger text-decoration-none">
                    Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-3">
                @foreach($categoryData['products'] as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <a href="{{ route('products.show', $product) }}">
                                @if($product->image_url)
                                    <img src="{{ asset('storage/' . $product->image_url) }}" class="card-img-top" alt="{{ $product->name }}">
                                @else
                                    <div class="text-center py-5 bg-light">
                                        <i class="fas fa-image text-muted fs-1"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="card-body">
                                <h5 class="card-title fs-6">
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                        {{ $product->name }}
                                    </a>
                                </h5>
                                <p class="text-danger fw-semibold">{{ number_format($product->price, 2) }}₫</p>
                                <p class="text-muted small mb-2">Kho: {{ $product->stock_quantity }}</p>
                                @if($product->stock_quantity > 0)
                                    <button onclick="addToCart({{ $product->product_id }})" class="btn btn-danger w-100">
                                        <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                    </button>
                                @else
                                    <button class="btn btn-secondary w-100" disabled>Hết hàng</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @else
        <div class="text-center my-5 py-5">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Chưa có sản phẩm nào</h4>
            <p class="text-secondary">Vui lòng quay lại sau.</p>
        </div>
    @endif
</div>
@endsection
