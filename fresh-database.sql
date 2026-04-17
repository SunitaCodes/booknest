-- BookNest Database for XAMPP
-- MySQL 5.7+ Compatible

-- Create database
CREATE DATABASE IF NOT EXISTS `booknest` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `booknest`;

-- Drop existing tables if they exist (fresh start)
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;

-- Create users table
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `full_name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(15) NOT NULL,
    `address` text NOT NULL,
    `password` varchar(255) NOT NULL,
    `role` enum('user','admin') NOT NULL DEFAULT 'user',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create products table
CREATE TABLE `products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `category` varchar(50) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    `stock` int(11) NOT NULL DEFAULT 0,
    `image` varchar(255) DEFAULT 'default-book.jpg',
    `description` text,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cart table
CREATE TABLE `cart` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `product_id` (`product_id`),
    CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create orders table
CREATE TABLE `orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(15) NOT NULL,
    `address` text NOT NULL,
    `total_price` decimal(10,2) NOT NULL,
    `payment_method` varchar(50) DEFAULT 'cash_on_delivery',
    `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create order_items table
CREATE TABLE `order_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `order_id` (`order_id`),
    KEY `product_id` (`product_id`),
    CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert admin user (password: admin123)
INSERT INTO `users` (`full_name`, `email`, `phone`, `address`, `password`, `role`) VALUES
('Admin User', 'admin@booknest.com', '9876543210', 'Kathmandu, Nepal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample users
INSERT INTO `users` (`full_name`, `email`, `phone`, `address`, `password`, `role`) VALUES
('John Doe', 'john@example.com', '9845123456', 'Patan, Nepal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Jane Smith', 'jane@example.com', '9866789012', 'Bhaktapur, Nepal', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample products
INSERT INTO `products` (`name`, `category`, `price`, `stock`, `image`, `description`) VALUES
('The Great Gatsby', 'Fiction', 15.99, 50, 'gatsby.jpg', 'A classic American novel set in the Jazz Age.'),
('1984', 'Science Fiction', 12.99, 30, '1984.jpg', 'A dystopian social science fiction novel by George Orwell.'),
('To Kill a Mockingbird', 'Fiction', 14.99, 40, 'mockingbird.jpg', 'A novel about racial injustice in the American South.'),
('Pride and Prejudice', 'Romance', 13.99, 35, 'pride.jpg', 'A romantic novel of manners written by Jane Austen.'),
('The Catcher in the Rye', 'Fiction', 16.99, 25, 'catcher.jpg', 'A story about teenage rebellion and angst.'),
('Harry Potter and the Sorcerer\'s Stone', 'Fantasy', 18.99, 60, 'harry.jpg', 'The first book in the magical Harry Potter series.'),
('The Da Vinci Code', 'Mystery', 15.99, 45, 'davinci.jpg', 'A mystery thriller novel by Dan Brown.'),
('Rich Dad Poor Dad', 'Self Help', 17.99, 55, 'richdad.jpg', 'A personal finance book by Robert Kiyosaki.'),
('The Alchemist', 'Fiction', 11.99, 70, 'alchemist.jpg', 'A philosophical book by Paulo Coelho.'),
('Think and Grow Rich', 'Self Help', 14.99, 40, 'think.jpg', 'A self-help book by Napoleon Hill.'),
('The Hobbit', 'Fantasy', 19.99, 35, 'hobbit.jpg', 'A fantasy novel by J.R.R. Tolkien.'),
('Sapiens', 'Education', 20.99, 30, 'sapiens.jpg', 'A brief history of humankind by Yuval Noah Harari.'),
('The Psychology of Money', 'Self Help', 16.99, 45, 'money.jpg', 'Timeless lessons on wealth by Morgan Housel.'),
('Atomic Habits', 'Self Help', 18.99, 50, 'habits.jpg', 'Tiny changes, remarkable results by James Clear.'),
('The Lean Startup', 'Business', 15.99, 25, 'lean.jpg', 'How today\'s entrepreneurs use continuous innovation.'),
('Zero to One', 'Business', 17.99, 20, 'zero.jpg', 'Notes on startups by Peter Thiel.'),
('The 4-Hour Workweek', 'Self Help', 16.99, 35, '4hour.jpg', 'Escape 9-5, live anywhere by Tim Ferriss.'),
('Thinking, Fast and Slow', 'Education', 19.99, 30, 'thinking.jpg', 'A tour of the mind by Daniel Kahneman.'),
('The Power of Habit', 'Self Help', 15.99, 40, 'power.jpg', 'Why we do what we do by Charles Duhigg.'),
('Educated', 'Biography', 14.99, 25, 'educated.jpg', 'A memoir by Tara Westover.');

-- Insert sample orders
INSERT INTO `orders` (`user_id`, `name`, `email`, `phone`, `address`, `total_price`, `payment_method`, `status`) VALUES
(2, 'John Doe', 'john@example.com', '9845123456', 'Patan, Nepal', 31.98, 'cash_on_delivery', 'delivered'),
(3, 'Jane Smith', 'jane@example.com', '9866789012', 'Bhaktapur, Nepal', 45.97, 'esewa', 'processing');

-- Insert sample order items
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 15.99),
(2, 3, 1, 14.99),
(2, 5, 2, 16.99);

-- Success message
SELECT 'BookNest Database Created Successfully!' as message;
SELECT 'Admin Login: admin@booknest.com / admin123' as admin_info;
SELECT 'Sample users: john@example.com / user123, jane@example.com / user123' as sample_users;
