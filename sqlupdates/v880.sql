ALTER TABLE `products` CHANGE `discount` `discount` DOUBLE(20,2) NOT NULL DEFAULT '0', CHANGE `discount_type` `discount_type` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'amount';

ALTER TABLE `customer_package_payments`
ADD `amount` DOUBLE(20,2) NOT NULL AFTER `payment_method`,
CHANGE `approval` `approval` INT(1) NOT NULL DEFAULT '1',
CHANGE `offline_payment` `offline_payment` INT(1) NOT NULL DEFAULT '2' COMMENT '1=offline payment\r\n2=online paymnet';

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`)
  VALUES (NULL, 'earning_report', 'report', 'web', current_timestamp(), current_timestamp());

UPDATE `business_settings` SET `value` = '8.8' WHERE `business_settings`.`type` = 'current_version';

COMMIT;