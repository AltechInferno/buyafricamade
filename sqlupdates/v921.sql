ALTER TABLE `business_settings` CHANGE `type` `type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) 
VALUES 
(NULL, 'product_external_link_for_seller', 1, NULL, current_timestamp(), current_timestamp());

UPDATE `business_settings` SET `value` = '9.2.1' WHERE `business_settings`.`type` = 'current_version';

COMMIT;
