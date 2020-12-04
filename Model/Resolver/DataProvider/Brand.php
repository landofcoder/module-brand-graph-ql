<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BrandGraphQl\Model\Resolver\DataProvider;

use Lof\BrandGraphQl\Api\BrandRepositoryInterface;

class Brand
{
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    public function __construct(
        BrandRepositoryInterface $brandRepository
    )
    {
        $this->brandRepository = $brandRepository;
    }

    public function getBrand($brandId)
    {
        return $this->brandRepository->get($brandId);
    }

}
