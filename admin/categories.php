<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: ../login.php');
    exit;
}
require_once '../db.php';

// Удаление категории
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM product_categories WHERE category_id = ?')->execute([$id]);
    header('Location: categories.php');
    exit;
}

// Добавление категории
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if ($name) {
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
    }
    header('Location: categories.php');
    exit;
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка - Категории</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">

<?php include __DIR__ . '/nav.php'; ?>

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4">Управление категориями</h1>

    <form method="POST" class="flex space-x-2 mb-4">
        <input type="text" name="name" placeholder="Новая категория" class="border p-2 flex-grow" required>
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Добавить</button>
    </form>

    <table class="min-w-full border">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="px-4 py-2 border">ID</th>
                <th class="px-4 py-2 border">Название</th>
                <th class="px-4 py-2 border">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-2 border"><?= $cat['id'] ?></td>
                    <td class="px-4 py-2 border"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="px-4 py-2 border">
                        <a href="?delete=<?= $cat['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Удалить категорию?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

</body>
</html>
