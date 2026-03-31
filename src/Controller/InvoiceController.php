<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\InvoiceDAO;

class InvoiceController extends BaseController {
    public function __construct() {
        AuthMiddleware::checkAuthentication();
    }

    public function show(): void {
        $saleId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (!$saleId) {
            die("Invalid Sale ID.");
        }

        $invoiceDAO = new InvoiceDAO();
        $invoiceData = $invoiceDAO->getInvoiceDetails((int) $saleId);

        if (!$invoiceData) {
            http_response_code(404);
            die("Invoice not found.");
        }

        $this->render('invoices/show', [
            'pageTitle' => 'Invoice ' . $invoiceData['invoice_number'],
            'data'      => $invoiceData
        ]);
    }
}