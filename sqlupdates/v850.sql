INSERT INTO `business_settings` (`id`, `type`, `value`, `lang`, `created_at`, `updated_at`) 
  VALUES (NULL, 'slider_section_full_width', '0', 'en', current_timestamp(), current_timestamp()),
        (NULL, 'slider_section_bg_color', '#dedede', 'en', current_timestamp(), current_timestamp()),
        (NULL, 'home_banner4_images', NULL, 'en', current_timestamp(), current_timestamp()),
        (NULL, 'home_banner4_links', NULL, 'en', current_timestamp(), current_timestamp()),
        (NULL, 'home_banner5_images', NULL, 'en', current_timestamp(), current_timestamp()),
        (NULL, 'home_banner5_links', NULL, 'en', current_timestamp(), current_timestamp()),
        (NULL, 'home_banner6_images', NULL, 'en', current_timestamp(), current_timestamp()),
        (NULL, 'home_banner6_links', NULL, 'en', current_timestamp(), current_timestamp());

INSERT INTO `permissions` (`id`, `name`, `section`, `guard_name`, `created_at`, `updated_at`)
  VALUES (NULL, 'set_category_wise_discount', 'product_category', 'web', current_timestamp(), current_timestamp());

UPDATE `business_settings` SET `value` = '8.5' WHERE `business_settings`.`type` = 'current_version';

COMMIT;