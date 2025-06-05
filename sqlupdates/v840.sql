ALTER TABLE `products` ADD `frequently_brought_selection_type` VARCHAR(19) NULL DEFAULT 'product' AFTER `wholesale_product`;

CREATE TABLE `frequently_brought_products` (
  `product_id` int(11) NOT NULL,
  `frequently_brought_product_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) 
  VALUES (NULL, 'guest_checkout_activation', '0', NULL, current_timestamp(), current_timestamp());

ALTER TABLE `orders` ADD `notified` TINYINT(1) NOT NULL DEFAULT '0' AFTER `commission_calculated`;

UPDATE `business_settings` SET `value` = '8.4' WHERE `business_settings`.`type` = 'current_version';

COMMIT;