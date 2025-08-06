<?php
session_start();
require_once '../db.php';

if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Вход в админку</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <form method="POST" class="bg-white p-8 rounded shadow max-w-sm w-full">
    <h1 class="text-2xl mb-4">Вход в админку</h1>
    <?php if (!empty($error)): ?>
      <p class="text-red-600 mb-4"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>
    <input type="text" name="username" placeholder="Логин" required
           class="w-full p-2 border rounded mb-4" value="<?=htmlspecialchars($username ?? '')?>">
    <input type="password" name="password" placeholder="Пароль" required
           class="w-full p-2 border rounded mb-4">
    <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Войти</button>
  </form>
</body>
</html>
