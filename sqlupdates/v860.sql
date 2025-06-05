CREATE TABLE `dynamic_popups` (
  `id` int(20) UNSIGNED NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `title` varchar(191) NOT NULL,
  `summary` text NOT NULL,
  `banner` varchar(191) DEFAULT NULL,
  `btn_link` varchar(191) NOT NULL,
  `btn_text` varchar(191) DEFAULT NULL,
  `btn_text_color` varchar(191) DEFAULT NULL,
  `btn_background_color` varchar(191) DEFAULT NULL,
  `show_subscribe_form` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dynamic_popups` (`id`, `status`, `title`, `summary`, `banner`, `btn_link`, `btn_text`, `btn_text_color`, `btn_background_color`, `show_subscribe_form`, `created_at`, `updated_at`) VALUES
(1, 1, 'Subscribe to Our Newsletter', 'Subscribe our newsletter for coupon, offer and exciting promotional discount..', null, '#', 'Subscribe Now', 'white', '#baa185', 'on', '2024-03-27 22:32:51', '2024-03-28 04:33:24');

ALTER TABLE `dynamic_popups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dynamic_popups`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

CREATE TABLE `last_viewed_products` (
  `id` int(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `last_viewed_products`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `last_viewed_products`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) 
  VALUES (NULL, 'last_viewed_product_activation', '1', NULL, current_timestamp(), current_timestamp());

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`)
  VALUES (NULL, 'view_all_dynamic_popups', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'add_dynamic_popups', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'edit_dynamic_popups', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'delete_dynamic_popups', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'publish_dynamic_popups', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'brand_bulk_upload', 'brand', 'web', current_timestamp(), current_timestamp());

UPDATE `business_settings` SET `value` = '8.6' WHERE `business_settings`.`type` = 'current_version';

COMMIT;