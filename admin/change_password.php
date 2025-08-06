<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['admin_username'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new !== $confirm) {
        $message = 'Новый пароль и подтверждение не совпадают';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($current, $admin['password_hash'])) {
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE admins SET password_hash = ? WHERE username = ?');
            $update->execute([$newHash, $username]);
            $message = 'Пароль успешно изменён';
        } else {
            $message = 'Текущий пароль неверен';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Смена пароля</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-6">

<?php include __DIR__ . '/nav.php'; ?>

<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-bold mb-4">Смена пароля</h1>

  <?php if ($message): ?>
    <p class="mb-4 text-center <?= strpos($message, 'успешно') !== false ? 'text-green-600' : 'text-red-600' ?>">
      <?= htmlspecialchars($message) ?>
    </p>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <input type="password" name="current_password" placeholder="Текущий пароль" required
           class="w-full border rounded p-2">
    <input type="password" name="new_password" placeholder="Новый пароль" required
           class="w-full border rounded p-2">
    <input type="password" name="confirm_password" placeholder="Подтвердите новый пароль" required
           class="w-full border rounded p-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Сменить пароль</button>
  </form>
</div>

</body>
</html>
