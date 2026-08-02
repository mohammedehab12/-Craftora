-- Craftora Database Schema
CREATE DATABASE IF NOT EXISTS craftora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE craftora;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    stock INT NOT NULL DEFAULT 0,
    category VARCHAR(100) DEFAULT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Cart
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
) ENGINE=InnoDB;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50) NOT NULL DEFAULT 'cash_on_delivery',
    shipping_address VARCHAR(255) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Order items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- Sample products
INSERT INTO products (name, description, price, image, stock, category, featured) VALUES
('Handwoven Basket', 'A sturdy handwoven basket made from natural fibers, perfect for storage or decor.', 24.99, 'images/products/basket.jpg', 15, 'Home Decor', 1),
('Lavender Bath Salts', 'Relaxing bath salts infused with lavender essential oil.', 12.50, 'images/products/bath-salts.jpg', 30, 'Bath & Body', 0),
('Shea Body Cream', 'Rich, moisturizing body cream made with shea butter.', 15.00, 'images/products/body-cream.jpg', 25, 'Bath & Body', 1),
('Embroidered Cushion', 'Hand-embroidered decorative cushion cover.', 19.99, 'images/products/cushion.jpg', 20, 'Home Decor', 0),
('Leather Belt', 'Genuine leather belt, hand-stitched and finished.', 29.99, 'images/products/leather-belt.jpg', 10, 'Accessories', 1),
('Macrame Wall Hanging', 'Boho-style macrame wall hanging, handmade with cotton cord.', 22.00, 'images/products/macrame.jpg', 12, 'Home Decor', 0),
('Ceramic Mug', 'Hand-thrown ceramic mug, unique glaze finish.', 9.99, 'images/products/mug.jpg', 40, 'Kitchen', 0),
('Natural Soap Bar', 'Cold-processed natural soap bar, gentle on skin.', 6.50, 'images/products/soap.jpg', 50, 'Bath & Body', 0),
('Woven Sun Hat', 'Lightweight woven sun hat, perfect for summer.', 17.99, 'images/products/sun-hat.jpg', 18, 'Accessories', 0),
('Wall Tapestry', 'Large decorative wall tapestry with tribal pattern.', 27.50, 'images/products/wall-tapestry.jpg', 8, 'Home Decor', 1),
('Handmade Wallet', 'Compact handmade leather wallet with card slots.', 21.00, 'images/products/wallet.jpg', 22, 'Accessories', 0),
('Wooden Serving Tray', 'Solid wood serving tray with carved handles.', 18.50, 'images/products/wooden-tray.jpg', 14, 'Kitchen', 0);
