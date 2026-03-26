<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;

class PointOfSaleController extends BaseController
{
    public function __construct()
    {
        AuthMiddleware::checkAuthentication();
    }

    public function index()
    {
        $userName = $_SESSION['user_name'];

        $this->render("pos/index". [
            'pageTitle' => 'Point of Sale',
            'userName' => $userName
        ]);
    }
}