<?php

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

use App\Core\Database;

try {
    echo "Connecting to the database...\n";
    $db = Database::getInstance()->getConnection();

    echo "Cleaning up existing data...\n";
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE sale_items;");
    $db->exec("TRUNCATE TABLE payments;");
    $db->exec("TRUNCATE TABLE invoices;");
    $db->exec("TRUNCATE TABLE sales;");
    $db->exec("TRUNCATE TABLE products;");
    $db->exec("TRUNCATE TABLE customers;");
    $db->exec("TRUNCATE TABLE suppliers;");
    $db->exec("TRUNCATE TABLE users;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    
    echo "Seeding Users...\n";
    $stmtUser = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $passwordHash = password_hash('123456', PASSWORD_DEFAULT);
    
    $stmtUser->execute(['Admin System', 'admin@example.com', $passwordHash, 'admin']);
    $adminId = $db->lastInsertId();
    
    $stmtUser->execute(['John Seller', 'seller@example.com', $passwordHash, 'seller']);
    $sellerId = $db->lastInsertId();

    
    echo "Seeding Suppliers...\n";
    $stmtSupplier = $db->prepare("INSERT INTO suppliers (company_name, document, contact, address) VALUES (?, ?, ?, ?)");
    
    $stmtSupplier->execute(['Tech Distribuidora S/A', '11.111.111/0001-11', 'Carlos - (11) 9999-9999', 'Rua da Tecnologia, 100 - SP']);
    $supplier1Id = $db->lastInsertId();
    
    $stmtSupplier->execute(['Global Importações', '22.222.222/0001-22', 'Maria - (21) 8888-8888', 'Av. das Importações, 500 - RJ']);
    $supplier2Id = $db->lastInsertId();

    
    echo "Seeding Customers...\n";
    $stmtCustomer = $db->prepare("INSERT INTO customers (name, document, email, phone) VALUES (?, ?, ?, ?)");
    
    $stmtCustomer->execute(['Alice Wonderland', '123.456.789-00', 'alice@email.com', '(85) 91111-1111']);
    $customerId1 = $db->lastInsertId();
    
    $stmtCustomer->execute(['Bob Builder', '987.654.321-99', 'bob@email.com', '(85) 92222-2222']);
    $customerId2 = $db->lastInsertId();

    
    echo "Seeding Products...\n";
    $stmtProduct = $db->prepare("INSERT INTO products (name, description, cost_price, selling_price, current_stock, minimum_stock, supplier_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmtProduct->execute(['Wireless Mouse Logitech', 'Ergonomic 2.4GHz wireless mouse', 45.00, 89.90, 50, 10, $supplier1Id]);
    $stmtProduct->execute(['Mechanical Keyboard Redragon', 'RGB gaming keyboard with blue switches', 120.00, 249.90, 30, 5, $supplier1Id]);
    $stmtProduct->execute(['Monitor Dell 24"', 'IPS Full HD 75Hz Monitor', 650.00, 950.00, 8, 3, $supplier1Id]); // Estoque OK
    
    $stmtProduct->execute(['SSD Kingston 1TB', 'NVMe PCIe Gen 4.0', 250.00, 399.90, 2, 5, $supplier2Id]); // Estoque Baixo (para o Dashboard)
    $stmtProduct->execute(['Headset HyperX Cloud', '7.1 Surround Gaming Headset', 280.00, 450.00, 15, 5, $supplier2Id]);
    $stmtProduct->execute(['USB-C Cable 2M', 'Fast charging braided cable', 15.00, 35.00, 100, 20, $supplier2Id]);

    echo "\n========================================\n";
    echo "Database seeded successfully!\n";
    echo "========================================\n";
    echo "Login Credentials:\n";
    echo "Admin  : admin@example.com / 123456\n";
    echo "Seller : seller@example.com / 123456\n";
    echo "========================================\n";

} catch (Exception $e) {
    echo "\n[ERROR] Failed to seed database:\n";
    echo $e->getMessage() . "\n";
}