@extends('layouts.app')

@section('title', 'TechMart - Siêu thị công nghệ hàng đầu')

@section('content')
<div>
    <!-- Header -->
    <header>
        <div>
            <!-- Logo -->
            <div>
                <a href="{{ route('home') }}">
                    <i class="fas fa-store"></i>
                    TechMart
                </a>
            </div>

            <!-- Search Bar -->
            <div>
                <form action="{{ route('search') }}" method="GET">
                    <input type="text" 
                           name="q" 
                           placeholder="Tìm kiếm sản phẩm..." 
                           value="{{ request('q') }}">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                        Tìm kiếm
                    </button>
                </form>
            </div>

            <!-- Cart & User -->
            <div>
                <!-- Cart -->
                <a href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    Giỏ hàng
                    @auth
                        @if(auth()->user()->cartItems->count() > 0)
                            <span>({{ auth()->user()->cartItems->sum('quantity') }})</span>
                        @endif
                    @endauth
                </a>

                <!-- User Menu -->
                <div>
                    <button onclick="toggleUserMenu()">
                        <i class="fas fa-user"></i>
                        Tài khoản
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <div id="userMenu" style="display: none;">
                        @auth
                            <div>
                                <strong>{{ auth()->user()->name }}</strong>
                                <br>
                                <small>{{ auth()->user()->email }}</small>
                            </div>
                            <hr>
                            <a href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-cog"></i>
                                Tài khoản của tôi
                            </a>
                            <a href="{{ route('cart.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                Giỏ hàng
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-cogs"></i>
                                    Quản trị
                                </a>
                            @endif
                            <hr>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i>
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i>
                                Đăng ký
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Categories Navigation -->
    <nav>
        <div>
            <button onclick="toggleCategories()">
                <i class="fas fa-bars"></i>
                Danh mục sản phẩm
                <i class="fas fa-chevron-down" id="categoryArrow"></i>
            </button>

            <!-- Quick Categories (Always visible) -->
            <div>
                @foreach($categories->take(6) as $category)
                    <a href="{{ route('products.category', $category) }}">
                        {{ $category->category_name }}
                    </a>
                @endforeach
                @if($categories->count() > 6)
                    <button onclick="showAllCategories()">
                        Xem thêm...
                    </button>
                @endif
            </div>

            <!-- Expandable Categories -->
            <div id="allCategories" style="display: none;">
                <div>
                    @foreach($categories as $category)
                        <div>
                            <a href="{{ route('products.category', $category) }}">
                                <div>
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <div>{{ $category->category_name }}</div>
                                    <small>{{ $category->products_count }} sản phẩm</small>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Welcome Banner -->
        <section>
            <div>
                <h1>Chào mừng đến với TechMart</h1>
                <p>Khám phá những sản phẩm công nghệ mới nhất với giá tốt nhất</p>
            </div>
        </section>

        <!-- Products by Category -->
        @foreach($categoriesWithProducts as $categoryData)
            <section>
                <!-- Category Header -->
                <div>
                    <h2>
                        {{ $categoryData['category']->category_name }}
                        <small>({{ $categoryData['products']->count() }} sản phẩm mới nhất)</small>
                    </h2>
                    <a href="{{ route('products.category', $categoryData['category']) }}">
                        Xem tất cả
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- Products Grid -->
                <div>
                    @foreach($categoryData['products'] as $product)
                        <div class="product-item">
                            <!-- Product Image -->
                            <div class="product-image">
                                <a href="{{ route('products.show', $product) }}">
                                    @if($product->image_url)
                                        <img src="{{ asset('storage/' . $product->image_url) }}" 
                                             alt="{{ $product->name }}">
                                    @else
                                        <div class="no-image">
                                            <i class="fas fa-image"></i>
                                            <span>Không có hình ảnh</span>
                                        </div>
                                    @endif
                                </a>
                            </div>

                            <!-- Product Info -->
                            <div class="product-info">
                                <h3>
                                    <a href="{{ route('products.show', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>

                                <!-- Price -->
                                <div class="product-price">
                                    ${{ number_format($product->price, 2) }}
                                </div>

                                <!-- Stock Status -->
                                <div class="product-stock">
                                    <span>Còn {{ $product->stock_quantity }} sản phẩm</span>
                                    @if($product->stock_quantity > 0)
                                        <span class="in-stock">
                                            <i class="fas fa-check-circle"></i>
                                            Còn hàng
                                        </span>
                                    @else
                                        <span class="out-of-stock">
                                            <i class="fas fa-times-circle"></i>
                                            Hết hàng
                                        </span>
                                    @endif
                                </div>

                                <!-- Add to Cart Button -->
                                @if($product->stock_quantity > 0)
                                    <button onclick="addToCart({{ $product->product_id }})" class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i>
                                        Thêm vào giỏ
                                    </button>
                                @else
                                    <button disabled class="out-of-stock-btn">
                                        Hết hàng
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <!-- Empty State -->
        @if(empty($categoriesWithProducts))
            <section>
                <div>
                    <i class="fas fa-box-open"></i>
                    <h3>Chưa có sản phẩm nào</h3>
                    <p>Vui lòng quay lại sau khi admin đã thêm sản phẩm.</p>
                </div>
            </section>
        @endif
    </main>

    <!-- Footer -->
    <footer>
        <div>
            <div>
                <h4>TechMart</h4>
                <p>Siêu thị công nghệ hàng đầu Việt Nam</p>
            </div>
            <div>
                <h4>Liên hệ</h4>
                <p>Email: info@techmart.com</p>
                <p>Hotline: 1900-1234</p>
            </div>
            <div>
                <h4>Hỗ trợ</h4>
                <a href="#">Chính sách bảo hành</a>
                <a href="#">Hướng dẫn mua hàng</a>
                <a href="#">Chính sách đổi trả</a>
            </div>
        </div>
        <div>
            <p>&copy; 2024 TechMart. All rights reserved.</p>
        </div>
    </footer>
</div>

<!-- Notification Container -->
<div id="notificationContainer"></div>

<script>
// Toggle User Menu
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Toggle Categories
function toggleCategories() {
    const categories = document.getElementById('allCategories');
    const arrow = document.getElementById('categoryArrow');
    
    if (categories.style.display === 'none') {
        categories.style.display = 'block';
        arrow.classList.add('rotated');
    } else {
        categories.style.display = 'none';
        arrow.classList.remove('rotated');
    }
}

// Show All Categories
function showAllCategories() {
    document.getElementById('allCategories').style.display = 'block';
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('userMenu');
    const categoriesMenu = document.getElementById('allCategories');
    
    if (!event.target.closest('[onclick="toggleUserMenu()"]')) {
        userMenu.style.display = 'none';
    }
    
    if (!event.target.closest('[onclick="toggleCategories()"]') && 
        !event.target.closest('[onclick="showAllCategories()"]')) {
        categoriesMenu.style.display = 'none';
        document.getElementById('categoryArrow').classList.remove('rotated');
    }
});

// Add to Cart Function
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
                updateCartCount();
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

// Show notification
function showNotification(message, type = 'info') {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div>
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation' : 'info'}-circle"></i>
            ${message}
        </div>
    `;
    
    container.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        container.removeChild(notification);
    }, 3000);
}

// Update cart count
function updateCartCount() {
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
@endsection