CREATE TABLE `custom_alerts` (
  `id` int(20) UNSIGNED NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `type` varchar(191) NOT NULL,
  `banner` varchar(191) DEFAULT NULL,
  `link` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `text_color` varchar(191) DEFAULT NULL,
  `background_color` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `custom_alerts` (`id`, `status`, `type`, `banner`, `link`, `description`, `text_color`, `background_color`, `created_at`, `updated_at`) VALUES
(1, 1, 'small', null, '#', '<p>We use cookie for better user experience, check our policy <a href=\"https://demo.activeitzone.com/ecommerce/privacypolicy\">here</a>&nbsp;</p>', 'dark', '#ffffff', '2024-03-27 02:02:20', '2024-03-27 23:21:29');

ALTER TABLE `custom_alerts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `custom_alerts`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`)
  VALUES (NULL, 'view_all_custom_alerts', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'add_custom_alerts', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'edit_custom_alerts', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'delete_custom_alerts', 'marketing', 'web', current_timestamp(), current_timestamp()),
          (NULL, 'publish_custom_alerts', 'marketing', 'web', current_timestamp(), current_timestamp());

INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) 
  VALUES (NULL, 'custom_alert_location', 'bottom-left', NULL, current_timestamp(), current_timestamp());

ALTER TABLE `frequently_brought_products` RENAME `frequently_bought_products`;
ALTER TABLE `frequently_bought_products` CHANGE `frequently_brought_product_id` `frequently_bought_product_id` int(11) DEFAULT NULL;
ALTER TABLE `products` CHANGE `frequently_brought_selection_type` `frequently_bought_selection_type` varchar(19) DEFAULT 'product';

UPDATE `business_settings` SET `value` = '8.7' WHERE `business_settings`.`type` = 'current_version';

COMMIT;