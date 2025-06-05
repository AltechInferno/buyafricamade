ALTER TABLE `business_settings` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`)
    VALUES (NULL, 'cupon_text_color', 'dark', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'flash_deal_section_outline', 0, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'flash_deal_section_outline_color', '#000000', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'featured_section_bg_color', '#ffffff', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'featured_section_outline', 1, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'featured_section_outline_color', '#dfdfe6', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'best_selling_section_bg_color', '#ffffff', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'best_selling_section_outline', 1, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'best_selling_section_outline_color', '#dfdfe6', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'new_products_section_bg_color', '#f5f5f5', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'new_products_section_outline', 0, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'new_products_section_outline_color', '#000000', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'home_categories_section_bg_color', '#f2f4f5', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'home_categories_content_bg_color', '#ffffff', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'home_categories_content_outline', 0, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'home_categories_content_outline_color', '#000000', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'classified_section_bg_color', '#fff9ed', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'classified_section_outline', 0, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'classified_section_outline_color', '#000000', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'sellers_section_bg_color', '#fff9ed', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'sellers_section_outline', 0, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'sellers_section_outline_color', '#000000', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'brands_section_bg_color', '#f0f2f5', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'brands_section_outline', 0, NULL, current_timestamp(), current_timestamp()),
        (NULL, 'brands_section_outline_color', '#000000', NULL, current_timestamp(), current_timestamp()),
        (NULL, 'uploaded_image_format', 'default', NULL, current_timestamp(), current_timestamp());

ALTER TABLE `customer_package_payments` CHANGE `payment_details` `payment_details` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

INSERT INTO `payment_methods` (`id`, `name`, `active`, `addon_identifier`, `created_at`, `updated_at`)
    VALUES (NULL, 'tap', 0, NULL, current_timestamp(), current_timestamp());

UPDATE `business_settings` SET `value` = '9.1' WHERE `business_settings`.`type` = 'current_version';

COMMIT;
