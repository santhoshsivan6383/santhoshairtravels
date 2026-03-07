-- Create Database
CREATE DATABASE IF NOT EXISTS santhosh_travels;
USE santhosh_travels;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Air Travel Tickets/Bills Table
CREATE TABLE IF NOT EXISTS tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(100),
    client_phone VARCHAR(20),
    departure_city VARCHAR(50) NOT NULL,
    arrival_city VARCHAR(50) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    airline_name VARCHAR(100) NOT NULL,
    flight_number VARCHAR(20) NOT NULL,
    passenger_count INT NOT NULL,
    ticket_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX (user_id),
    INDEX (created_at)
);

-- Sample admin user (password: admin123)
INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@santhosh-travels.com', '$2y$10$slYQmyNdGzC3tqISNk0NLe9YznLQaA7JAqG5VQvqKFFrMWaUHrPzC');

-- Sample data
INSERT INTO tickets (user_id, client_name, client_email, client_phone, departure_city, arrival_city, departure_date, departure_time, arrival_time, airline_name, flight_number, passenger_count, ticket_price, total_price, booking_reference, status, notes) 
VALUES 
(1, 'Sample Client', 'client@example.com', '9876543210', 'Chennai', 'Delhi', '2026-03-15', '10:30:00', '13:45:00', 'Air India', 'AI-101', 1, 5000, 5000, 'SA000001', 'confirmed', 'Sample booking');
