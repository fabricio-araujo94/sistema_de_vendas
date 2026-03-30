<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\ReportDAO;

class ReportController extends BaseController
{
    private ReportDAO $reportDAO;

    public function __construct()
    {
        AuthMiddleware::checkAdminRole();
        $this->reportDAO = new ReportDAO();
    }

    public function index(): void
    {
        $lowStock = $this->reportDAO->getLowStockProducts();
        $topProducts = $this->reportDAO->getTopSellingProducts(5);
        $salesBySeller = $this->reportDAO->getSalesBySeller();
        $recentRevenue = $this->reportDAO->getRecentRevenue();

        $this->render('reports/index', [
            'pageTitle'     => 'Management Dashboard',
            'lowStock'      => $lowStock,
            'topProducts'   => $topProducts,
            'salesBySeller' => $salesBySeller,
            'recentRevenue' => $recentRevenue
        ]);
    }
}