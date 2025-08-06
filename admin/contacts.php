<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $content = $_POST['content'];
  $stmt = $pdo->query('SELECT COUNT(*) FROM contacts_info');
  $exists = $stmt->fetchColumn();

  if ($exists) {
    $stmt = $pdo->prepare('UPDATE contacts_info SET content = ?');
  } else {
    $stmt = $pdo->prepare('INSERT INTO contacts_info (content) VALUES (?)');
  }

  $stmt->execute([$content]);
  header('Location: contacts.php?success=1');
  exit;
}

$stmt = $pdo->query('SELECT content FROM contacts_info LIMIT 1');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$content = $row ? $row['content'] : '';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Контакты - Админка</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 p-8">

<?php include __DIR__ . '/nav.php'; ?>

<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">

  <main class="max-w-4xl mx-auto py-10 px-4 flex-grow">
    <h1 class="text-2xl font-bold mb-4">Редактировать Контакты</h1>
    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">Сохранено!</div>
    <?php endif; ?>
    <form method="post">
      <textarea name="content" rows="12" class="w-full p-3 border border-gray-300 rounded mb-4"><?= htmlspecialchars($content) ?></textarea>
      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Сохранить</button>
    </form>
  </main>

</body>
</html>