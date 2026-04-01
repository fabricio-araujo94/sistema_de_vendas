<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Sales System' : 'Sales System' ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body class="bg-light">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show flash-message shadow-sm" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash']); ?> <script>
                setTimeout(() => {
                    const flash = document.querySelector('.flash-message');
                    if (flash) {
                        flash.classList.remove('show');
                        setTimeout(() => flash.remove(), 150);
                    }
                }, 4000);
            </script>
        <?php endif; ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container">
                <a class="navbar-brand" href="/">Sales System</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/pos">POS (Cart)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/customers">Customers</a>
                        </li>

                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/products">Inventory</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/suppliers">Suppliers</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/users">Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/reports">Reports</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <span class="nav-link text-white">
                                <small class="badge bg-secondary me-1"><?= strtoupper($_SESSION['user_role'] ?? 'USER') ?></small>
                                <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger fw-bold" href="/logout">Logout</a>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <main class="container">
