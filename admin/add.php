<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Добавить товар</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Новый товар</h1>
    <form action="save.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="text" name="name" placeholder="Название товара" class="w-full border rounded p-2" required>
      <input type="number" step="0.01" name="price" placeholder="Цена" class="w-full border rounded p-2" required>
      <textarea name="short_description" placeholder="Краткое описание" class="w-full border rounded p-2"></textarea>
      <textarea name="full_description" placeholder="Полное описание" class="w-full border rounded p-2"></textarea>
      <input type="file" name="image" accept="image/*" class="w-full border rounded p-2">

      <select name="is_available" class="w-full border rounded p-2">
        <option value="1">В наличии</option>
        <option value="0">Нет в наличии</option>
      </select>
      
      <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Сохранить</button>
    </form>
    <a href="index.php" class="block mt-4 text-blue-600 hover:underline">← Назад</a>
  </div>

</body>
</html>
