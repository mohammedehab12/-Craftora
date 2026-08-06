-- =========================================================
-- Craftora Database Schema
-- Database name: Craftora
-- =========================================================

CREATE DATABASE IF NOT EXISTS Craftora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Craftora;

-- ---------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL UNIQUE,
    phone           VARCHAR(20)         NULL,
    password        VARCHAR(255)        NOT NULL,
    address         TEXT                NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: products
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150)        NOT NULL,
    description     TEXT                NULL,
    price           DECIMAL(10,2)       NOT NULL,
    category        VARCHAR(80)         NOT NULL,
    image           VARCHAR(255)        NOT NULL,
    stock           INT UNSIGNED        NOT NULL DEFAULT 0,
    featured        BOOLEAN             NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: cart
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED        NOT NULL,
    product_id      INT UNSIGNED        NOT NULL,
    quantity        INT UNSIGNED        NOT NULL DEFAULT 1,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_product (user_id, product_id),
    CONSTRAINT fk_cart_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: orders
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED        NOT NULL,
    total_price         DECIMAL(10,2)       NOT NULL,
    payment_method      VARCHAR(50)         NOT NULL,
    shipping_address    TEXT                NOT NULL,
    status              ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    order_date          TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: order_items
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED        NOT NULL,
    product_id      INT UNSIGNED        NOT NULL,
    quantity        INT UNSIGNED        NOT NULL,
    price           DECIMAL(10,2)       NOT NULL,
    CONSTRAINT fk_order_items_order   FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: contact_messages
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL,
    message         TEXT                NOT NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- Seed data: products
-- Existing 6 products use real images already in
-- images/products/. The additional products below use
-- placeholder image names -- the site will automatically
-- fall back to images/placeholder.jpg until real photos
-- are added to images/products/.
-- =========================================================

INSERT INTO products (name, description, price, category, image, stock, featured) VALUES
('Handwoven Leather Wallet', 'A genuine leather wallet, handcrafted with precision stitching and a timeless design.', 350.00, 'Accessories', 'wallet.jpg', 25, TRUE),
('Embroidered Throw Cushion', 'Soft cotton cushion cover with hand-embroidered patterns, perfect for any living room.', 220.00, 'Home Decor', 'cushion.jpg', 40, TRUE),
('Natural Handmade Soap', 'Cold-pressed soap made from natural oils and herbs, gentle on the skin.', 80.00, 'Bath & Body', 'soap.jpg', 100, TRUE),
('Hand-painted Ceramic Mug', 'A one-of-a-kind ceramic mug, hand-painted by local artisans.', 150.00, 'Kitchen', 'mug.jpg', 60, FALSE),
('Macrame Wall Hanging', 'Intricately knotted macrame wall art woven from natural cotton cord.', 280.00, 'Home Decor', 'macrame.jpg', 30, FALSE),
('Woven Storage Basket', 'A sturdy handwoven basket made from natural palm leaves, great for storage.', 190.00, 'Home Decor', 'basket.jpg', 35, FALSE),
('Beaded Statement Necklace', 'Handmade beaded necklace featuring locally sourced stones and beads.', 175.00, 'Jewelry', 'necklace.jpg', 20, FALSE),
('Hand-carved Wooden Bowl', 'A rustic bowl carved from a single piece of solid wood, food-safe finish.', 260.00, 'Kitchen', 'wooden-bowl.jpg', 18, FALSE),
('Linen Table Runner', 'Handwoven pure linen table runner with a natural, textured finish.', 210.00, 'Textiles', 'table-runner.jpg', 22, FALSE),
('Handmade Scented Candle', 'Soy wax candle hand-poured with natural essential oils, long burn time.', 95.00, 'Bath & Body', 'candle.jpg', 75, FALSE),
('Woven Straw Sun Hat', 'A breathable, hand-woven straw hat made for everyday summer wear.', 240.00, 'Accessories', 'sun-hat.jpg', 28, FALSE),
('Hand-stitched Fabric Tote Bag', 'Durable canvas tote bag with hand-stitched detailing, ideal for daily use.', 165.00, 'Accessories', 'tote-bag.jpg', 45, FALSE);
