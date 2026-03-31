<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Supplier Details</h5>
                <a href="/suppliers" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                
                <form action="/suppliers/store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="company_name" class="form-label">Company Name *</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" required autocomplete="off">
                        </div>
                        <div class="col-md-5">
                            <label for="document" class="form-label">Document (CNPJ) *</label>
                            <input type="text" class="form-control" id="document" name="document" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact Person / Phone</label>
                        <input type="text" class="form-control" id="contact" name="contact" placeholder="e.g. John Doe - (55) 9999-9999">
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Full Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Save Supplier</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>