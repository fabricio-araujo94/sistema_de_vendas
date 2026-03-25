<?php

namespace App\Controller;

abstract class BaseController
{
    protected function render(string $viewPath, array $data = []): void
    {
        extract($data);

        $file = __DIR__ . "/../../templates/" . $viewPath . ".php";

        if (file_exists($file)) {
            require_once __DIR__ . "/../../templates/layout/header.php";

            require_once $file;

            require_once __DIR__ . "/../../templates/layout/footer.php";
        } else {
            http_response_code(404);
            die("View file not found: {$file}");
        }
    }

    protected function redirect(string $url): void
    {
        header("Location: " . $url);
        exit();
    }

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($data);
        exit();
    }
}
