-- BookNest eCommerce Database Setup
-- Run this script to create the complete database structure

-- Create database
CREATE DATABASE IF NOT EXISTS booknest;
USE booknest;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT(11) NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default-book.jpg',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (user_id, product_id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    payment_method ENUM('cash_on_delivery', 'esewa') NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample admin user
INSERT IGNORE INTO users (full_name, email, phone, address, password, role) VALUES 
('Admin User', 'admin@booknest.com', '9876543210', 'Admin Office, Kathmandu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample products
INSERT IGNORE INTO products (name, category, price, stock, image, description) VALUES 
('The Great Gatsby', 'Fiction', 15.99, 50, 'gatsby.jpg', 'A classic American novel set in the Jazz Age, exploring themes of wealth, love, and the American Dream.'),
('To Kill a Mockingbird', 'Fiction', 18.99, 30, 'mockingbird.jpg', 'A gripping tale of racial injustice and childhood innocence in the American South.'),
('1984', 'Science Fiction', 16.99, 40, '1984.jpg', 'A dystopian social science fiction novel and cautionary tale about the dangers of totalitarianism.'),
('Pride and Prejudice', 'Romance', 14.99, 35, 'pride.jpg', 'A romantic novel of manners that critiques the British landed gentry at the end of the 18th century.'),
('The Catcher in the Rye', 'Fiction', 17.99, 25, 'catcher.jpg', 'A story about teenage rebellion and angst, narrated by the iconic Holden Caulfield.'),
('Harry Potter and the Sorcerer\'s Stone', 'Fantasy', 19.99, 60, 'harry.jpg', 'The first book in the Harry Potter series, introducing the magical world of Hogwarts.'),
('The Da Vinci Code', 'Mystery', 20.99, 45, 'davinci.jpg', 'A mystery thriller novel that follows symbologist Robert Langdon as he investigates a murder in Paris.'),
('Sapiens', 'Education', 22.99, 55, 'sapiens.jpg', 'A brief history of humankind, from the Stone Age to the 21st century.'),
('The Alchemist', 'Philosophy', 13.99, 70, 'alchemist.jpg', 'A philosophical book that follows a young shepherd on his journey to find a worldly treasure.'),
('Atomic Habits', 'Self Help', 21.99, 65, 'atomic.jpg', 'An easy and proven way to build good habits and break bad ones.');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_cart_user_id ON cart(user_id);
CREATE INDEX idx_order_items_order_id ON order_items(order_id);

-- Display success message
SELECT 'BookNest database created successfully!' AS message;
SELECT 'Admin credentials: admin@booknest.com / admin123' AS admin_info;
SELECT 'Sample products inserted: 10 books' AS products_info;
