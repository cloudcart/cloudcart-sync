<?php
/**
 * @category Mage
 * @package  CloudCart_Sync
 * @author   Nikola Haralamov <n.haralamov@cloudcart.com>
 */

/**
 * Configuration
 *
 * @var string $api_key = cloudcart
 */
$api_key = '4252e0e94b380477e6b8560854622ed3:CbTygUMjOL5G7qvMh1c19mZr7zmTk7Hf';
$api_user = 'cloudcart';
$email = 'support@cloudcart.com';
$first_name = 'Cloud';
$last_name = 'Cart';

$table_api_role = $this->getTable('api/role');
$table_api_rule = $this->getTable('api/rule');
$table_api_user = $this->getTable('api/user');

/**
 * SOAP/XML-RPC
 *  1) Create API_USER with API_KEY
 *  2) Create API_ROLE with ALL_RESOURCE_ACCESS
 *  3) Assign API_ROLE to API_USER
 *
 * @var Mage_Core_Model_Resource_Setup $this
 * @see Mage/Core/Model/Resource/Setup
 */
$this->startSetup();
$this->run("

SET FOREIGN_KEY_CHECKS = 0;
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- TRUNCATE TABLE `{$table_api_role}`;
INSERT INTO `{$table_api_role}` (`parent_id`, `tree_level`, `sort_order`, `role_type`, `user_id`, `role_name`)
VALUES
(0, 1, 0, 'G', 0, '{$api_user}');

SELECT LAST_INSERT_ID() INTO @role_id;

-- TRUNCATE TABLE `{$table_api_rule}`;
INSERT INTO `{$table_api_rule}` (`role_id`, `resource_id`, `api_privileges`, `assert_id`, `role_type`, `api_permission`)
VALUES
(@role_id, 'core', NULL, 0, 'G', 'deny'),
(@role_id, 'core/store', NULL, 0, 'G', 'deny'),
(@role_id, 'core/store/info', NULL, 0, 'G', 'deny'),
(@role_id, 'core/store/list', NULL, 0, 'G', 'deny'),
(@role_id, 'core/magento', NULL, 0, 'G', 'deny'),
(@role_id, 'core/magento/info', NULL, 0, 'G', 'deny'),
(@role_id, 'directory', NULL, 0, 'G', 'deny'),
(@role_id, 'directory/country', NULL, 0, 'G', 'deny'),
(@role_id, 'directory/region', NULL, 0, 'G', 'deny'),
(@role_id, 'customer', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/create', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/update', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/delete', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/info', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/address', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/address/create', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/address/update', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/address/delete', NULL, 0, 'G', 'deny'),
(@role_id, 'customer/address/info', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/create', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/move', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/delete', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/tree', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/info', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/attributes', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/product', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/product/assign', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/product/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/category/product/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/create', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/delete', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/update_tier_price', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/info', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/listOfAdditionalAttributes', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attributes', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/read', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/write', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/types', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/create', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/info', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/option', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/option/add', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/option/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/list', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/create', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/attribute_add', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/attribute_remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/group_add', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/group_rename', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/attribute/set/group_remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/link', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/link/assign', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/link/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/link/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/media', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/media/create', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/media/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/media/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/add', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/types', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/info', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/list', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/value', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/value/list', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/value/info', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/value/add', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/value/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/option/value/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/tag', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/tag/list', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/tag/info', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/tag/add', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/tag/update', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/tag/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/downloadable_link', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/downloadable_link/add', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/downloadable_link/list', NULL, 0, 'G', 'deny'),
(@role_id, 'catalog/product/downloadable_link/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'sales', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/change', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/info', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/shipment', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/shipment/create', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/shipment/comment', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/shipment/track', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/shipment/info', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/shipment/send', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/invoice', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/invoice/create', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/invoice/comment', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/invoice/capture', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/invoice/void', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/invoice/cancel', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/invoice/info', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/creditmemo', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/creditmemo/create', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/creditmemo/comment', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/creditmemo/cancel', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/creditmemo/info', NULL, 0, 'G', 'deny'),
(@role_id, 'sales/order/creditmemo/list', NULL, 0, 'G', 'deny'),
(@role_id, 'cataloginventory', NULL, 0, 'G', 'deny'),
(@role_id, 'cataloginventory/update', NULL, 0, 'G', 'deny'),
(@role_id, 'cataloginventory/info', NULL, 0, 'G', 'deny'),
(@role_id, 'cart', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/create', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/order', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/info', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/totals', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/license', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/product', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/product/add', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/product/update', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/product/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/product/list', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/product/moveToCustomerQuote', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/customer', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/customer/set', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/customer/addresses', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/shipping', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/shipping/method', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/shipping/list', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/payment', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/payment/method', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/payment/list', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/coupon', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/coupon/add', NULL, 0, 'G', 'deny'),
(@role_id, 'cart/coupon/remove', NULL, 0, 'G', 'deny'),
(@role_id, 'giftmessage', NULL, 0, 'G', 'deny'),
(@role_id, 'giftmessage/set', NULL, 0, 'G', 'deny'),
(@role_id, 'all', NULL, 0, 'G', 'allow');

-- TRUNCATE TABLE `{$table_api_user}`;
INSERT INTO `{$table_api_user}` (`firstname`, `lastname`, `email`, `username`, `api_key`, `created`, `modified`, `lognum`, `reload_acl_flag`, `is_active`)
VALUES
('{$first_name}', '{$last_name}', '{$email}', '{$api_user}', '{$api_key}', NOW(), NOW(), 65535, 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

");

$this->endSetup();