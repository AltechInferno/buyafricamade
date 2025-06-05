ALTER TABLE `carts` ADD `status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `id`;

CREATE TABLE `payment_methods` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `addon_identifier` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `payment_methods` (`id`, `name`, `active`, `addon_identifier`, `created_at`, `updated_at`) VALUES
(1, 'paypal', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(2, 'stripe', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(3, 'sslcommerz', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(4, 'instamojo', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(5, 'razorpay', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(6, 'paystack', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(7, 'voguepay', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(8, 'payhere', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(9, 'ngenius', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(10, 'iyzico', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(11, 'nagad', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(12, 'bkash', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(13, 'aamarpay', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(14, 'authorizenet', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(15, 'payku', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(16, 'mercadopago', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(17, 'paymob', 0, NULL, '2024-05-19 05:31:18', '2024-05-19 05:31:18'),
(18, 'paytm', 0, 'paytm', '2024-05-19 05:35:13', '2024-05-19 05:35:13'),
(19, 'toyyibpay', 0, 'paytm', '2024-05-19 05:35:13', '2024-05-19 05:35:13'),
(20, 'myfatoorah', 0, 'paytm', '2024-05-19 05:35:13', '2024-05-19 05:35:13'),
(21, 'khalti', 0, 'paytm', '2024-05-19 05:35:13', '2024-05-19 05:35:13'),
(22, 'phonepe', 0, 'paytm', '2024-05-19 05:35:13', '2024-05-19 05:35:13'),
(23, 'flutterwave', 0, 'african_pg', '2024-05-19 05:36:10', '2024-05-19 05:36:10'),
(24, 'payfast', 0, 'african_pg', '2024-05-19 05:36:10', '2024-05-19 05:36:10');

ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `payment_methods`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

DELETE FROM `business_settings` WHERE type IN
    ('paypal_payment','stripe_payment','mercadopago_payment','sslcommerz_payment','instamojo_payment','razorpay','paystack','voguepay','payhere','ngenius','iyzico','nagad','bkash','aamarpay','authorizenet','payku','paymob_payment','flutterwave','payfast','paytm_payment','toyyibpay_payment','myfatoorah','khalti_payment','phonepe_payment');


UPDATE `business_settings` SET `value` = '8.9' WHERE `business_settings`.`type` = 'current_version';

COMMIT;
