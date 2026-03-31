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

$queries['messages'] = "CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    message_type VARCHAR(50) NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
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
$adminPassword = 'Admin@123';
$adminCheck = mysqli_query($conn, "SELECT user_id, password FROM users WHERE email='$adminEmail' LIMIT 1");

if ($adminCheck && mysqli_num_rows($adminCheck) === 0) {
    $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
    $insertAdmin = "INSERT INTO users (name, email, phone, password, address, is_admin)
                    VALUES ('Store Admin', '$adminEmail', '9800000000', '$hash', 'Kathmandu', 1)";
    mysqli_query($conn, $insertAdmin);
    echo "Default admin created: admin@hamropasal.com / Admin@123<br>";
} elseif ($adminCheck) {
    $existingAdmin = mysqli_fetch_assoc($adminCheck);
    if (!password_verify($adminPassword, $existingAdmin['password'])) {
        $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
        $adminId = (int) $existingAdmin['user_id'];
        mysqli_query($conn, "UPDATE users SET password='$hash', is_admin=1 WHERE user_id=$adminId");
        echo "Default admin password reset: admin@hamropasal.com / Admin@123<br>";
    }
}

// Seed sample products
$productsCheck = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$productCount = mysqli_fetch_assoc($productsCheck)['count'];

if ($productCount < 12) {
    $sampleProducts = [
        ['name' => 'Basmati Rice 5kg', 'category' => 'Groceries', 'description' => 'Premium long-grain basmati rice, perfect for daily meals.', 'price' => 450.00, 'stock' => 100, 'image' => 'https://via.placeholder.com/300x220?text=Basmati+Rice'],
        ['name' => 'Nepali Tea 500g', 'category' => 'Beverages', 'description' => 'Traditional Nepali tea leaves, rich in flavor.', 'price' => 120.00, 'stock' => 50, 'image' => 'https://via.placeholder.com/300x220?text=Nepali+Tea'],
        ['name' => 'Dish Soap 1L', 'category' => 'Household', 'description' => 'Effective dishwashing liquid for clean dishes.', 'price' => 85.00, 'stock' => 75, 'image' => 'https://via.placeholder.com/300x220?text=Dish+Soap'],
        ['name' => 'Toothpaste 200g', 'category' => 'Personal Care', 'description' => 'Fluoride toothpaste for healthy teeth and gums.', 'price' => 65.00, 'stock' => 60, 'image' => 'https://via.placeholder.com/300x220?text=Toothpaste'],
        ['name' => 'Cooking Oil 1L', 'category' => 'Groceries', 'description' => 'Refined cooking oil for all your cooking needs.', 'price' => 180.00, 'stock' => 40, 'image' => 'https://via.placeholder.com/300x220?text=Cooking+Oil'],
        ['name' => 'Milk Powder 400g', 'category' => 'Dairy', 'description' => 'Full cream milk powder, great for making milk or baking.', 'price' => 220.00, 'stock' => 30, 'image' => 'https://via.placeholder.com/300x220?text=Milk+Powder'],
        ['name' => 'Laundry Detergent 1kg', 'category' => 'Household', 'description' => 'Powerful laundry detergent for clean clothes.', 'price' => 150.00, 'stock' => 45, 'image' => 'https://via.placeholder.com/300x220?text=Laundry+Detergent'],
        ['name' => 'Sugar 2kg', 'category' => 'Groceries', 'description' => 'White refined sugar for sweetening your food.', 'price' => 110.00, 'stock' => 80, 'image' => 'https://via.placeholder.com/300x220?text=Sugar'],
        ['name' => 'Shampoo 250ml', 'category' => 'Personal Care', 'description' => 'Gentle shampoo for healthy, shiny hair.', 'price' => 95.00, 'stock' => 55, 'image' => 'https://via.placeholder.com/300x220?text=Shampoo'],
        ['name' => 'Coffee 200g', 'category' => 'Beverages', 'description' => 'Ground coffee beans for a perfect brew.', 'price' => 250.00, 'stock' => 25, 'image' => 'https://via.placeholder.com/300x220?text=Coffee'],
        ['name' => 'Bread Loaf', 'category' => 'Bakery', 'description' => 'Fresh white bread loaf, soft and delicious.', 'price' => 45.00, 'stock' => 90, 'image' => 'https://via.placeholder.com/300x220?text=Bread'],
        ['name' => 'Eggs 12pcs', 'category' => 'Dairy', 'description' => 'Farm fresh eggs, perfect for breakfast.', 'price' => 130.00, 'stock' => 70, 'image' => 'https://via.placeholder.com/300x220?text=Eggs'],
    ];

    $stmt = mysqli_prepare($conn, "INSERT INTO products (name, category, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($sampleProducts as $product) {
        mysqli_stmt_bind_param($stmt, 'sssdss', $product['name'], $product['category'], $product['description'], $product['price'], $product['stock'], $product['image']);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
    echo "Sample products added to the database.<br>";
}

echo "<h3>Setup complete.</h3>";

mysqli_close($conn);
?>
