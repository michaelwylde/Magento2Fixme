<?php
namespace Swimwear\Fixme\Model\Config\Source;

class Categories extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Api\CategoryListInterface
     */
    private $categoryList;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory
     */
    private $attrOptionFactory;

    public function __construct(
        \Magento\Catalog\Api\CategoryListInterface $categoryList,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
    ) {
        $this->categoryList = $categoryList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attrOptionFactory = $attrOptionFactory;
    }

    public function getSubCategoriesForId($categoryId=null)
    {
        if ($categoryId) {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('parent_id', $categoryId)->create();
        } else {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        $categories = $this->categoryList->getList($searchCriteria);
        return $categories->getItems();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            ['value' => '0', 'label' => __('None')]
        ];

        /** @var \Magento\Catalog\Api\Data\CategoryInterface $category */
        foreach ($this->getSubCategoriesForId() as $category) {
            $options[] = [
                'value' => $category->getEntityId(),
                'label' => addslashes(__($category->getName()))
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];

        foreach ($this->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }

    /**
     * Taken from \Magento\Eav\Model\Entity\Attribute\Source\Table
     *
     * Retrieve Column(s) for Flat - removed multiselect
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $columns = [];
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $type = \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER;

        $columns[$attributeCode] = [
            'type' => $type,
            'length' => null,
            'unsigned' => false,
            'nullable' => true,
            'default' => null,
            'extra' => null,
            'comment' => $attributeCode . ' column',
        ];

        $columns[$attributeCode . '_value'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'unsigned' => false,
            'nullable' => true,
            'default' => null,
            'extra' => null,
            'comment' => $attributeCode . ' column',
        ];

        return $columns;
    }

    /**
     * Taken from \Magento\Eav\Model\Entity\Attribute\Source\Table
     *
     * Retrieve Indexes for Flat - removed multiselect
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = [];

        $index = sprintf('IDX_%s', strtoupper($this->getAttribute()->getAttributeCode()));
        $indexes[$index] = ['type' => 'index', 'fields' => [$this->getAttribute()->getAttributeCode()]];

        $index = sprintf('IDX_%s_VALUE', strtoupper($this->getAttribute()->getAttributeCode()));
        $indexes[$index] = [
            'type' => 'index',
            'fields' => [$this->getAttribute()->getAttributeCode() . '_value'],
        ];

        return $indexes;
    }


    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        // This must return a select to our custom table (in this case the category table), but currently it is ignored.
        // Ignored in \Magento\Catalog\Model\Indexer\Product\Flat\TableBuilder::_fillTemporaryTable
        // Simply joins on eav_attribute_option_value which is always going to be wrong.

        // Currently this will return
        /*
        SELECT IF(t2.value_id > 0, t2.value, t1.value) AS `extra_category`, IF(to2.value_id > 0, to2.value, to1.value) AS `extra_category_value` FROM `catalog_product_entity_int` AS `t1`
        LEFT JOIN `catalog_product_entity_int` AS `t2` ON t1.entity_id = t2.entity_id AND t2.entity_type_id = 4 AND t2.attribute_id = 177 AND t2.store_id = 2
        LEFT JOIN `eav_attribute_option_value` AS `to1` ON to1.option_id = IF(t2.value_id > 0, t2.value, t1.value) AND to1.store_id = 0
        LEFT JOIN `eav_attribute_option_value` AS `to2` ON to2.option_id = IF(t2.value_id > 0, t2.value, t1.value) AND to2.store_id = '2';
        ERROR 1054 (42S22): Unknown column 't1.entity_id' in 'on clause'
        */
        return parent::getFlatUpdateSelect($store);
    }
}
