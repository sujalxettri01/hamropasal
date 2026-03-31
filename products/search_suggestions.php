<?php
// Endpoint used by the live search suggestion dropdown.
// Returns up to 8 matching active products as JSON.

require __DIR__ . '/../database/connection.php';

$term = trim($_GET['term'] ?? '');
if ($term === '') {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$like = "%{$term}%";
$query = "SELECT product_id, name, category, price FROM products WHERE is_active=1 AND (name LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT 8";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$suggestions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $suggestions[] = [
        'id' => (int) $row['product_id'],
        'name' => $row['name'],
        'category' => $row['category'],
        'price' => (float) $row['price'],
    ];
}

header('Content-Type: application/json');
echo json_encode($suggestions);
