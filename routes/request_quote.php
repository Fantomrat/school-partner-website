<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешён']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['product_id']) || empty($data['name']) || empty($data['phone'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Заполните все поля']);
    exit;
}

require __DIR__ . '/../db.php';

try {
    $stmt = $pdo->prepare("
        INSERT INTO requests (product_id, name, phone, created_at)
        VALUES (:product_id, :name, :phone, NOW())
    ");
    $stmt->execute([
        ':product_id' => $data['product_id'],
        ':name'       => $data['name'],
        ':phone'      => $data['phone']
    ]);

    echo json_encode(['success' => true, 'message' => 'Запрос отправлен!']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
