INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) VALUES 
(NULL, 'use_floating_buttons', 1, NULL, current_timestamp(), current_timestamp()),
(NULL, 'seller_commission_type', 'fixed_rate', NULL, current_timestamp(), current_timestamp());

ALTER TABLE `shops` ADD `commission_percentage` DOUBLE(8,2) NOT NULL DEFAULT '0.00' AFTER `shipping_cost`;

INSERT INTO `notification_types` (`id`, `user_type`, `type`, `name`, `image`, `default_text`, `status`, `created_at`, `updated_at`) VALUES 
(NULL, 'customer', 'complete_unpaid_order_payment', 'Complete Unpaid Order Payment', NULL, 'Your order: [[order_code]] is still not paid for. Kindly complete your payment.', '1', current_timestamp(), current_timestamp());

INSERT INTO `pages` (`id`, `type`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `keywords`, `meta_image`, `created_at`, `updated_at`)
    VALUES (NULL, 'contact_us_page', 'Contact us', 'contact-us', '{"description":null,"address":null,"phone":null,"email":null}', NULL, NULL, NULL, NULL, current_timestamp(), current_timestamp());

-- Contact Us
CREATE TABLE `contacts` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `contacts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`) 
VALUES
(NULL, 'view_all_contacts', 'support', 'web', current_timestamp(), current_timestamp()),
(NULL, 'reply_to_contact', 'support', 'web', current_timestamp(), current_timestamp()),
(NULL, 'view_all_offline_payment_orders', 'offline_payment', 'web', current_timestamp(), current_timestamp()),
(NULL, 'add_customer', 'customer', 'web', current_timestamp(), current_timestamp()),
(NULL, 'add_seller', 'seller', 'web', current_timestamp(), current_timestamp()),
(NULL, 'view_all_unpaid_orders', 'sale', 'web', current_timestamp(), current_timestamp()),
(NULL, 'unpaid_order_payment_notification_send', 'sale', 'web', current_timestamp(), current_timestamp());

DELETE FROM `permissions` WHERE `name` = 'sms_providers_configurations';

UPDATE `business_settings` SET `value` = '9.2' WHERE `business_settings`.`type` = 'current_version';

COMMIT;
