CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) DEFAULT NULL,
  `product_format` ENUM('simple','variation') DEFAULT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `sku` VARCHAR(255) DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT NULL,
  `stock` INT(11) DEFAULT NULL,
  `variations_colors` TEXT DEFAULT NULL,
  `variations_sizes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `products_images` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `is_default` TINYINT(4) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `products_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `products_variations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `color` VARCHAR(255) DEFAULT NULL,
  `size` VARCHAR(255) DEFAULT NULL,
  `sku` VARCHAR(255) DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `products_variations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;