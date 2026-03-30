<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h2>Inventory Managemente</h2>
    <a href="/products/create" class="btn btn-primary">+ New Product</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th>Stock</th>
                        <th>Min. Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No products found. Start by adding a new one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p->getId()) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($p->getName()) ?></td>
                                <td>$ <?= number_format($p->getCostPrice(), 2) ?></td>
                                <td>$ <?= number_format($p->getSellingPrice(), 2) ?></td>
                                <td>
                                    <span class="badge <?= $p->getCurrentStock() <= $p->getMinimumStock() ? 'bg-danger' : 'bg-success' ?>">
                                        <?= htmlspecialchars($p->getCurrentStock()) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($p->getMinimumStock()) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>