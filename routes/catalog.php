<?php
require __DIR__ . '/../db.php';

$sql = '
  SELECT 
    p.id, p.name, p.price, p.image_url, p.short_description,
    GROUP_CONCAT(c.name SEPARATOR ", ") AS categories
  FROM products p
  LEFT JOIN product_categories pc ON pc.product_id = p.id
  LEFT JOIN categories c ON c.id = pc.category_id
  WHERE p.is_available = 1
  GROUP BY p.id
';

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Разделяем категории в массив и создаём поле category
foreach ($products as &$product) {
    $product['categories'] = $product['categories']
        ? explode(', ', $product['categories'])
        : [];
}


header('Content-Type: application/json');
echo json_encode($products);
