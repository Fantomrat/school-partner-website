<?php
function runCreateTable(PDO $pdo): void {
    $lockFile = __DIR__ . '/create_table.lock';

    if (file_exists($lockFile)) {
        return;
    }

    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `company_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `contacts_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Новая таблица категорий
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Таблица связи товаров и категорий (многие-ко-многим)
CREATE TABLE IF NOT EXISTS `product_categories` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`, `category_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL;

    $pdo->exec($sql);

    // Вставка данных по умолчанию
    $countAdmins = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    if ($countAdmins == 0) {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->execute(['admin', '$2y$10$Byip80g53J3wXcMrV/Mg1uBtABGElFFWVM5mIDnTEgHARfSd3LjHS']);
    }

    $countCompanyInfo = $pdo->query("SELECT COUNT(*) FROM company_info")->fetchColumn();
    if ($countCompanyInfo == 0) {
        $stmt = $pdo->prepare("INSERT INTO company_info (content) VALUES (?)");
        $stmt->execute(['<p>Наша компания "Школьный Партнёр" более 10 лет поставляет качественные товары для образовательных учреждений...</p>']);
    }

    $countContactsInfo = $pdo->query("SELECT COUNT(*) FROM contacts_info")->fetchColumn();
    if ($countContactsInfo == 0) {
        $stmt = $pdo->prepare("INSERT INTO contacts_info (content) VALUES (?)");
        $stmt->execute(['<p><strong>Адрес:</strong> г. Москва, ул. Примерная, д. 123<br><strong>Телефон:</strong> +7 (123) 456-78-90<br><strong>Email:</strong> info@example.com</p>']);
    }

    // Добавляем категории
    $countCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($countCategories == 0) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $categories = ['Мебель', 'Техника', 'Лаборатории', 'Спортинвентарь'];
        foreach ($categories as $c) {
            $stmt->execute([$c]);
        }
    }

    // Добавляем товары
    $countProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($countProducts == 0) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, short_description, full_description, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)");
        $products = [
            ['Стол ученический регулируемый', 4500.00, 'Регулируемый по высоте стол для начальной и средней школы.', 'Прочный металлический каркас, пластиковая столешница. Высота регулируется в трёх положениях.', 'uploads/product_68934ce9ed8f79.76371176.jpg', 1],
            ['Интерактивная доска SmartBoard', 38000.00, 'Электронная доска с сенсорным управлением.', 'Многофункциональная доска для интерактивных занятий. Подключается к ПК, совместима с ПО для образования.', 'uploads/product_68934ce4ba8bb4.63388797.jpg', 1],
            ['Комплект лаборатории физики', 72000.00, 'Оборудование для проведения физических экспериментов.', 'Полный набор оборудования для школы: электричество, оптика, механика, термодинамика.', 'uploads/product_68934cdfe5af94.84059929.jpg', 1],
            ['Стул ученический с регулируемой высотой', 2300.00, 'Металлический каркас, регулируемая высота.', 'Прочный ученический стул с антивандальной конструкцией. Регулировка для трёх возрастных групп.', 'uploads/product_68934cd6109c24.08131522.jpg', 1],
            ['Шкаф для хранения ноутбуков', 16500.00, 'Металлический шкаф на 15 ноутбуков.', 'Безопасное хранение и зарядка ноутбуков. Замок, вентиляция, розетки внутри.', 'uploads/product_68934cc860d230.23277403.webp', 1],
            ['Набор спортивного инвентаря', 15000.00, 'Комплект для уроков физкультуры.', 'Мячи, скакалки, конусы, координационные лестницы и другое оборудование для подвижных игр.', 'uploads/product_68934cbe7e1fc3.91218381.jpg', 1],
        ];
        foreach ($products as $p) {
            $stmt->execute($p);
        }

        // Привязка товаров к категориям
        $stmtLink = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
        $stmtLink->execute([1, 1]); // Стол → Мебель
        $stmtLink->execute([2, 2]); // Доска → Техника
        $stmtLink->execute([3, 3]); // Лаборатория → Лаборатории
        $stmtLink->execute([4, 1]); // Стул → Мебель
        $stmtLink->execute([5, 2]); // Шкаф → Техника
        $stmtLink->execute([6, 4]); // Спортинвентарь → Спортинвентарь
    }

    file_put_contents($lockFile, "table creation completed at " . date('Y-m-d H:i:s'));
}
