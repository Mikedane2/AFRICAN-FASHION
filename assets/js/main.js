document.addEventListener('DOMContentLoaded', function() {
    
    // Add to Cart Functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart, .quick-add-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const quantity = document.getElementById('qty') ? document.getElementById('qty').value : 1;
            let size = null;
            let color = null;
            
            const activeSize = document.querySelector('.size-btn.active');
            if(activeSize) size = activeSize.dataset.size;
            
            const activeColor = document.querySelector('.color-btn.active');
            if(activeColor) color = activeColor.dataset.color;
            
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            this.disabled = true;
            
            fetch('includes/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&product_id=${productId}&quantity=${quantity}&size=${size || ''}&color=${color || ''}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const cartCount = document.getElementById('cart-count');
                    if(cartCount) cartCount.textContent = data.cart_count;
                    showNotification('Added to cart!', 'success');
                } else {
                    showNotification(data.message || 'Error adding to cart', 'error');
                }
                this.innerHTML = originalText;
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding to cart', 'error');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });
    
    // Add to Wishlist
    const wishlistButtons = document.querySelectorAll('.add-to-wishlist');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            
            fetch('wishlist-ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                const wishlistCount = document.getElementById('wishlist-count');
                if(wishlistCount) wishlistCount.textContent = data.count;
                if(data.added) {
                    this.innerHTML = '<i class="fas fa-heart"></i>';
                    showNotification('Added to wishlist!', 'success');
                } else {
                    this.innerHTML = '<i class="far fa-heart"></i>';
                    showNotification('Removed from wishlist', 'info');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    
    // Remove from Wishlist
    const removeWishlistButtons = document.querySelectorAll('.remove-wishlist');
    removeWishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if(confirm('Remove from wishlist?')) {
                const productId = this.dataset.id;
                fetch('wishlist-ajax.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}`
                })
                .then(() => location.reload())
                .catch(error => console.error('Error:', error));
            }
        });
    });
    
    // Change Currency
    const currencyButtons = document.querySelectorAll('.change-currency');
    currencyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const currency = this.dataset.currency;
            fetch('includes/currency.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `change_currency=${currency}`
            })
            .then(() => location.reload())
            .catch(error => console.error('Error:', error));
        });
    });
    
    // Update Cart Quantity
    const cartQtyInputs = document.querySelectorAll('.cart-qty');
    cartQtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.id;
            const quantity = this.value;
            fetch('includes/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&cart_id=${cartId}&quantity=${quantity}`
            })
            .then(() => location.reload())
            .catch(error => console.error('Error:', error));
        });
    });
    
    // Remove Cart Item
    const removeItemButtons = document.querySelectorAll('.remove-item');
    removeItemButtons.forEach(button => {
        button.addEventListener('click', function() {
            if(confirm('Remove item from cart?')) {
                const cartId = this.dataset.id;
                fetch('includes/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=remove&cart_id=${cartId}`
                })
                .then(() => location.reload())
                .catch(error => console.error('Error:', error));
            }
        });
    });
    
    // Size Selection
    const sizeButtons = document.querySelectorAll('.size-btn');
    sizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            sizeButtons.forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('active', 'btn-primary');
        });
    });
    
    // Color Selection
    const colorButtons = document.querySelectorAll('.color-btn');
    colorButtons.forEach(button => {
        button.addEventListener('click', function() {
            colorButtons.forEach(btn => btn.classList.remove('active', 'border-primary'));
            this.classList.add('active', 'border-primary');
        });
    });
    
    // Quantity Controls
    const quantityPlus = document.querySelector('.quantity-plus');
    const quantityMinus = document.querySelector('.quantity-minus');
    const quantityInput = document.getElementById('qty');
    
    if(quantityPlus && quantityInput) {
        quantityPlus.addEventListener('click', () => {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        });
    }
    
    if(quantityMinus && quantityInput) {
        quantityMinus.addEventListener('click', () => {
            if(parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
    }
    
    // Sort Products
    const sortSelect = document.getElementById('sort-by');
    if(sortSelect) {
        sortSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }
    
    // Checkout Form
    const checkoutForm = document.getElementById('checkoutForm');
    if(checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });
            
            fetch('process-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data).toString()
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Order placed successfully!\nOrder Number: ' + data.order_number);
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Load Cart Count
    function loadCartCount() {
        fetch('includes/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                const cartCount = document.getElementById('cart-count');
                if(cartCount) cartCount.textContent = data.count;
            })
            .catch(error => console.error('Error loading cart count:', error));
    }
    
    // Load Wishlist Count
    function loadWishlistCount() {
        fetch('wishlist-count.php')
            .then(response => response.json())
            .then(data => {
                const wishlistCount = document.getElementById('wishlist-count');
                if(wishlistCount) wishlistCount.textContent = data.count;
            })
            .catch(error => console.error('Error loading wishlist count:', error));
    }
    
    // Notification function
    function showNotification(message, type) {
        const bgColor = type === 'success' ? '#28a745' : (type === 'error' ? '#dc3545' : '#17a2b8');
        const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle');
        
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header" style="background: ${bgColor}; color: white;">
                    <i class="fas fa-${icon} me-2"></i>
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
        
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
        
        if(typeof bootstrap !== 'undefined') {
            const bsToast = new bootstrap.Toast(toast.querySelector('.toast'));
            bsToast.show();
        }
    }
    
    loadCartCount();
    loadWishlistCount();
});