CREATE DATABASE IF NOT EXISTS sales_system
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sales_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Armazena o hash
    role ENUM('admin', 'seller') DEFAULT 'seller',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    document VARCHAR(20) NOT NULL UNIQUE, -- CPF ou CNPJ
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    document VARCHAR(20) NOT NULL UNIQUE, -- CNPJ
    contact VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    cost_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    current_stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 0,
    supplier_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_supplier FOREIGN KEY (supplier_id)
        REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('completed', 'canceled') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sale_customer FOREIGN KEY (customer_id)
        REFERENCES customers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_sale_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_sale FOREIGN KEY (sale_id)
        REFERENCES sales(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_product FOREIGN KEY (product_id)
        REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'debit_card', 'pix', 'boleto') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    installments INT DEFAULT 1,
    status ENUM('paid', 'pending', 'refunded') DEFAULT 'paid',
    CONSTRAINT fk_payment_sale FOREIGN KEY (sale_id)
        REFERENCES sales(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL UNIQUE,
    invoice_number VARCHAR(50) NOT NULL,
    access_key VARCHAR(44) NOT NULL UNIQUE,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    xml_data TEXT,
    CONSTRAINT fk_invoice_sale FOREIGN KEY (sale_id)
        REFERENCES sales(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Password: 'admin123'
INSERT INTO users (name, email, password, role)
VALUES ('System Admin', 'admin@system.com', '$2y$10$w4O20wXWz9.oXfCXZc4d/.vE.w8Z4yT2s1Yv7B4N9mQ1vY5qL8u.', 'admin');
