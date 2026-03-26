<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">Sales System Login</h4>
            </div>
            <div class="card-body p-4">
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="/login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus placeholder="name@example.com">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="********">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3 text-muted">
            <small>Admin login: "admin@system.com / admin123</small>
        </div>
    </div>
</div>