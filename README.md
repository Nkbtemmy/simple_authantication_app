# User Management System

A web-based user management system built with PHP, MySQL, and Docker. This application provides a secure interface for managing users with features like user authentication and admin controls.

## Features

- Secure User Authentication (Login/Logout)
- Admin Dashboard
- User Management Operations:
  - View user list
  - Add new users
  - Edit user details
  - Delete users
- Password Management
- Responsive Design

## Tech Stack

- PHP 8.1
- MySQL 8.0
- Apache Web Server
- Docker & Docker Compose
- phpMyAdmin

## Prerequisites

- Docker
- Docker Compose

## Project Structure

- `config/db_config.php`: Database configuration
- `dashboard.php`: Admin dashboard
- `login.php`: User login
- `logout.php`: User logout
- `register.php`: User registration
- `style.css`: CSS styles

## Installation

1. Clone the repository:

docker-compose up -d --build

## Default Login Credentials

Admin Account:
- Username: admin@example.com
- Password: admin123!

## Test User Account:
- Username: user@example.com
- Password: User123!

## Database Setup

1. Create a MySQL database:

CREATE DATABASE user_management;  

2. Import the database schema:

mysql -u root -p user_management < database.sql

3. Update the database configuration in `config/db_config.php`: 

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root_password';
$db_name = 'user_management';

4. Start the Docker containers:

docker-compose up -d --build

5. Access the application at:

http://localhost:8080

## Accessing phpMyAdmin

1. Access phpMyAdmin at:

http://localhost:8081

2. Login with the following credentials:

Username: root
Password: root_password

## Accessing the Admin Dashboard

1. Access the Admin Dashboard at:

http://localhost:8080/dashboard.php

2. Login with the following credentials:

Username: admin@example.com
Password: admin123!

## Accessing the User Management Interface

1. Access the User Management Interface at:

http://localhost:8080/user_management.php

## Generating a Hashed Password

1. Generate a hashed password using the following command:

php generate_password.php your_password

2. The output will display the original password and the hashed version.
