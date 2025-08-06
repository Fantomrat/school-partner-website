<?php
require __DIR__ . '/../db.php';

$stmt = $pdo->query('SELECT content FROM company_info LIMIT 1');
$row = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['content' => $row ? $row['content'] : 'Информация о компании пока не добавлена.']);
