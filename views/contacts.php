<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Контакты — Школьный Партнёр</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

<?php include __DIR__ . '/header.php'; ?>

<main class="flex-grow max-w-3xl mx-auto px-4 py-10">
  <h1 class="text-3xl font-bold mb-6">Контакты</h1>
  <div id="contactsContent" class="prose prose-lg max-w-none">
    <!-- Контент из БД будет здесь -->
  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
  async function loadContacts() {
    const res = await fetch('/routes/contacts.php');
    const data = await res.json();
    document.getElementById('contactsContent').innerHTML = data.content;
  }

  loadContacts();
</script>

</body>
</html>
