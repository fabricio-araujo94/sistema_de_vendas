<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-8 col-lg-6">
        
        <div class="d-flex justify-content-between mb-3 d-print-none">
            <a href="/pos" class="btn btn-outline-secondary">&larr; Back to POS</a>
            <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
        </div>

        <div class="card shadow-sm border-2 border-dark" id="printable-invoice">
            <div class="card-body p-4">
                
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-0">SALES SYSTEM INC.</h3>
                    <p class="text-muted small mb-0">123 Business Road, Tech City - TX</p>
                    <p class="text-muted small">CNPJ: 00.000.000/0001-00</p>
                    
                    <h5 class="mt-3 border-top border-bottom py-2">
                        RECEIPT / INVOICE: <?= htmlspecialchars($data['invoice_number']) ?>
                    </h5>
                </div>

                <div class="row mb-4 small">
                    <div class="col-sm-6">
                        <strong>Date:</strong> <?= date('d/m/Y H:i', strtotime($data['issued_at'])) ?><br>
                        <strong>Sale ID:</strong> #<?= htmlspecialchars($data['sale_id']) ?>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                        <strong>Customer:</strong> <?= htmlspecialchars($data['customer_name'] ?? 'CONSUMIDOR FINAL') ?><br>
                        <?php if ($data['customer_document']): ?>
                            <strong>Doc:</strong> <?= htmlspecialchars($data['customer_document']) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <table class="table table-sm border-dark small mb-4">
                    <thead>
                        <tr class="border-bottom border-dark">
                            <th>ITEM</th>
                            <th class="text-center">QTY</th>
                            <th class="text-end">PRICE</th>
                            <th class="text-end">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end">$ <?= number_format($item['unit_price'], 2) ?></td>
                                <td class="text-end">$ <?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mb-4">
                    <div style="width: 200px;">
                        <div class="d-flex justify-content-between small">
                            <span>Discount:</span>
                            <span>$ <?= number_format($data['discount'], 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-2 border-top border-dark pt-1">
                            <span>TOTAL:</span>
                            <span>$ <?= number_format($data['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5 pt-3 border-top">
                    <p class="small text-muted mb-1">Access Key for Consultation</p>
                    <div class="font-monospace bg-light p-2 border rounded small" style="letter-spacing: 1px;">
                        <?= htmlspecialchars(implode(' ', str_split($data['access_key'], 4))) ?>
                    </div>
                    <p class="mt-3 mb-0"><strong>Thank you for your purchase!</strong></p>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printable-invoice, #printable-invoice * { visibility: visible; }
        #printable-invoice { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
    }
</style>