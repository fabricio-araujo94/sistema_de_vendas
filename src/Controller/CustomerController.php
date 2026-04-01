<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\CustomerDAO;
use App\Model\Customer;

class CustomerController extends BaseController
{
    private CustomerDAO $customerDAO;

    public function __construct()
    {
        AuthMiddleware::checkAuthentication();
        $this->customerDAO = new CustomerDAO();
    }

    public function index(): void
    {
        $customers = $this->customerDAO->findAll();

        $this->render('customers/index', [
            'pageTitle' => 'Customer Management',
            'customers' => $customers
        ]);
    }

    public function create(): void
    {
        $this->render('customers/create', [
            'pageTitle' => 'Add New Customer'
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/customers');
        }

        $customer = new Customer(
            filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS),
            filter_input(INPUT_POST, 'document', FILTER_SANITIZE_SPECIAL_CHARS) // CPF or CNPJ
        );

        $customer->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $customer->setPhone(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS));
        $customer->setAddress(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS));

        if ($this->customerDAO->insert($customer)) {
            $this->setFlash('success', 'Customer registered successfully!');
            $this->redirect('/customers');
        } else {
            $this->setFlash('danger', 'Failed to register the customer.');
            $this->redirect('/customers/create');
        }
    }
}