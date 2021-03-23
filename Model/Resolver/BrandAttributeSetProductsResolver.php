<?php

namespace Lof\BrandGraphQl\Model\Resolver;

use Lof\BrandGraphQl\Api\BrandRepositoryInterface;
use Lof\BrandGraphQl\Model\Resolver\Query\Products\ProductQueryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Ves\Brand\Model\BrandFactory;

/**
 * Class to resolve custom attribute_set_name field in brand GraphQL query
 */
class BrandAttributeSetProductsResolver implements ResolverInterface
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * BrandAttributeSetProductsResolver constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param BrandFactory $brand
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        BrandFactory $brand
    ) {
        $this->productRepository = $productRepository;
        $this->brandFactory = $brand;
    }


    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (isset($value['brand_id']) && $value['brand_id']) {
            $brand = $this->brandFactory->create()->load($value['brand_id']);
            $products = $brand->getData('productIds');
            $items = [];
            foreach ($products as $key => $productId) {
                $product = $this->productRepository->getById($productId);
                $items[$key] = $product->getData();
                $items[$key]['model'] = $product;
            }
            return [
                'total_count' => count($items),
                'items' => $items
            ];
        } else {
            return [];
        }
    }
}
