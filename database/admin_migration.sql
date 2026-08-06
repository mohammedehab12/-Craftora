-- =========================================================
-- Craftora - Admin Panel Migration
-- Run this AFTER the main schema.sql (adds the admins table
-- and one default admin account).
-- =========================================================

USE Craftora;

CREATE TABLE IF NOT EXISTS admins (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL UNIQUE,
    password        VARCHAR(255)        NOT NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin account:
--   email:    admin@craftora.com
--   password: admin123
-- (Change this password after first login in a real deployment.)
INSERT INTO admins (name, email, password) VALUES
('Admin', 'admin@craftora.com', '$2b$10$OyJBGacXeV24hgtim5huTOdOnLtyKrLr3LHni6vCjoIP38Nohvq4S');
