<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

// Обработка удаления
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    header('Location: index.php');
    exit;
}

// Обработка изменения доступности
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    $id = (int)$_POST['toggle_id'];
    $isAvailable = isset($_POST['is_available']) ? 1 : 0;
    $stmt = $pdo->prepare('UPDATE products SET is_available = ? WHERE id = ?');
    $stmt->execute([$isAvailable, $id]);
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка - Товары</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<?php include __DIR__ . '/nav.php'; ?>

<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Управление товарами</h1>
        <a href="add.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Добавить товар</a>
    </div>

    <table class="min-w-full border">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="px-4 py-2 border">ID</th>
                <th class="px-4 py-2 border">Название</th>
                <th class="px-4 py-2 border">Цена</th>
                <th class="px-4 py-2 border">Доступность</th>
                <th class="px-4 py-2 border">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-2 border"><?= $product['id'] ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($product['name']) ?></td>
                    <td class="px-4 py-2 border"><?= number_format($product['price'], 2) ?> ₽</td>
                    <td class="px-4 py-2 border">
                        <form method="POST" class="inline">
                            <input type="hidden" name="toggle_id" value="<?= $product['id'] ?>">
                            <label class="inline-flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="is_available" onchange="this.form.submit()" <?= $product['is_available'] ? 'checked' : '' ?>>
                                <span><?= $product['is_available'] ? 'В наличии' : 'Нет в наличии' ?></span>
                            </label>
                        </form>
                    </td>
                    <td class="px-4 py-2 border space-x-2">
                        <a href="edit.php?id=<?= $product['id'] ?>" class="text-blue-600 hover:underline">Редактировать</a>
                        <a href="?delete=<?= $product['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Удалить товар?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

</body>
</html>
