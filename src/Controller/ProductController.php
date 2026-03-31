<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\ProductDAO;
use App\Model\Product;

class ProductController extends BaseController
{
    private ProductDAO $productDAO;

    public function __construct()
    {
        AuthMiddleware::checkAdminRole();
        $this->productDAO = new ProductDAO();
    }

    public function index(): void
    {
        $products = $this->productDAO->findAll();

        $this->render('products/index', [
            'pageTitle' => 'Inventory Management',
            'products'  => $products
        ]);
    }

    public function create(): void
    {
        $this->render('products/create', [
            'pageTitle' => 'Add New Product'
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/products');
        }

        $product = new Product(
            filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS),
            (float) filter_input(INPUT_POST, 'cost_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            (float) filter_input(INPUT_POST, 'selling_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            (int) filter_input(INPUT_POST, 'current_stock', FILTER_SANITIZE_NUMBER_INT),
            (int) filter_input(INPUT_POST, 'minimum_stock', FILTER_SANITIZE_NUMBER_INT)
        );
        
        $product->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));

        if ($this->productDAO->insert($product)) {
            $this->redirect('/products');
        } else {
            $this->redirect('/products/create');
        }
    }
}