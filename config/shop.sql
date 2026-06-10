CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_categories (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(110) NOT NULL UNIQUE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name        VARCHAR(200) NOT NULL,
    slug        VARCHAR(210) NOT NULL UNIQUE,
    description TEXT,
    price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    size        VARCHAR(50)  NOT NULL DEFAULT 'Универсальный',
    is_top      TINYINT(1)   NOT NULL DEFAULT 0,
    is_new      TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_images (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    filename   VARCHAR(255) NOT NULL,
    is_main    TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cart (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity   INT UNSIGNED NOT NULL DEFAULT 1,
    added_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cart_item (user_id, product_id),
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_statuses (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO order_statuses (name) VALUES
('pending'),('processing'),('shipped'),('delivered'),('cancelled');

CREATE TABLE IF NOT EXISTS orders (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    status_id  INT UNSIGNED NOT NULL DEFAULT 1,
    total      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)          ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES order_statuses(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id   INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity   INT UNSIGNED NOT NULL,
    price      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO product_categories (name, slug, sort_order) VALUES
('Платья',    'platya',      1),
('Юбки',      'yubki',       2),
('Блузки',    'bluzki',      3),
('Брюки',     'bryuki',      4),
('Украшения', 'ukrasheniya', 5),
('Сумки',     'sumki',       6);

-- Платья (category_id = 1)
INSERT INTO products (category_id, name, slug, description, price, size, is_top, is_new) VALUES
(1, 'Платье красное',    'plate-krasnoe',
 'Элегантное платье из шифона. Приталенный силуэт, длина миди.',
 4990.00, 'S', 1, 0),
(1, 'Платье черное',     'plate-chernoe',
 'Летнее платье с цветочным принтом. Свободный крой, длина до колена.',
 3490.00, 'M', 0, 1),
(1, 'Платье браун', 'plate-brayn',
 'Строгое деловое платье-футляр. Ткань: костюмная шерсть.',
 6200.00, 'L', 1, 0);

-- Юбки (category_id = 2)
INSERT INTO products (category_id, name, slug, description, price, size, is_top, is_new) VALUES
(2, 'Юбка миди',   'yubka-midi',
 'Юбка-карандаш из плотной ткани. Длина midi.',
 2990.00, 'S', 0, 1),
(2, 'Юбка мини', 'yubka-mini',
 'Пышная юбка-солнце из лёгкой ткани.',
 2490.00, 'M', 1, 0);

-- Блузки (category_id = 3)
INSERT INTO products (category_id, name, slug, description, price, size, is_top, is_new) VALUES
(3, 'Блузка белая',  'bluzka-belaya',
 'Блузка из искусственного шёлка. V-образный вырез.',
 2200.00, 'S', 0, 1),
 (3, 'Блузка бежевая',  'bluzka-bezhevaya',
 'Блузка из искусственного шёлка. V-образный вырез.',
 2200.00, 'S', 0, 1),
(3, 'Блузка черная',  'bluzka-chernaya',
 'Классическая белая блузка. Прямой крой, длинный рукав.',
 1890.00, 'L', 1, 0);

-- Брюки (category_id = 4)
INSERT INTO products (category_id, name, slug, description, price, size, is_top, is_new) VALUES
(4, 'Брюки черные', 'bryuki-chernye',
 'Прямые брюки из костюмной ткани. Высокая посадка.',
 3800.00, 'M', 1, 0),
 (4, 'Брюки браун', 'bryuki-braun',
 'Прямые брюки из костюмной ткани. Высокая посадка.',
 3800.00, 'M', 1, 0),
(4, 'Брюки серые',  'bryuki-serye',
 'Укороченные брюки-кюлоты. Свободный силуэт.',
 3100.00, 'S', 0, 1);

-- Украшения (category_id = 5)
INSERT INTO products (category_id, name, slug, description, price, size, is_top, is_new) VALUES
(5, 'Колье Жемчуг',   'kole-zhemchug',
 'Изящное колье с искусственным жемчугом. Длина цепочки 45 см.',
 1200.00, 'Универсальный', 1, 0);


-- Сумки (category_id = 6)
INSERT INTO products (category_id, name, slug, description, price, size, is_top, is_new) VALUES
(6, 'Сумка браун','sumka-brayn',
 'Компактная сумка через плечо. Регулируемый ремень.',
 2800.00, 'Средний',   0, 1),
(6, 'Клатч черная',  'klatch-chernaya',
 'Маленький клатч для вечерних выходов. Металлическая фурнитура.',
 1900.00, 'Маленький', 1, 1);

INSERT INTO product_images (product_id, filename, is_main, sort_order) VALUES
(45, 'dress_red.png', 1, 1),
(46, 'dress_black.png', 1, 1),
(47, 'dress_brown.png', 1, 1),
(48, 'skirt_midi.png', 1, 1),
(49, 'skirt_mini.png', 1, 1),
(50, 'blouse_white.png', 1, 1),
(51, 'blouse_beige.png', 1, 1),
(52, 'blouse_black.png', 1, 1),
(53, 'trousers_black.png', 1, 1),
(54, 'trousers_brown.png', 1, 1),
(55, 'trousers_grey.png', 1, 1),
(56, 'accessory.png', 1, 1),
(57, 'bag_brown.png', 1, 1),
(58, 'bag_black.png', 1, 1);