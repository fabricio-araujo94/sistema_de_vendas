<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Product Details</h5>
                <a href="/products" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                
                <form action="/products/store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="cost_price" class="form-label">Cost Price ($) *</label>
                            <input type="number" class="form-control" id="cost_price" name="cost_price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="selling_price" class="form-label">Selling Price ($) *</label>
                            <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="current_stock" class="form-label">Initial Stock *</label>
                            <input type="number" class="form-control" id="current_stock" name="current_stock" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="minimum_stock" class="form-label">Minimum Stock Alert</label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" min="0" value="5">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Save Product</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>