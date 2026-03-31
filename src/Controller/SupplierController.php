<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\SupplierDAO;
use App\Model\Supplier;

class SupplierController extends BaseController
{
    private SupplierDAO $supplierDAO;

    public function __construct()
    {
        AuthMiddleware::checkAuthentication();
        $this->supplierDAO = new SupplierDAO();
    }

    public function index(): void
    {
        $suppliers = $this->supplierDAO->findAll();

        $this->render('suppliers/index', [
            'pageTitle' => 'Supplier Management',
            'suppliers' => $suppliers
        ]);
    }

    public function create(): void
    {
        $this->render('suppliers/create', [
            'pageTitle' => 'Add New Supplier'
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/suppliers');
        }

        $supplier = new Supplier(
            filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_SPECIAL_CHARS),
            filter_input(INPUT_POST, 'document', FILTER_SANITIZE_SPECIAL_CHARS)
        );

        $supplier->setContact(filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_SPECIAL_CHARS));
        $supplier->setAddress(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS));

        if ($this->supplierDAO->insert($supplier)) {
            $this->redirect('/suppliers');
        } else {
            $this->redirect('/suppliers/create');
        }
    }
}