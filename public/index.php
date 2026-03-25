<?php

session_start();

require_once __DIR__ . "/../vendor/autoload.php";

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
$router->post("/pos/checkout", "PointOfSaleController@checkout");

$requestUri = $_SERVER["REQUEST_URI"];
$requestMethod = $_SERVER["REQUEST_METHOD"];

$router->dispatch($requestUri, $requestMethod);
