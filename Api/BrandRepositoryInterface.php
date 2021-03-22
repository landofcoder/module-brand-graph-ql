<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BrandGraphQl\Api;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface BrandRepositoryInterface
 * @package Lof\BrandGraphQl\Api
 */
interface BrandRepositoryInterface
{

    /**
     * Save brand
     * @param \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
    );

    /**
     * Retrieve brand
     * @param string $brandId
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($brandId);

    /**
     * Retrieve brand matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $search
     * @return \Lof\BrandGraphQl\Api\Data\BrandSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $search
    );

    /**
     * Retrieve brand matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param Int $productId
     * @return \Lof\BrandGraphQl\Api\Data\BrandSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByProduct(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        int $productId
    );

    /**
     * Delete brand
     * @param \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
    );

    /**
     * Delete brand by ID
     * @param string $brandId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($brandId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $search
     * @return mixed
     */
    public function getGroups(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $search
    );
}
