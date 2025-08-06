<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>О компании — Школьный Партнёр</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
  <div class="flex-grow flex flex-col">
<?php include __DIR__ . '/header.php'; ?>


<main class="max-w-3xl mx-auto px-4 py-10">
  <h1 class="text-3xl font-bold mb-6">О компании</h1>
  <div id="aboutContent" class="prose prose-lg max-w-none">
    <!-- Контент из БД будет здесь -->
  </div>
</main>
  </div>


<?php include __DIR__ . '/footer.php'; ?>

<script>
  async function loadAbout() {
    const res = await fetch('/routes/about.php');
    const data = await res.json();
    document.getElementById('aboutContent').innerHTML = data.content;
  }

  loadAbout();
</script>

</body>
</html>
