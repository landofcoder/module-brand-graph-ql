<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\BrandGraphQl\Model;

use Lof\BrandGraphQl\Api\BrandRepositoryInterface;
use Lof\BrandGraphQl\Api\Data\BrandInterfaceFactory;
use Lof\BrandGraphQl\Api\Data\BrandSearchResultsInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Ves\Brand\Model\ResourceModel\Brand as ResourceBrand;
use Ves\Brand\Model\BrandFactory;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class BrandRepository implements BrandRepositoryInterface
{

    protected $dataObjectHelper;

    protected $brandFactory;

    protected $dataBrandFactory;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    private $collectionProcessor;

    protected $brandCollectionFactory;
    /**
     * @var ResourceConnection
     */
    private $_resourceConnection;


    /**
     * @param ResourceBrand $resource
     * @param BrandFactory $brandFactory
     * @param BrandInterfaceFactory $dataBrandFactory
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param BrandSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceBrand $resource,
        BrandFactory $brandFactory,
        BrandInterfaceFactory $dataBrandFactory,
        BrandCollectionFactory $brandCollectionFactory,
        BrandSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        ResourceConnection $resourceConnection
    ) {
        $this->resource = $resource;
        $this->brandFactory = $brandFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBrandFactory = $dataBrandFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->_resourceConnection = $resourceConnection;

    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
    ) {
        /* if (empty($brand->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $brand->setStoreId($storeId);
        } */

        $brandData = $this->extensibleDataObjectConverter->toNestedArray(
            $brand,
            [],
            \Lof\BrandGraphQl\Api\Data\BrandInterface::class
        );

        $brandModel = $this->brandFactory->create()->setData($brandData);

        try {
            $this->resource->save($brandModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the brand: %1',
                $exception->getMessage()
            ));
        }
        return $brandModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($brandId)
    {
        $brand = $this->brandFactory->create();
        $this->resource->load($brand, $brandId);
        if (!$brand->getId()) {
            throw new NoSuchEntityException(__('brand with id "%1" does not exist.', $brandId));
        }
        $item = $brand->getData();
        $item['url_key'] = $brand->getUrl();
        $item['image'] = $brand->getImageUrl();
        $item['thumbnail'] = $brand->getThumbnailUrl();
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->brandCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\BrandGraphQl\Api\Data\BrandInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $key => $model) {
            $model->load($model->getBrandId());
            $items[$key] = $model->getData();
            $items[$key] ['url_key'] = $model->getUrl();
            $items[$key] ['image'] = $model->getImageUrl();
            $items[$key] ['thumbnail'] = $model->getThumbnailUrl();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByProduct(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $productId
    ) {
        $collection = $this->brandCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\BrandGraphQl\Api\Data\BrandInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $connection = $this->_resourceConnection->getConnection();
        $table_name = $this->_resourceConnection->getTableName('ves_brand_product');
        $brandIds = $connection->fetchCol(" SELECT brand_id FROM ".$table_name." WHERE product_id = ".$productId);
        if($brandIds || count($brandIds) > 0) {
            $collection
                ->setOrder('position','ASC')
                ->addFieldToFilter('status',1);
            $collection->getSelect()->where('brand_id IN (?)', $brandIds);
            $items = [];
            foreach ($collection as $key => $model) {
                $model->load($model->getBrandId());
                $items[$key] = $model->getData();
                $items[$key]['url_key'] = $model->getUrl();
                $items[$key]['image'] = $model->getImageUrl();
                $items[$key]['thumbnail'] = $model->getThumbnailUrl();
            }

            $searchResults->setItems($items);
            $searchResults->setTotalCount($collection->getSize());
        }

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
    ) {
        try {
            $brandModel = $this->brandFactory->create();
            $this->resource->load($brandModel, $brand->getBrandId());
            $this->resource->delete($brandModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the brand: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($brandId)
    {
        return $this->delete($this->get($brandId));
    }
}
