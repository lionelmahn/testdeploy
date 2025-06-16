@extends('layouts.app')

@section('content')
<div class="row">
    @foreach($products as $product)
        <div class="col-6 col-md-4 col-lg-3 mb-4">
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
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-6">
                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                            {{ $product->name }}
                        </a>
                    </h5>
                    <p class="text-danger fw-semibold">{{ number_format($product->price, 2) }}₫</p>
                    <p class="text-muted small mb-2">Kho: {{ $product->stock_quantity }}</p>
                    @if($product->stock_quantity > 0)
                        <button onclick="addToCart({{ $product->product_id }})" class="btn btn-danger mt-auto w-100">
                            <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                        </button>
                    @else
                        <button class="btn btn-secondary mt-auto w-100" disabled>Hết hàng</button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
