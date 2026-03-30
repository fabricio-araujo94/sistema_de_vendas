<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h2>Customers</h2>
    <a href="/customers/create" class="btn btn-primary">+ New Customer</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Document</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No customers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c->getId()) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($c->getName()) ?></td>
                                <td><?= htmlspecialchars($c->getDocument()) ?></td>
                                <td><?= htmlspecialchars($c->getEmail() ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($c->getPhone() ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($c->getCreatedAt())) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>