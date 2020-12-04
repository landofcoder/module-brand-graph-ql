<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BrandGraphQl\Api\Data;

interface BrandSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get brand list.
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface[]
     */
    public function getItems();

    /**
     * Set brand_id list.
     * @param \Lof\BrandGraphQl\Api\Data\BrandInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
