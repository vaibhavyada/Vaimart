# VaiMart E-Commerce Website

A full-featured e-commerce website built with PHP, MySQL, and Bootstrap.

## Features

- User Authentication (Register/Login)
- Product Catalog with Categories
- Shopping Cart Functionality
- Checkout Process
- Order Management
- Contact Form
- Responsive Design

## Tech Stack

- Frontend: HTML, CSS, Bootstrap 5
- Backend: PHP (Vanilla PHP)
- Database: MySQL
- Theme Colors: Orange (#FFA500) and White (#FFFFFF)

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- phpMyAdmin (for database management)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/vaimart.git
cd vaimart
```

2. Import the database:
- Open phpMyAdmin
- Create a new database named `vaimart_db`
- Import the `database.sql` file

3. Configure the database connection:
- Open `config/database.php`
- Update the database credentials if needed:
```php
$host = 'localhost';
$dbname = 'vaimart_db';
$username = 'root';
$password = '';
```

4. Set up the web server:
- Configure your web server to point to the project directory
- Ensure the document root is set to the project folder

5. Create the `images` directory and add product images:
```bash
mkdir images
```

## Project Structure

```
vaimart/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
├── images/
├── about.php
├── cart.php
├── checkout.php
├── contact.php
├── index.php
├── login.php
├── logout.php
├── order_success.php
├── product.php
├── products.php
├── register.php
└── database.sql
```

## Developer Information

- **Developer:** Vaibhav Yadav
- **Student ID:** 22BBS0030
- **Institution:** VIT Bhopal University

## Security Features

- Password hashing using `password_hash()`
- Prepared statements for SQL queries
- Input validation and sanitization
- Session management
- CSRF protection

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 