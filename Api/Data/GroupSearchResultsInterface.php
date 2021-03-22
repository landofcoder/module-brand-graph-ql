<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BrandGraphQl\Api\Data;

interface GroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Group list.
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface[]
     */
    public function getItems();

    /**
     * Set group_id list.
     * @param \Lof\BrandGraphQl\Api\Data\GroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
