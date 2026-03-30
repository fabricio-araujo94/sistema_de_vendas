<div class="row mt-3">
    <div class="col-md-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white pt-3 pb-2">
                <h5 class="card-title">Search Products</h5>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Type product name (min. 2 characters)..." autocomplete="off">
                    <button type="button" id="btnSearch" class="btn btn-outline-secondary">Search</button>
                </div>

                <div class="table-responsive">
                    <table class="table-light">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="searchResults">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Search for a product to add to cart.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Shopping Cart</h5>
                <button class="btn btn-sm btn-light text-danger" id="btnClearCart">Clear</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-borderless table-striped mb-0">
                        <tbody id="cartItems">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-5">Subtotal:</span>
                    <span class="fs-5 fw-bold" id="cartSubtotal">$ 0.00</span>
                </div>

                <hr>

                <div class="mb-3">
                    <label for="paymentMethod" class="form-label text-muted small mb-1">Payment Method</label>
                    <select name="paymentMethod" id="paymentMethod" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="pix">PIX</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="discountInput" class="form-label text-muted small mb-1">Discount ($)</label>
                    <input type="number" name="discountInput" id="discountInput" class="form-control" value="0.00" min="0" step="0.01">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 text-success">
                    <span class="fs-4 fw-bold">Total:</span>
                    <span class="fs-3 fw-bold" id="cartTotal">$ 0.00</span>
                </div>

                <div class="d-grid">
                    <button class="btn btn-success btn-lg" id="btnCheckout">Complete Sale</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/pos.js"></script>