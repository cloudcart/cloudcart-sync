<?php
/**
 * @category Mage
 * @package  CloudCart_Sync
 * @author   Nikola Haralamov <n.haralamov@cloudcart.com>
 */

class CloudCart_Sync_Model_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * @param  int $configurableProductId
     * @return array
     */
    public function variations($configurableProductId)
    {
        $configurableProduct = Mage::getModel('catalog/product')->load($configurableProductId);
        if ($configurableProduct->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            throw new \Exception('Product type is not configurable.');
        }
        $ids = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($configurableProductId);

        $configurableAttributes = array();
        foreach ($configurableProduct->getTypeInstance(true)->getConfigurableAttributes($configurableProduct) as $attribute) {
            $configurableAttributes[] = $attribute->getProductAttribute()->getAttributeCode();
        }

        $data = array();
        foreach ($ids[0] as $id) {
            $product = Mage::getModel('catalog/product')->load($id);
            $attributes = $product->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getIsVisibleOnFront()) {
                    if (!in_array($attribute->getAttributeCode(), $configurableAttributes)) {
                        continue;
                    }
                    $value = $attribute->getFrontend()->getValue($product);

                    if (!$product->hasData($attribute->getAttributeCode())) {
                        $value = '';
                    } elseif ((string)$value == '') {
                        $value = '';
                    }

                    if (is_string($value) && strlen($value)) {
                        $data[$product->getId()]['price'] = $product->getPrice();
                        $data[$product->getId()]['sku'] = $product->getSku();
                        $data[$product->getId()]['configurable_product_id'] = $configurableProductId;
                        $data[$product->getId()]['simple_product_id'] = $product->getId();
                        $data[$product->getId()]['configurable_attributes'][$attribute->getAttributeCode()] = array(
                            'label' => $attribute->getStoreLabel(),
                            'value' => $value,
                            'code'  => $attribute->getAttributeCode(),
                        );
                    }
                }
            }
        }
        return $data;
    }
}