<meta name="csrf-token" content="<?= $csrfToken ?>">

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
                    <table class="table table-hover mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45%;">Product</th>
                                <th style="width: 20%;">Stock</th>
                                <th style="width: 15%;">Price</th>
                                <th style="width: 20%;" class="text-end">Action</th>
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
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Customer (Optional)</label>
                    <div class="input-group">
                        <input type="text" id="customerSearch" class="form-control form-control-sm" placeholder="Search by name or document..." autocomplete="off">
                        <input type="hidden" id="selectedCustomerId">
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="btnClearCustomer">X</button>
                    </div>
                    <ul id="customerResults" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></ul>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-5">Subtotal:</span>
                    <span class="fs-5 fw-bold" id="cartSubtotal">$ 0.00</span>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted small mb-1">Discount ($)</label>
                    <input type="number" id="discountInput" class="form-control form-control-sm" value="0.00" min="0" step="0.01">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2 text-primary">
                    <span class="fs-5 fw-bold">Total to Pay:</span>
                    <span class="fs-4 fw-bold" id="cartTotal">$ 0.00</span>
                </div>

                <hr>

                <h6 class="text-muted mb-2">Payments</h6>
                
                <div class="row g-2 mb-2">
                    <div class="col-5">
                        <select id="paymentMethod" class="form-select form-select-sm">
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <input type="number" id="paymentAmount" class="form-control form-control-sm" placeholder="Amount" step="0.01">
                    </div>
                    <div class="col-3">
                        <button class="btn btn-sm btn-secondary w-100" id="btnAddPayment">Add</button>
                    </div>
                </div>

                <ul class="list-group list-group-flush mb-3" id="paymentList">
                    </ul>

                <div class="d-flex justify-content-between align-items-center mb-3 text-danger">
                    <span class="fs-6 fw-bold">Remaining Balance:</span>
                    <span class="fs-5 fw-bold" id="remainingBalance">$ 0.00</span>
                </div>

                <div class="d-grid">
                    <button class="btn btn-success btn-lg" id="btnCheckout" disabled>Complete Sale</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = "<?= $_SESSION['csrf_token'] ?? '' ?>";
</script>

<script src="/js/pos.js"></script>