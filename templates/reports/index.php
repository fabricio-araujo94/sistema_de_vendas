<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h2>Management Dashboard</h2>
    <span class="text-muted">Last 30 Days Overview</span>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm bg-primary text-white">
            <div class="card-body py-4 text-center">
                <h6 class="card-title text-uppercase mb-2 opacity-75">30-Day Revenue</h6>
                <h2 class="display-6 fw-bold mb-0">$ <?= number_format($recentRevenue, 2) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white pt-3 pb-2 border-bottom-0">
                <h5 class="card-title text-danger mb-0">Low Stock Alerts</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Current</th>
                            <th class="text-center">Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lowStock)): ?>
                            <tr><td colspan="3" class="text-center text-muted">All stock levels are optimal.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lowStock as $item): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($item['name']) ?></td>
                                    <td class="text-center text-danger fw-bold"><?= $item['current_stock'] ?></td>
                                    <td class="text-center text-muted"><?= $item['minimum_stock'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white pt-3 pb-2 border-bottom-0">
                <h5 class="card-title text-success mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty Sold</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topProducts)): ?>
                            <tr><td colspan="3" class="text-center text-muted">No sales data available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="text-center"><?= $product['total_sold'] ?></td>
                                    <td class="text-end">$ <?= number_format($product['total_revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white pt-3 pb-2 border-bottom-0">
                <h5 class="card-title text-info mb-0">Performance by Seller</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Seller Name</th>
                            <th class="text-center">Total Sales (Count)</th>
                            <th class="text-end">Total Revenue Generated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($salesBySeller)): ?>
                            <tr><td colspan="3" class="text-center text-muted">No sales data available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($salesBySeller as $seller): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($seller['seller_name']) ?></td>
                                    <td class="text-center"><?= $seller['total_sales'] ?></td>
                                    <td class="text-end text-success fw-bold">$ <?= number_format($seller['total_revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>