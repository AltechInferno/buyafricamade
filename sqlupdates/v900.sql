ALTER TABLE `notifications` ADD `notification_type_id` INT NOT NULL AFTER `id`;

-- Notification Types
CREATE TABLE `notification_types` (
  `id` int(11) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'customer',
  `type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `default_text` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notification_types` (`id`, `user_type`, `type`, `name`, `image`, `default_text`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'order_placed_admin', 'Order Placed', NULL, 'Order: [[order_code]] has been Placed', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(2, 'seller', 'order_placed_seller', 'Order Placed', NULL, 'Order: [[order_code]] has been Placed', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(3, 'customer', 'order_placed_customer', 'Order Placed', NULL, 'Your Order: [[order_code]] has been Placed', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(4, 'admin', 'order_confirmed_admin', 'Order Confirmed', NULL, 'Order: [[order_code]] has been Confirmed', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(5, 'seller', 'order_confirmed_seller', 'Order Confirmed', NULL, 'Order: [[order_code]] has been Confirmed', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(6, 'customer', 'order_confirmed_customer', 'Order Confirmed', NULL, 'Your Order: [[order_code]] has been Confirmed', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(7, 'admin', 'order_picked_up_admin', 'Order Picked Up', NULL, 'Order: [[order_code]] has been picked up', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(8, 'seller', 'order_picked_up_seller', 'Order Picked Up', NULL, 'Order: [[order_code]] has been picked up', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(9, 'customer', 'order_picked_up_customer', 'Order Picked Up', NULL, 'Your Order: [[order_code]] has been picked up', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(10, 'admin', 'order_on_the_way_admin', 'Order On the Way', NULL, 'Order: [[order_code]] is on the way', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(11, 'seller', 'order_on_the_way_seller', 'Order On the Way\r\n', NULL, 'Order: [[order_code]] is on the way', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(12, 'customer', 'order_on_the_way_customer', 'Order On the Way\r\n', NULL, 'Your Order: [[order_code]] is on the way', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(13, 'admin', 'order_delivered_admin', 'Order Delivered', NULL, 'Order: [[order_code]] has been delivered', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(14, 'seller', 'order_delivered_seller', 'Order Delivered', NULL, 'Order: [[order_code]] has been delivered', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(15, 'customer', 'order_delivered_customer', 'Order Delivered', NULL, 'Your Order: [[order_code]] has been delivered', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(16, 'admin', 'order_cancelled_admin', 'Order Cancelled', NULL, 'Order: [[order_code]] has been cancelled', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(17, 'seller', 'order_cancelled_seller', 'Order Cancelled', NULL, 'Order: [[order_code]] has been cancelled', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(18, 'customer', 'order_cancelled_customer', 'Order Cancelled', NULL, 'Your Order: [[order_code]] has been cancelled', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(19, 'admin', 'order_paid_admin', 'Successful Payment', NULL, 'Payment for order: [[order_code]] is successful', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(20, 'seller', 'order_paid_seller', 'Successful Payment', NULL, 'Payment for order: [[order_code]] is successful', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(21, 'customer', 'order_paid_customer', 'Successful Payment', NULL, 'Your payment for order: [[order_code]] is successful', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(22, 'admin', 'seller_product_upload', 'Seller Product Upload', NULL, 'Product : [[product_name]] is pending', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(23, 'seller', 'seller_product_approved', 'Product Approved', NULL, 'Product : [[product_name]] is approved', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(24, 'admin', 'seller_payout_request', 'Seller Payout Request', NULL, '[[shop_name]] request for payment : [[amount]]', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(25, 'seller', 'seller_payout', 'Payout', NULL, '[[shop_name]] : payment [[amount]] is paid', 1, '2024-05-14 21:35:09', '2024-05-14 21:35:09'),
(26, 'admin', 'shop_verify_request_submitted', 'Shop Verification Request Submitted', NULL, '[[shop_name]] submitted the verification request.', 1, '2024-05-20 18:22:20', '2024-05-20 18:22:20'),
(27, 'seller', 'shop_verify_request_approved', 'Shop Verification Request Approved', NULL, 'Your shop verification request has been Approved.', 1, '2024-05-20 18:22:20', '2024-05-20 18:22:20'),
(28, 'seller', 'shop_verify_request_rejected', 'Shop Verification Request Rejected', NULL, 'Your shop verification request has been Rejected.', 1, '2024-05-20 18:22:20', '2024-05-20 18:22:20');

ALTER TABLE `notification_types`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `notification_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;


-- Notification Type Translations
CREATE TABLE `notification_type_translations` (
  `id` bigint(20) NOT NULL,
  `notification_type_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `default_text` varchar(255) NOT NULL,
  `lang` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `notification_type_translations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notification_type_translations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) 
VALUES 
(NULL, 'notification_show_type', 'only_text', NULL, current_timestamp(), current_timestamp());


INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`)
VALUES
(NULL, 'notification_settings', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'view_all_notification_types', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'add_notification_types', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'edit_notification_types', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'delete_notification_types', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'notification_types_status_update', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'send_custom_notification', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'view_custom_notification_history', 'notification', 'web', current_timestamp(), current_timestamp()),
(NULL, 'delete_custom_notification_history', 'notification', 'web', current_timestamp(), current_timestamp());
        

UPDATE `business_settings` SET `value` = '9.0' WHERE `business_settings`.`type` = 'current_version';

COMMIT;
