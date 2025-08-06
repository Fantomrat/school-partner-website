<?php
$page = $_GET['page'] ?? 'catalog';

switch ($page) {
    case 'catalog':
        require __DIR__ . '/views/catalog.php';
        break;
    case 'product':
        require __DIR__ . '/routes/product.php';
        break;
    case 'contacts':
        require __DIR__ . '/views/contacts.php';
        break;
    case 'about':
        require __DIR__ . '/views/about.php';
        break;
    case 'request-quote':
        require __DIR__ . '/routes/request_quote.php';
        break;
    default:
        http_response_code(404);
        echo 'Страница не найдена';
}
