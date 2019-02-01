<?php
/**
 * @category Mage
 * @package  CloudCart_Sync
 * @author   Nikola Haralamov <n.haralamov@cloudcart.com>
 */

$this->startSetup();

$api_key = 'cloudcart';
$api_user = 'cloudcart';
$role_name = 'cloudcart';
$email = 'sales@cloudcart.com';
$first_name = 'CloudCart';
$last_name = 'Sync';

$role = Mage::getModel('api/roles')
    ->setName($role_name)
    ->setPid(false)
    ->setRoleType(Mage_Api_Model_Acl::ROLE_TYPE_GROUP)
    ->save();
Mage::getModel('api/rules')->setRoleId($role->getId())->setResources(array('all'))->saveRel();
$user = Mage::getModel('api/user');
$user->setData(
    array(
        'username' => $api_user,
        'firstname' => $first_name,
        'lastname' => $last_name,
        'email' => $email,
        'is_active' => 1,
        'user_roles' => '',
        'assigned_user_role' => '',
        'role_name' => '',
        'roles' => array($role->getId()),
    )
);
$user->setApiKey($api_key);
$user->save()->load($user->getId());
$user->setRoleIds(array($role->getId()))->setRoleUserId($user->getUserId())->saveRelations();

$this->endSetup();