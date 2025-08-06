<?php
require __DIR__ . '/../db.php';

$stmt = $pdo->query('SELECT id, name, price, image_url, short_description FROM products WHERE is_available = 1');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($products);
