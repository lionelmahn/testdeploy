@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm: ' . $query)

@section('content')
<div class="container py-4">
    <!-- Search Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-2">
            Kết quả tìm kiếm cho: "<span class="text-danger">{{ $query }}</span>"
        </h1>
        <p class="text-secondary">Tìm thấy {{ $products->total() }} sản phẩm</p>
    </div>

    @if($products->count() > 0)
        <!-- Products Grid -->
        <div class="row g-3 mb-4">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('products.show', $product) }}" class="d-block overflow-hidden" style="aspect-ratio: 1 / 1;">
                            @if($product->image_url)
                                <img src="{{ asset('storage/' . $product->image_url) }}" 
                                     alt="{{ $product->name }}" 
                                     class="card-img-top object-fit-cover transition-scale" 
                                     style="transition: transform 0.3s;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%;">
                                    <i class="fas fa-image text-muted fs-1"></i>
                                </div>
                            @endif
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fs-6 text-truncate">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            <div class="text-muted small mb-1 text-truncate">
                                {{ $product->category->category_name ?? 'Chưa phân loại' }}
                            </div>
                            <div class="text-danger fw-bold fs-5 mb-2">
                                {{ number_format($product->price, 2) }}₫
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 small text-muted">
                                <span>Còn {{ $product->stock_quantity }} sản phẩm</span>
                                @if($product->stock_quantity > 0)
                                    <span class="text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>Còn hàng
                                    </span>
                                @else
                                    <span class="text-danger fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i>Hết hàng
                                    </span>
                                @endif
                            </div>
                            @if($product->stock_quantity > 0)
                                <button onclick="addToCart({{ $product->product_id }})" class="btn btn-danger w-100 mt-auto">
                                    <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                </button>
                            @else
                                <button class="btn btn-secondary w-100 mt-auto" disabled>Hết hàng</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-5x text-muted mb-3"></i>
            <h3 class="mb-2 text-secondary">Không tìm thấy sản phẩm nào</h3>
            <p class="mb-4 text-secondary">Hãy thử tìm kiếm với từ khóa khác hoặc xem các danh mục sản phẩm</p>
            <a href="{{ route('home') }}" class="btn btn-danger px-4 py-2">
                Về trang chủ
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Add to Cart Function (same as home page)
function addToCart(productId) {
    @auth
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
            } else {
                showNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
        });
    @else
        showNotification('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!', 'warning');
        setTimeout(() => {
            window.location.href = '{{ route("login") }}';
        }, 2000);
    @endauth
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed-top mt-3 me-3 p-3 rounded shadow text-white`;
    
    const colors = {
        success: 'bg-success',
        error: 'bg-danger',
        warning: 'bg-warning text-dark',
        info: 'bg-info'
    };
    
    notification.classList.add(...colors[type].split(' '));
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation' : 'info'}-circle me-2"></i>
            <div>${message}</div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity');
    }, 3000);
    
    setTimeout(() => {
        notification.remove();
    }, 3500);
}
</script>
@endpush

@push('styles')
<style>
.object-fit-cover {
    object-fit: cover;
}

.text-truncate {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.transition-scale:hover {
    transform: scale(1.05);
    transition: transform 0.3s;
}
</style>
@endpush

@endsection
