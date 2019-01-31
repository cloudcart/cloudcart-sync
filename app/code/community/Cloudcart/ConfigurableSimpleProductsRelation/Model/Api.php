<?php
/**
 * @category Mage
 * @package  Cloudcart_Import
 * @author   Nikola Haralamov <n.haralamov@cloudcart.com>
 */

class Cloudcart_ConfigurableSimpleProductsRelation_Model_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * @var
     */
    private $_defaultMagentoAttributes = [
        'name',
        'price',
        'special_price',
        'sku',
        'manufacturer',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'weight',
        'status',
        'visibility',
        'news_from_date',
        'news_to_date',
        'special_from_date',
        'special_to_date',
    ];

    /**
     * @var array
     */
    private $_defaultMagentoAdditionalAttributes = array(
        'color',
        'country_of_manufacture',
    );

    /**
     * @var array
     */
    private $_skippedDefaultMagentoAttributes = array(
        'cost',
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'custom_layout_update',
        'gallery',
        'gift_message_available',
        'group_price',
        'image',
        'is_recurring',
        'media_gallery',
        'meta_keyword',
        'msrp',
        'msrp_display_actual_price_type',
        'msrp_enabled',
        'options_container',
        'page_layout',
        'price_view',
        'recurring_profile',
        'small_image',
        'tax_class_id',
        'thumbnail',
        'tier_price',
        'url_key',
    );

    /**
     * @param integer $configurableProductId
     * @return array
     */
    public function getVariantsByConfigurableProductId($configurableProductId)
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
                        $value = ''; // Mage::helper('catalog')->__('N/A');
                    } elseif ((string)$value == '') {
                        $value = ''; // Mage::helper('catalog')->__('No');
                    }

                    // elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    //     $value = Mage::app()->getStore()->convertPrice($value, true);
                    // }

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

    /**
     * @return [type] [description]
     */
    public function properties($id)
    {
        $product = Mage::getModel('catalog/product')->load($id);
        return $product->getData();
    }

    /**
     * @return array
     */
    public function getConfigurableSimpleProductAssociationIds($addConfigurableProductIdAsKey = true)
    {
        return $this->getAssociatedOfConfigurableProductIds($addConfigurableProductIdAsKey);
    }

    /**
     * @return array
     */
    public function getProductsConfigurableAttributeCodes()
    {
        $configurableAttributes = array();
        $configurableProducts = $this->getConfigurableProducts();
        foreach ($configurableProducts as $product) {
            foreach ($product->getTypeInstance(true)->getConfigurableAttributes($product) as $attribute) {
                $configurableAttributes[$product->getId()][] = $attribute->getProductAttribute()->getAttributeCode();
                $configurableAttributes[$product->getId()]['product_id'] = $product->getId();
            }
        }
        return $configurableAttributes;
    }

    /**
     * @param integer $productId
     * @return array
     */
    public function getProductAttributesByProductId($productId)
    {
        $data = array();
        $product = Mage::getModel('catalog/product')->load($productId);
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = ''; // Mage::helper('catalog')->__('N/A');
                } elseif ((string)$value == '') {
                    $value = ''; // Mage::helper('catalog')->__('No');
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode(),
                        'price'  => $product->getPrice(),
                        'sku'  => $product->getSku(),
                    );
                }
            }
        }
        return $data;
    }

    /**
     * @param integer $productId
     * @return array
     */
    public function getSimpleProductsByConfigurableProductId($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            throw new \Exception('Product type is not configurable.');
        }
        return Mage::getModel('catalog/product_type_configurable')->getChildrenIds($productId);
    }

    /**
     * @return array
     */
    public function getUnassociatedSimpleProductIds()
    {
        $simpleProducts = array();
        $collection = $this->getSimpleProducts();
        foreach ($collection as $simpleProduct) {
            $simpleProducts[] = $simpleProduct->getId();
        }
        return $simpleProducts;
    }

    /**
     * @return array
     */
    public function getConfigurableProductIds()
    {
        $configurableProducts = array();
        $collection = $this->getConfigurableProducts();
        foreach ($collection as $configurableProduct) {
            $configurableProducts[] = $configurableProduct->getId();
        }
        return $configurableProducts;
    }

    /**
     * @param  array $filters
     * @return array
     */
    private function getProductCollection($filters = array())
    {
        $select = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addUrlRewrite()
        ;
        if (!empty($filters)) {
            foreach ($filters as $filter => $value) {
                $select->addAttributeToFilter($filter, $value);
            }
        }
        $collection = array();
        foreach ($select as $product) {
            $collection[] = Mage::getModel('catalog/product')->load($product->getId());
        }
        return $collection;
    }

    /**
     * @return array
     */
    private function getConfigurableProducts()
    {
        return $this->getProductCollection(array('type_id' => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE));
    }

    /**
     * @param  boolean $addConfigurableProductIdAsKey
     * @return array
     */
    private function getAssociatedOfConfigurableProductIds($addConfigurableProductIdAsKey = false)
    {
        $associatedOfConfigurableProductids = array();
        $configurableProducts = $this->getConfigurableProducts();
        foreach ($configurableProducts as $configurableProduct) {
            $associatedProducts = $configurableProduct
                ->getTypeInstance(true)
                ->getUsedProducts(null, $configurableProduct);
                if ($addConfigurableProductIdAsKey) {
                    if (!in_array($configurableProduct->getId(), $associatedOfConfigurableProductids)) {
                        $associatedOfConfigurableProductids[$configurableProduct->getId()] = array();
                    }
                }
            foreach ($associatedProducts as $associatedProduct) {
                if (!in_array($associatedProduct->getId(), $associatedOfConfigurableProductids[$configurableProduct->getId()])) {
                    if ($addConfigurableProductIdAsKey) {
                        $associatedOfConfigurableProductids[$configurableProduct->getId()][$associatedProduct->getId()] = $associatedProduct->getId();
                    } else {
                        $associatedOfConfigurableProductids[$associatedProduct->getId()] = $associatedProduct->getId();
                    }
                }
            }
        }
        return $associatedOfConfigurableProductids;
    }

    /**
     * @param  boolean $ignoreAssociatedOfConfigurable
     * @return array
     */
    private function getSimpleProducts($ignoreAssociatedOfConfigurable = true)
    {
        if (!$ignoreAssociatedOfConfigurable) {
            return $this->getProductCollection(array('type_id' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE));
        }
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('type_id', array('eq' => 'simple'))
            ->addAttributeToSelect('e.entity_id');
        $collection->getSelect()->joinLeft(
            array('link_table' => $collection->getTable('catalog/product_super_link')),
            'link_table.product_id = e.entity_id',
            array('product_id')
        );
        $collection->getSelect()->where('link_table.product_id IS NULL');
        return $collection;
    }
}