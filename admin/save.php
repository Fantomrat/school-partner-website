<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? 0;
$short = $_POST['short_description'] ?? '';
$full = $_POST['full_description'] ?? '';
$available = isset($_POST['is_available']) ? (int)$_POST['is_available'] : 1;

$imagePath = null;

// Обработка загрузки изображения
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/';
    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $filename;

    // Проверка, что это изображение
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = 'uploads/' . $filename;
        }
    }
}

// Вставка в базу
if ($name && $price > 0) {
    $stmt = $pdo->prepare("INSERT INTO products (name, price, short_description, full_description, image_url, is_available)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $name,
        $price,
        $short,
        $full,
        $imagePath ?? '',
        $available
    ]);
}

header("Location: index.php");
exit;
