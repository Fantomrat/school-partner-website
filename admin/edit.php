<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

// Получаем все категории
$allCategories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Получаем выбранные категории для товара
$stmt = $pdo->prepare("SELECT category_id FROM product_categories WHERE product_id = ?");
$stmt->execute([$id]);
$productCategories = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $short = $_POST['short_description'] ?? '';
    $full = $_POST['full_description'] ?? '';
    $available = isset($_POST['is_available']) ? 1 : 0;
    $categories = $_POST['categories'] ?? [];

    // Получаем текущие данные о товаре
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $image_url = $product['image_url'];

    // Загрузка новой картинки
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('product_', true) . '.' . $ext;
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        move_uploaded_file($tmp_name, $upload_dir . $new_filename);
        $image_url = 'uploads/' . $new_filename;
    }

    // Обновляем товар
    if ($name && $price > 0) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, short_description = ?, full_description = ?, is_available = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$name, $price, $short, $full, $available, $image_url, $id]);

        // Обновляем связи категорий
        $pdo->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$id]);
        $stmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
        foreach ($categories as $cat_id) {
            $stmt->execute([$id, $cat_id]);
        }

        header('Location: index.php');
        exit;
    }
}

// Получаем товар для формы
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Редактировать товар</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">

<?php include __DIR__ . '/nav.php'; ?>

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-bold mb-4">Редактировать товар #<?= $product['id'] ?></h1>

  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <input type="text" name="name" placeholder="Название товара" value="<?= htmlspecialchars($product['name']) ?>" class="w-full border rounded p-2" required>
    <input type="number" step="0.01" name="price" placeholder="Цена" value="<?= $product['price'] ?>" class="w-full border rounded p-2" required>
    <textarea name="short_description" placeholder="Краткое описание" class="w-full border rounded p-2"><?= htmlspecialchars($product['short_description']) ?></textarea>
    <textarea name="full_description" placeholder="Полное описание" class="w-full border rounded p-2"><?= htmlspecialchars($product['full_description']) ?></textarea>

    <div>
      <p class="mb-1 text-gray-700">Категории:</p>
      <select name="categories[]" multiple class="w-full border rounded p-2 h-40">
        <?php foreach ($allCategories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $productCategories) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php if ($product['image_url']): ?>
      <div>
        <p class="mb-1 text-gray-700">Текущее изображение:</p>
        <img src="../<?= htmlspecialchars($product['image_url']) ?>" alt="Текущее изображение" class="w-48 h-auto rounded shadow mb-4">
      </div>
    <?php endif; ?>

    <label class="block">
      <span class="text-gray-700">Новое изображение (если нужно заменить):</span>
      <input type="file" name="image" accept="image/*" class="mt-1 block w-full border border-gray-300 rounded p-2">
    </label>

    <label class="inline-flex items-center space-x-2 cursor-pointer">
      <input type="checkbox" name="is_available" <?= $product['is_available'] ? 'checked' : '' ?>>
      <span>В наличии</span>
    </label>

    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Сохранить</button>
  </form>

  <a href="index.php" class="block mt-4 text-blue-600 hover:underline">← Назад</a>
</div>

</body>
</html>
