/**
 * main.js
 * Shared frontend behaviour for Craftora.
 */

document.addEventListener('DOMContentLoaded', () => {
    refreshCartCount();
    bindAddToCartButtons();
    bindCartQuantityControls();
    bindRemoveFromCartButtons();
});

/**
 * Fetches the current cart item count and updates the badge in the header.
 */
function refreshCartCount() {
    const badge = document.getElementById('cartCount');
    if (!badge) return;

    fetch('api/cart_count.php')
        .then(res => res.json())
        .then(data => {
            const count = data.count || 0;
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        })
        .catch(() => {
            badge.style.display = 'none';
        });
}

/**
 * Binds "Add to Cart" buttons (on products.php / product cards).
 */
function bindAddToCartButtons() {
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = btn.dataset.productId;

            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            fetch('api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        refreshCartCount();
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Added';
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }, 1200);
                    } else {
                        alert(data.message || 'Could not add item to cart.');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    alert('Something went wrong. Please try again.');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
        });
    });
}

/**
 * Binds quantity +/- controls on cart.php
 */
function bindCartQuantityControls() {
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('.cart-item-row');
            const input = row.querySelector('.qty-input');
            let qty = parseInt(input.value, 10) || 1;
            qty = btn.dataset.action === 'increase' ? qty + 1 : Math.max(1, qty - 1);
            input.value = qty;
            updateCartQuantity(row.dataset.cartId, qty, row);
        });
    });

    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', () => {
            const row = input.closest('.cart-item-row');
            let qty = Math.max(1, parseInt(input.value, 10) || 1);
            input.value = qty;
            updateCartQuantity(row.dataset.cartId, qty, row);
        });
    });
}

function updateCartQuantity(cartId, quantity, row) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);

    fetch('api/cart_actions.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (row) {
                    const priceEl = row.querySelector('.line-total');
                    if (priceEl && data.line_total !== undefined) {
                        priceEl.textContent = data.line_total;
                    }
                }
                if (data.cart_total !== undefined) {
                    const totalEl = document.getElementById('cartTotal');
                    if (totalEl) totalEl.textContent = data.cart_total;
                }
                refreshCartCount();
            }
        });
}

/**
 * Binds "remove" buttons on cart.php
 */
function bindRemoveFromCartButtons() {
    document.querySelectorAll('.btn-remove-item').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!confirm('Remove this item from your cart?')) return;

            const row = btn.closest('.cart-item-row');
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('cart_id', row.dataset.cartId);

            fetch('api/cart_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        row.remove();
                        refreshCartCount();
                        if (data.cart_total !== undefined) {
                            const totalEl = document.getElementById('cartTotal');
                            if (totalEl) totalEl.textContent = data.cart_total;
                        }
                        if (data.empty) {
                            location.reload();
                        }
                    }
                });
        });
    });
}
