<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\ProductDAO;
use App\DAO\SaleDAO;
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
        $quantity = (int) ($data['quantity'] ?? 1);

        if ($productId <= 0 || $quantity <= 0) {
            $this->jsonResponse(['error' => 'Invalid product or quantity'], 400);
        }

        $product = $this->productDAO->findById($productId);

        if (!$product) {
            $this->jsonResponse(['error' => 'Product not found'], 404);
        }

        if ($product->getCurrentStock() < $quantity) {
            $this->jsonResponse(['error' => 'Insufficient stock'], 400);
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
            $_SESSION['cart'][$productId]['subtotal'] = $_SESSION['cart'][$productId]['quantity'] * $product->getSellingPrice();
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getSellingPrice(),
                'quantity' => $quantity,
                'subtotal' => $quantity * $product->getSellingPrice()
            ];
        }

        $this->jsonResponse([
            'success' => true, 
            'message' => 'Product added to cart',
            'cart' => array_values($_SESSION['cart']) 
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

    public function checkout(): void {
        if (empty($_SESSION['cart'])) {
            $this->jsonResponse(['error' => 'Cart is empty'], 400);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $paymentsData = $data['payments'] ?? [];
        $customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;
        $discount = isset($data['discount']) ? (float) $data['discount'] : 0.0;

        if (empty($paymentsData)) {
            $this->jsonResponse(['error' => 'No payment method provided'], 400);
        }

        try {
            $userId = $_SESSION['user_id'];
            $sale = new Sale($userId, $customerId, $discount);

            $totalItems = 0.0;

            foreach ($_SESSION['cart'] as $cartItem) {
                $product = $this->productDAO->findById($cartItem['id']);

                if (!$product) {
                    throw new Exception("Product not found: {$cartItem['id']}");
                }

                if ($product->getCurrentStock() < $cartItem['quantity']) {
                    throw new Exception("Insufficient stock for product: {$product->getName()}");
                }

                $unitPrice = $product->getSellingPrice();

                $saleItem = new SaleItem(
                    $product->getId(),
                    $cartItem['quantity'],
                    $unitPrice
                );

                $sale->addItem($saleItem);
                $totalItems += $saleItem->getSubtotal();
            }

            $totalPayments = 0.0;

            foreach ($paymentsData as $p) {
                $amount = (float) $p['amount'];
                $totalPayments += $amount;

                $payment = new Payment(
                    $p['method'],
                    $amount,
                    (int) ($p['installments'] ?? 1)
                );

                $sale->addPayment($payment);
            }

            $expectedTotal = $totalItems - $discount;

            if (abs($expectedTotal - $totalPayments) > 0.01) {
                throw new Exception("Payment total does not match sale total");
            }

            $this->saleDAO->create($sale);

            $_SESSION['cart'] = [];

            $this->jsonResponse([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->getId()
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'error' => 'Checkout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}