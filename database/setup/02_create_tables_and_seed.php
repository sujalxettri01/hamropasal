<?php
require __DIR__ . '/../connection.php';

$queries = [];

$queries['users'] = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB";

$queries['products'] = "CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    category VARCHAR(80) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT 'https://via.placeholder.com/300x220?text=Product',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB";

$queries['orders'] = "CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    payment_method ENUM('Cash on Delivery', 'eSewa', 'Khalti') NOT NULL DEFAULT 'Cash on Delivery',
    order_status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Pending',
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB";

$queries['order_items'] = "CREATE TABLE IF NOT EXISTS order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB";

echo "<h2>Setting up HamroPasal tables...</h2>";

foreach ($queries as $table => $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Table '$table' is ready.<br>";
    } else {
        echo "Error creating '$table': " . mysqli_error($conn) . "<br>";
    }
}

$adminEmail = 'admin@hamropasal.com';
$adminCheck = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$adminEmail'");

if ($adminCheck && mysqli_num_rows($adminCheck) === 0) {
    $hash = password_hash('Admin@123', PASSWORD_DEFAULT);
    $insertAdmin = "INSERT INTO users (name, email, phone, password, address, is_admin)
                    VALUES ('Store Admin', '$adminEmail', '9800000000', '$hash', 'Kathmandu', 1)";
    mysqli_query($conn, $insertAdmin);
    echo "Default admin created: admin@hamropasal.com / Admin@123<br>";
}

echo "<h3>Setup complete.</h3>";

mysqli_close($conn);
?>
