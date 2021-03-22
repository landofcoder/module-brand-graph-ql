<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BrandGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lof\BrandGraphQl\Api\BrandRepositoryInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;

class Groups implements ResolverInterface
{

    private $brandDataProvider;
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param DataProvider\Brand $brandDataProvider
     * @param BrandRepositoryInterface $brandRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        DataProvider\Brand $brandDataProvider,
        BrandRepositoryInterface $brandRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->brandRepository = $brandRepository;
        $this->brandDataProvider = $brandDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        $searchCriteria = $this->searchCriteriaBuilder->build( 'lofBrands', $args );
        $searchCriteria->setCurrentPage( $args['currentPage'] );
        $searchCriteria->setPageSize( $args['pageSize'] );
        $search = '';
        if (isset($args['search']) && $args['search']) {
            $search = $args['search'];
        }
        $searchResult = $this->brandRepository->getList( $searchCriteria , $search);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItems(),
        ];
    }
}
