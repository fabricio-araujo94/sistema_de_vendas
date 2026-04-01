<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\ProductDAO;
use App\DAO\SaleDAO;
use App\DAO\CustomerDAO;
use App\Model\Sale;
use App\Model\SaleItem;
use App\Model\Payment;
use Exception;

class PointOfSaleController extends BaseController
{
    private ProductDAO $productDAO;
    private SaleDAO $saleDAO;

    public function __construct()
    {
        AuthMiddleware::checkAuthentication();

        $this->productDAO = new ProductDAO();
        $this->saleDAO = new SaleDAO();

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function index(): void
    {
        $this->render('pos/index', [
            'pageTitle' => 'Point of Sale (POS)',
            'userName'  => $_SESSION['user_name']
        ]);
    }

    public function searchProducts(): void
    {
        $searchTerm = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        
        if (strlen($searchTerm) < 2) {
            $this->jsonResponse(['error' => 'Search term too short'], 400);
        }

        $products = $this->productDAO->searchByName($searchTerm);
        
        $result = array_map(function($p) {
            return [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'price' => $p->getSellingPrice(),
                'stock' => $p->getCurrentStock()
            ];
        }, $products);

        $this->jsonResponse($result);
    }

    public function addToCart(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $productId = (int) ($data['product_id'] ?? 0);
        $quantityToAdd = (int) ($data['quantity'] ?? 1);

        if ($productId <= 0 || $quantityToAdd <= 0) {
            $this->jsonResponse(['error' => 'Invalid product or quantity must be greater than zero.'], 400);
        }

        $product = $this->productDAO->findById($productId);

        if (!$product) {
            $this->jsonResponse(['error' => 'Product not found in the database.'], 404);
        }

        $currentQuantityInCart = 0;
        if (isset($_SESSION['cart'][$productId])) {
            $currentQuantityInCart = $_SESSION['cart'][$productId]['quantity'];
        }

        $totalRequestedQuantity = $currentQuantityInCart + $quantityToAdd;

        if ($product->getCurrentStock() < $totalRequestedQuantity) {
            $this->jsonResponse([
                'error' => "Insufficient stock. You have {$currentQuantityInCart} in cart, and only {$product->getCurrentStock()} available in stock."
            ], 400);
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $totalRequestedQuantity;
            $_SESSION['cart'][$productId]['subtotal'] = $totalRequestedQuantity * $product->getSellingPrice();
        } else {
            $_SESSION['cart'][$productId] = [
                'id'       => $product->getId(),
                'name'     => $product->getName(),
                'price'    => $product->getSellingPrice(),
                'quantity' => $quantityToAdd,
                'subtotal' => $quantityToAdd * $product->getSellingPrice()
            ];
        }

        $this->jsonResponse([
            'success' => true, 
            'message' => 'Product successfully added to cart',
            'cart'    => array_values($_SESSION['cart'])
        ]);
    }

    public function getCart(): void
    {
        $this->jsonResponse(array_values($_SESSION['cart']));
    }

    public function clearCart(): void
    {
        $_SESSION['cart'] = [];
        $this->jsonResponse(['success' => true, 'message' => 'Cart cleared']);
    }

    public function removeCartItem(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = (int) ($data['product_id'] ?? 0);

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        $this->jsonResponse(['success' => true, 'cart' => array_values($_SESSION['cart'])]);
    }

    public function updateCartItem(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 0);

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]); 
        } elseif (isset($_SESSION['cart'][$productId])) {
            $product = $this->productDAO->findById($productId);
            
            if ($product->getCurrentStock() < $quantity) {
                $this->jsonResponse(['error' => 'Insufficient stock for this quantity.'], 400);
            }

            $_SESSION['cart'][$productId]['quantity'] = $quantity;
            $_SESSION['cart'][$productId]['subtotal'] = $quantity * $product->getSellingPrice();
        }

        $this->jsonResponse(['success' => true, 'cart' => array_values($_SESSION['cart'])]);
    }

    public function checkout(): void
    {
        if (empty($_SESSION['cart'])) {
            $this->jsonResponse(['error' => 'Cart is empty'], 400);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $paymentsData = $data['payments'] ?? []; 
        $customerId = !empty($data['customer_id']) ? (int) $data['customer_id'] : null;
        $discount = isset($data['discount']) ? (float) $data['discount'] : 0.0;

        if (empty($paymentsData)) {
            $this->jsonResponse(['error' => 'No payment method provided'], 400);
        }

        $cartTotal = 0.0;
        foreach ($_SESSION['cart'] as $item) {
            $cartTotal += $item['subtotal'];
        }
        
        $finalAmount = $cartTotal - $discount;
        $totalPaid = array_sum(array_column($paymentsData, 'amount'));

        if (round($totalPaid, 2) < round($finalAmount, 2)) {
            $this->jsonResponse(['error' => 'Total payments are less than the final amount'], 400);
        }

        try {
            $userId = $_SESSION['user_id'];
            $sale = new Sale($userId, $customerId, $discount);

            foreach ($_SESSION['cart'] as $cartItem) {
                $sale->addItem(new SaleItem($cartItem['id'], $cartItem['quantity'], $cartItem['price']));
            }

            foreach ($paymentsData as $p) {
                $sale->addPayment(new Payment($p['method'], (float) $p['amount'], (int) ($p['installments'] ?? 1)));
            }

            $this->saleDAO->create($sale);
            $_SESSION['cart'] = [];

            $this->jsonResponse([
                'success' => true, 
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->getId()
            ]);

        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }

    public function searchCustomers(): void {
        $searchTerm = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        if (strlen($searchTerm) < 2) {
            $this->jsonResponse(['error' => 'Search term too short'], 400);
        }

        $customerDAO = new CustomerDAO();
        $customers = $customerDAO->searchByNameOrDocument($searchTerm);

        $this->jsonResponse($customers);
    }
}