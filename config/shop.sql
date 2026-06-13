SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `shop_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `size` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `added_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `status_id` int UNSIGNED NOT NULL DEFAULT '1',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `customer_name` varchar(100) NOT NULL DEFAULT '',
  `customer_phone` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status_id`, `total`, `created_at`, `updated_at`, `customer_name`, `customer_phone`) VALUES
(1, 2, 2, '51940.00', '2026-06-12 14:16:17', '2026-06-12 14:49:57', 'Мария Парыгина', '+799999999');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, '3490.00'),
(2, 1, 2, 5, '3490.00'),
(3, 1, 3, 5, '6200.00');

-- --------------------------------------------------------

--
-- Структура таблицы `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `name`) VALUES
(1, 'accepted'),
(2, 'processing'),
(3, 'assembled'),
(5, 'received'),
(6, 'cancelled');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(210) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `size` varchar(50) NOT NULL DEFAULT 'Универсальный',
  `is_top` tinyint(1) NOT NULL DEFAULT '0',
  `is_new` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `size`, `is_top`, `is_new`, `created_at`, `updated_at`) VALUES
(1, 1, 'Платье красное', 'plate-krasnoe', 'Элегантное платье из шифона. Приталенный силуэт, длина миди.', '4990.00', 'S', 1, 0, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(2, 1, 'Платье черное', 'plate-chernoe', 'Летнее платье с цветочным принтом. Свободный крой, длина до колена.', '3490.00', 'M', 0, 1, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(3, 1, 'Платье браун', 'plate-brayn', 'Строгое деловое платье-футляр. Ткань: костюмная шерсть.', '6200.00', 'L', 1, 0, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(4, 2, 'Юбка миди', 'yubka-midi', 'Юбка-карандаш из плотной ткани. Длина midi.', '2990.00', 'S', 0, 1, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(5, 2, 'Юбка мини', 'yubka-mini', 'Пышная юбка-солнце из лёгкой ткани.', '2490.00', 'M', 1, 0, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(6, 3, 'Блузка белая', 'bluzka-belaya', 'Блузка из искусственного шёлка. V-образный вырез.', '2200.00', 'S', 0, 1, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(7, 3, 'Блузка бежевая', 'bluzka-bezhevaya', 'Блузка из искусственного шёлка. V-образный вырез.', '2200.00', 'S', 0, 1, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(8, 3, 'Блузка черная', 'bluzka-chernaya', 'Классическая белая блузка. Прямой крой, длинный рукав.', '1890.00', 'L', 1, 0, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(9, 4, 'Брюки черные', 'bryuki-chernye', 'Прямые брюки из костюмной ткани. Высокая посадка.', '3800.00', 'M', 1, 0, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(10, 4, 'Брюки браун', 'bryuki-braun', 'Прямые брюки из костюмной ткани. Высокая посадка.', '3800.00', 'M', 1, 0, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(11, 4, 'Брюки серые', 'bryuki-serye', 'Укороченные брюки-кюлоты. Свободный силуэт.', '3100.00', 'S', 0, 1, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(12, 5, 'Колье Жемчуг', 'kole-zhemchug', 'Изящное колье с искусственным жемчугом. Длина цепочки 45 см.', '1200.00', 'Универсальный', 1, 0, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(13, 6, 'Сумка браун', 'sumka-brayn', 'Компактная сумка через плечо. Регулируемый ремень.', '2800.00', 'Средний', 0, 1, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(14, 6, 'Клатч черная', 'klatch-chernaya', 'Маленький клатч для вечерних выходов. Металлическая фурнитура.', '1900.00', 'Маленький', 1, 1, '2026-06-12 07:33:28', '2026-06-12 07:33:28'),
(15, 11, 'Футболка белая', 'futbolka-belaya', 'Белая футболка', '500.00', 'XS', 1, 1, '2026-06-12 15:11:16', '2026-06-12 15:11:16');

-- --------------------------------------------------------

--
-- Структура таблицы `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(110) NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `sort_order`, `created_at`) VALUES
(1, 'Платья', 'platya', 1, '2026-06-12 07:33:28'),
(2, 'Юбки', 'yubki', 2, '2026-06-12 07:33:28'),
(3, 'Блузки', 'bluzki', 4, '2026-06-12 07:33:28'),
(4, 'Брюки', 'bryuki', 5, '2026-06-12 07:33:28'),
(5, 'Украшения', 'ukrasheniya', 7, '2026-06-12 07:33:28'),
(6, 'Сумки', 'sumki', 8, '2026-06-12 07:33:28'),
(9, 'Джинсы', 'dzhinsy', 6, '2026-06-12 15:01:53'),
(11, 'Футболки', 'futbolki', 3, '2026-06-12 15:04:28');

-- --------------------------------------------------------

--
-- Структура таблицы `product_images`
--

CREATE TABLE `product_images` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `filename`, `is_main`, `sort_order`, `created_at`) VALUES
(29, 1, 'dress_red.png', 1, 1, '2026-06-12 07:50:45'),
(30, 2, 'dress_black.png', 1, 1, '2026-06-12 07:50:45'),
(31, 3, 'dress_brown.png', 1, 1, '2026-06-12 07:50:45'),
(32, 4, 'skirt_midi.png', 1, 1, '2026-06-12 07:50:45'),
(33, 5, 'skirt_mini.png', 1, 1, '2026-06-12 07:50:45'),
(34, 6, 'blouse_white.png', 1, 1, '2026-06-12 07:50:45'),
(35, 7, 'blouse_beige.png', 1, 1, '2026-06-12 07:50:45'),
(36, 8, 'blouse_black.png', 1, 1, '2026-06-12 07:50:45'),
(37, 9, 'trousers_black.png', 1, 1, '2026-06-12 07:50:45'),
(38, 10, 'trousers_brown.png', 1, 1, '2026-06-12 07:50:45'),
(39, 11, 'trousers_grey.png', 1, 1, '2026-06-12 07:50:45'),
(40, 12, 'accessory.png', 1, 1, '2026-06-12 07:50:45'),
(41, 13, 'bag_brown.png', 1, 1, '2026-06-12 07:50:45'),
(42, 14, 'bag_black.png', 1, 1, '2026-06-12 07:50:45'),
(43, 15, 'img_6a2bf7648cec01.97472793.webp', 1, 0, '2026-06-12 15:11:16');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Мария', 'ppp@yandex.ru', NULL, '$2y$12$oLvcrycHahZWvWJSKHO3hOvW5dJjZpagnqg/sB6jx9YmHU4WI75Ny', 'user', '2026-06-12 07:51:40'),
(2, 'admin', 'admin@shop.local', NULL, '$2a$12$Md5rqiRfuG1I5.07tcOXYeINCoQ6NhFO/ySuOlAkHS6P9hKJ5rnOy', 'admin', '2026-06-12 08:09:29');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_item` (`user_id`,`product_id`,`size`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Индексы таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `order_statuses` (`id`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;