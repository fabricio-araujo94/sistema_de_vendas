<?php

if (php_sapi_name() === 'cli-server') {
    $path = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($path)) {
        return false; 
    }
}

session_start();

require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

use App\Core\Router;

$router = new Router();

$router->get("/", "AuthController@loginForm");
$router->post("/login", "AuthController@authenticate");
$router->get("/logout", "AuthController@logout");

$router->get("/products", "ProductController@index");
$router->get("/products/create", "ProductController@create");
$router->post("/products/store", "ProductController@store");

$router->get("/pos", "PointOfSaleController@index");
$router->post("/pos/add-item", "PointOfSaleController@addItem");
$router->get('/pos/search', 'PointOfSaleController@searchProducts');
$router->get('/pos/cart', 'PointOfSaleController@getCart');
$router->post('/pos/add', 'PointOfSaleController@addToCart');
$router->post('/pos/clear', 'PointOfSaleController@clearCart');
$router->post('/pos/remove-item', 'PointOfSaleController@removeCartItem');
$router->post('/pos/update-item', 'PointOfSaleController@updateCartItem');
$router->post('/pos/checkout', 'PointOfSaleController@checkout');

$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users/store', 'UserController@store');

$router->get('/customers', 'CustomerController@index');
$router->get('/customers/create', 'CustomerController@create');
$router->post('/customers/store', 'CustomerController@store');

$router->get('/suppliers', 'SupplierController@index');
$router->get('/suppliers/create', 'SupplierController@create');
$router->post('/suppliers/store', 'SupplierController@store');

$router->get('/reports', 'ReportController@index');

$router->get('/invoice', 'InvoiceController@show');

$requestUri = $_SERVER["REQUEST_URI"];
$requestMethod = $_SERVER["REQUEST_METHOD"];

$router->dispatch($requestUri, $requestMethod);
