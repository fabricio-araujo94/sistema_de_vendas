<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h2>Suppliers</h2>
    <a href="/suppliers/create" class="btn btn-primary">+ New Supplier</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Document (CNPJ)</th>
                        <th>Contact</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No suppliers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s->getId()) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($s->getCompanyName()) ?></td>
                                <td><?= htmlspecialchars($s->getDocument()) ?></td>
                                <td><?= htmlspecialchars($s->getContact() ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($s->getCreatedAt())) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>