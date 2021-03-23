<?php

namespace Lof\BrandGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class to resolve custom attribute_set_name field in group GraphQL query
 */
class GroupAttributeSetBrandsResolver implements ResolverInterface
{

    /**
     * @var CollectionFactory
     */
    private $brandCollectionFactory;

    /**
     * GroupAttributeSetBrandsResolver constructor.
     * @param CollectionFactory $brandCollectionFactory
     */
    public function __construct(
        CollectionFactory $brandCollectionFactory
    ) {
        $this->brandCollectionFactory = $brandCollectionFactory;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (isset($value['group_id']) && $value['group_id']) {
            $collection = $this->brandCollectionFactory->create();
            $collection->addFieldToFilter('group_id', $value['group_id']);
            $items = [];
            foreach($collection as $item) {
                $item->load($item->getBrandId());
                $items[] = $item;
            }
            return [
                'total_count' => $collection->getSize(),
                'items' => $collection->getData()
            ];
        } else {
            return [];
        }
    }
}
