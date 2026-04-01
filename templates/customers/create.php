<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Customer Details</h5>
                <a href="/customers" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                
                <form action="/customers/store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="name" class="form-label">Full Name / Company Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
                        </div>
                        <div class="col-md-5">
                            <label for="document" class="form-label">Document (CPF/CNPJ) *</label>
                            <input type="text" class="form-control mask-doc" id="document" name="document" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control mask-phone" id="phone" name="phone">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Full Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Save Customer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="/js/masks.js"></script>