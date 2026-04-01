document.addEventListener('DOMContentLoaded', () => { 
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const cartItems = document.getElementById('cartItems');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartTotal = document.getElementById('cartTotal'); // Corrigido: era 'discountInput'
    const discountInput = document.getElementById('discountInput');
    const paymentMethod = document.getElementById('paymentMethod');
    const btnCheckout = document.getElementById('btnCheckout');
    const btnClearCart = document.getElementById('btnClearCart');
    const customerSearch = document.getElementById('customerSearch');
    const customerResults = document.getElementById('customerResults');
    const selectedCustomerId = document.getElementById('selectedCustomerId');
    const btnClearCustomer = document.getElementById('btnClearCustomer');
    const paymentAmount = document.getElementById('paymentAmount');
    const btnAddPayment = document.getElementById('btnAddPayment');
    const paymentList = document.getElementById('paymentList');
    const remainingBalanceDisplay = document.getElementById('remainingBalance');

    let currentCartTotal = 0.0;
    let addedPayments = [];

    loadCart();

    searchInput.addEventListener('input', async (e) => {
        const term = e.target.value;
        if (term.length >= 2) {
            searchProducts(term);
        } else {
            searchResults.innerHTML = '<tr><td colspan=4 class="text-center text-muted">Type at least 2 characters...</td></tr>';
        }
    });

    discountInput.addEventListener('input', updateTotalsDisplay);

    btnClearCart.addEventListener('click', clearCart);

    btnCheckout.addEventListener('click', processCheckout);

    customerSearch.addEventListener('input', async (e) => {
        const term = e.target.value;
        if (term.length >= 2) {
            const customers = await searchCustomers(term);
            renderCustomerResults(customers);
        } else {
            customerResults.style.display = 'none';
        }
    });

    btnClearCustomer.addEventListener('click', () => {
        selectedCustomerId.value = '';
        customerSearch.value = '';
        customerSearch.disabled = false;
        customerResults.style.display = 'none';
    });

    btnAddPayment.addEventListener('click', () => {
        const method = paymentMethod.value;
        const methodName = paymentMethod.options[paymentMethod.selectedIndex].text;
        const amount = parseFloat(paymentAmount.value);

        if (isNaN(amount) || amount <= 0) return;

        addedPayments.push({ method: method, name: methodName, amount: amount });
        paymentAmount.value = ''; // Limpa o input
        renderPayments();
    });

    window.removePayment = function(index) {
        addedPayments.splice(index, 1);
        renderPayments();
    };

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
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const response = await fetch('/pos/add', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
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

    window.removeCartItem = async function(productId) {
        try {
            await fetch('/pos/remove-item', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ product_id: productId })
            });
            loadCart();
        } catch (error) { console.error(error); }
    };

    window.updateCartQty = async function(productId, newQuantity) {
        if (newQuantity <= 0) {
            removeCartItem(productId);
            return;
        }
        try {
            const response = await fetch('/pos/update-item', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ product_id: productId, quantity: newQuantity })
            });
            const data = await response.json();
            if (response.ok) {
                loadCart();
            } else {
                alert(`Error: ${data.error}`);
            }
        } catch (error) { console.error(error); }
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
                    <td class="fw-bold">${item.name} <br> 
                        <div class="input-group input-group-sm mt-1" style="width: 110px;">
                            <button class="btn btn-outline-secondary px-2" onclick="updateCartQty(${item.id}, ${item.quantity - 1})">-</button>
                            <input type="text" class="form-control text-center px-1" value="${item.quantity}" readonly>
                            <button class="btn btn-outline-secondary px-2" onclick="updateCartQty(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                    </td>
                    <td class="text-end align-middle fw-bold">
                        $ ${parseFloat(item.subtotal).toFixed(2)} <br>
                        <button class="btn btn-link text-danger p-0 mt-1 small text-decoration-none" onclick="removeCartItem(${item.id})">Remove</button>
                    </td>
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
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            await fetch('/pos/clear', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
            });
            loadCart();
            discountInput.value = '0.00';
        } catch (error) {
            console.error('Error clearing cart:', error);
        }
    }

    async function processCheckout() {
        btnCheckout.disabled = true;
        btnCheckout.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        const payload = {
            customer_id: selectedCustomerId.value || null,
            discount: parseFloat(discountInput.value) || 0,
            payments: addedPayments
        };

        try {
            const response = await fetch('/pos/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                window.open(`/invoice?id=${data.sale_id}`, '_blank');
                
                await fetch('/pos/clear', { method: 'POST', headers: {'X-CSRF-TOKEN': csrfToken} });
                addedPayments = [];
                btnClearCustomer.click();
                discountInput.value = '0.00';
                loadCart();
                searchInput.focus(); 
                
                btnCheckout.disabled = false;
                btnCheckout.innerHTML = 'Complete Sale';
            } else {
                alert(`Error: ${data.error}`);
                
                btnCheckout.disabled = false;
                btnCheckout.innerHTML = 'Complete Sale';
            }
        } catch (error) {
            console.error('Checkout error:', error);
            alert('A network error occurred.');
            
            btnCheckout.disabled = false;
            btnCheckout.innerHTML = 'Complete Sale';
        }
    }

    function updateTotalsDisplay() {
        const discount = parseFloat(discountInput.value) || 0;
        const total = Math.max(0, currentCartTotal - discount);
        
        const totalPaid = addedPayments.reduce((sum, p) => sum + p.amount, 0);
        const remaining = total - totalPaid;
        
        cartSubtotal.innerText = `$ ${currentCartTotal.toFixed(2)}`;
        cartTotal.innerText = `$ ${total.toFixed(2)}`;
        remainingBalanceDisplay.innerText = `$ ${Math.max(0, remaining).toFixed(2)}`;

        if (remaining > 0) {
            paymentAmount.value = remaining.toFixed(2);
        }

        updateButtonState(btnCheckout, currentCartTotal === 0 || remaining > 0, 'Complete Sale');
    }

    function updateButtonState(button, isDisabled, text) {
        button.disabled = isDisabled;
        button.innerText = text;
    }

    async function searchCustomers(term) {
        try {
            const response = await fetch(`/pos/customers?q=${encodeURIComponent(term)}`);
            return await response.json();
        } catch (error) {
            console.error('Error searching customers:', error);
            return [];
        }
    }

    function renderCustomerResults(customers) {
        customerResults.innerHTML = '';
        customerResults.style.display = customers.length > 0 ? 'block' : 'none';

        customers.forEach(c => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action py-1 small cursor-pointer';
            li.innerHTML = `${c.name} <span class="text-muted">(${c.document})</span>`;
            li.onclick = () => {
                selectedCustomerId.value = c.id;
                customerSearch.value = c.name;
                customerSearch.disabled = true;
                customerResults.style.display = 'none';
            };
            customerResults.appendChild(li);
        });
    }

    function renderPayments() {
        paymentList.innerHTML = '';
        
        addedPayments.forEach((p, index) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center py-1 small px-0 border-0';
            li.innerHTML = `
                <span>${p.name}</span>
                <span>$ ${p.amount.toFixed(2)} 
                    <button class="btn btn-link text-danger p-0 ms-2" onclick="removePayment(${index})">x</button>
                </span>
            `;
            paymentList.appendChild(li);
        });

        updateTotalsDisplay();
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault(); 
            if (!btnCheckout.disabled) {
                btnCheckout.click();
            } else {
                alert('Add products and match the payment amount before checking out (F2).');
            }
        }
    });

    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstAddButton = searchResults.querySelector('button.btn-primary');
            if (firstAddButton && !firstAddButton.disabled) {
                firstAddButton.click();
            }
        }
    });
});