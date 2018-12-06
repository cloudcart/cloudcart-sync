<?php
/**
 * @package Cloudcart_ConfigurableSimpleProductsRelation
 * @author  Nikola Haralamov <sales@cloudcart.com>
 */

class Cloudcart_ConfigurableSimpleProductsRelation_Model_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * @return array
     */
    public function getConfigurableSimpleProductAssociationIds()
    {
        return $this->getAssociatedOfConfigurableProductIds();
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