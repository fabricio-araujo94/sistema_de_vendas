document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const cartItems = document.getElementById('cartItems');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartTotal = document.getElementById('discountInput');
    const discountInput = document.getElementById('discountInput');
    const paymentMethod = document.getElementById('paymentMethod');
    const btnCheckout = document.getElementById('btnCheckout');
    const btnClearCart = document.getElementById('btnClearCart');

    let currentCartTotal = 0.0;

    loadCart();

    searchInput.addEventListener('input', async (e) => {
        const term = e.target.value;
        if (term.length >= 2) {
            searchResults(term);
        } else {
            searchResults.innerHTML = '<tr><td colspan=4 class="text-center text-muted">Type at least 2 characters...</td></tr>';
        }
    });

    discountInput.addEventListener('input', updateTotalsDisplay);

    btnClearCart.addEventListener('click', clearCart);

    btnCheckout.addEventListener('click', processCheckout);

    async function searchProducts(term) {
        try {
            const response = await fetch(`/pos/search?q=${encodeURIComponent(term)}`);
            const data = await response.json();
            
            searchResults.innerHTML = ''; 
            
            if (data.length === 0) {
                searchResults.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No products found.</td></tr>';
                return;
            }

            data.forEach(product => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold">${product.name}</td>
                    <td>
                        <span class="badge ${product.stock > 0 ? 'bg-success' : 'bg-danger'}">
                            ${product.stock} in stock
                        </span>
                    </td>
                    <td>$ ${parseFloat(product.price).toFixed(2)}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary" onclick="addToCart(${product.id}, 1)" ${product.stock === 0 ? 'disabled' : ''}>
                            Add
                        </button>
                    </td>
                `;
                searchResults.appendChild(tr);
            });
        } catch (error) {
            console.error('Error searching products:', error);
        }
    }

    window.addToCart = async function(productId, quantity) {
        try {
            const response = await fetch('/pos/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                loadCart(); 
                searchInput.value = ''; 
                searchResults.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Search for a product to add to cart.</td></tr>';
            } else {
                alert(`Error: ${data.error}`);
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
        }
    };

    async function loadCart() {
        try {
            const response = await fetch('/pos/cart');
            const cartData = await response.json();
            
            cartItems.innerHTML = '';
            currentCartTotal = 0.0;

            if (cartData.length === 0) {
                cartItems.innerHTML = '<tr><td class="text-center text-muted py-4">Cart is empty</td></tr>';
                updateTotalsDisplay();
                return;
            }

            cartData.forEach(item => {
                currentCartTotal += parseFloat(item.subtotal);
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold">${item.name} <br> <small class="text-muted">${item.quantity}x $${parseFloat(item.price).toFixed(2)}</small></td>
                    <td class="text-end align-middle fw-bold">$ ${parseFloat(item.subtotal).toFixed(2)}</td>
                `;
                cartItems.appendChild(tr);
            });

            updateTotalsDisplay();

        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    async function clearCart() {
        if (!confirm('Are you sure you want to clear the cart?')) return;
        
        try {
            await fetch('/pos/clear', { method: 'POST' });
            loadCart();
            discountInput.value = '0.00';
        } catch (error) {
            console.error('Error clearing cart:', error);
        }
    }

    async function processCheckout() {
        if (currentCartTotal === 0) {
            alert('Add products to the cart before checking out.');
            return;
        }

        btnCheckout.disabled = true;
        btnCheckout.innerHTML = 'Processing...';

        const discount = parseFloat(discountInput.value) || 0;
        const finalAmount = currentCartTotal - discount;

        const payload = {
            discount: discount,
            payments: [
                {
                    method: paymentMethod.value,
                    amount: finalAmount,
                    installments: 1
                }
            ]
        };

        try {
            const response = await fetch('/pos/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                alert(`Sale completed successfully! Invoice #${data.sale_id}`);
                loadCart();
                discountInput.value = '0.00';
            } else {
                alert(`Failed to complete sale: ${data.error}`);
            }
        } catch (error) {
            console.error('Checkout error:', error);
            alert('A network error occurred. Please try again.');
        } finally {
            btnCheckout.disabled = false;
            btnCheckout.innerHTML = 'Complete Sale';
        }
    }

    function updateTotalsDisplay() {
        const discount = parseFloat(discountInput.value) || 0;
        const total = currentCartTotal - discount;
        
        cartSubtotal.innerText = `$ ${currentCartTotal.toFixed(2)}`;
        cartTotal.innerText = `$ ${Math.max(0, total).toFixed(2)}`;
    }
});