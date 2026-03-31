<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">User Details</h5>
                <a href="/users" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
            <div class="card-body">
                
                <form action="/users/store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" required autocomplete="off">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Temporary Password *</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                            <div class="form-text">Min 6 characters.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">System Role *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="seller">Seller (Standard Access)</option>
                                <option value="admin">Administrator (Full Access)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Create User</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>