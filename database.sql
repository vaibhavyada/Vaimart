-- Create the database
CREATE DATABASE IF NOT EXISTS vaimart_db;
USE vaimart_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(50),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample products
INSERT INTO products (name, description, price, image_url, category, stock) VALUES
('Smartphone X', 'Latest smartphone with advanced features', 699.99, 'images/smartphone.png', 'Electronics', 50),
('Laptop Pro', 'High-performance laptop for professionals', 1299.99, 'images/laptop.png', 'Electronics', 30),
('Wireless Headphones', 'Premium wireless headphones with noise cancellation', 199.99, 'images/earphones.png', 'Electronics', 100),
('Smart Watch', 'Fitness tracker and smartwatch combo', 149.99, 'images/watch.png', 'Electronics', 75),
('Camera', 'A high resolution camera', 99.99, 'images/camera.png', 'Electronics', 25),
('Gaming Console', 'Next-gen gaming console with controller', 499.99, 'images/console.png', 'Electronics', 25); 