<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';

    // Проверка: существует ли уже запись
    $stmt = $pdo->query('SELECT COUNT(*) FROM company_info');
    $exists = $stmt->fetchColumn() > 0;

    if ($exists) {
        $stmt = $pdo->prepare('UPDATE company_info SET content = :content');
    } else {
        $stmt = $pdo->prepare('INSERT INTO company_info (content) VALUES (:content)');
    }

    $stmt->execute(['content' => $content]);
    $message = 'Информация обновлена.';
}

$stmt = $pdo->query('SELECT content FROM company_info LIMIT 1');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$content = $row ? $row['content'] : '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>О компании - Админка</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-8">

<?php include __DIR__ . '/nav.php'; ?>

<main class="max-w-4xl mx-auto px-4 py-8">

  <h1 class="text-2xl font-bold mb-4">О компании</h1>
  <p>Здесь можно редактировать информацию о компании.</p>
<?php if ($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <textarea name="content" rows="15" class="w-full border p-3 rounded"><?= htmlspecialchars($content) ?></textarea>
    <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Сохранить</button>
  </form>
</main>


</body>
</html>
