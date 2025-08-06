<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Каталог — Школьный Партнёр</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">

    <?php include __DIR__ . '/header.php'; ?>


  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Каталог товаров</h1>
    <div id="products" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Товары вставляются здесь через JS -->
    </div>
  </main>

  <!-- Quote Modal -->
  <div id="quoteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded w-full max-w-md relative">
      <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-400 hover:text-black">&times;</button>
      <h2 class="text-xl font-bold mb-4">Запросить КП</h2>
      <form id="quoteForm" class="space-y-3">
        <input type="hidden" name="product_id" id="product_id">
        <input type="text" name="name" placeholder="Ваше имя" class="w-full border rounded p-2" required>
        <input type="tel" name="phone" placeholder="Телефон" class="w-full border rounded p-2" required>
        <button class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 w-full">Отправить</button>
        <p id="quoteMessage" class="text-sm mt-2"></p>
      </form>
    </div>
  </div>

    <?php include __DIR__ . '/footer.php'; ?>


  <script>
    async function loadProducts() {
      const res = await fetch('/routes/catalog.php');
      const products = await res.json();
      const container = document.getElementById('products');

      products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'bg-white shadow rounded overflow-hidden';

        card.innerHTML = `
          <img src="${product.image_url || 'https://via.placeholder.com/300x200'}" alt="${product.name}" class="w-full h-48 object-cover">
          <div class="p-4">
            <h3 class="font-semibold text-lg">${product.name}</h3>
            <p class="text-sm text-gray-500 mb-2">${product.short_description}</p>
            <p class="text-blue-700 font-bold text-lg">${Number(product.price).toLocaleString()} ₽</p>
            <button onclick="openModal(${product.id})" class="mt-3 w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800">Запросить КП</button>
          </div>
        `;

        container.appendChild(card);
      });
    }

    function openModal(productId) {
      document.getElementById('product_id').value = productId;
      document.getElementById('quoteModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('quoteModal').classList.add('hidden');
      document.getElementById('quoteMessage').textContent = '';
    }

    document.getElementById('quoteForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData.entries());

      try {
        
      
      const res = await fetch('/routes/request_quote.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
      });

      const result = await res.json();
      const msg = document.getElementById('quoteMessage');
      if (result.success) {
        msg.textContent = 'Заявка отправлена!';
        msg.className = 'text-green-600';
        e.target.reset();
      } else {
        msg.textContent = result.error || 'Ошибка отправки';
        msg.className = 'text-red-600';
      }
      } catch (error) {
                console.error('Ошибка fetch:', error);

      }

    });

    loadProducts();
  </script>

</body>
</html>
