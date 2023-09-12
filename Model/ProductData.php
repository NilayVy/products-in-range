<?php
/**
 * @author Nilay
 * @package NilayVy\ProductsInRange
 */
namespace NilayVy\ProductsInRange\Model;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Helper\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\DB\Select;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ProductData
{
    protected const PAGE_SIZE = 10;
    protected const CURRENT_PAGE = 1;

    /** @var CollectionFactory */
    protected CollectionFactory $_productCollectionFactory;

    /** @var Magento\Catalog\Model\ResourceModel\Product\Collection */
    protected $_productCollection;

    /** @var Product */
    protected Product $_productHelper;

    /** @var StoreManagerInterface */
    protected StoreManagerInterface $_storeManager;

    /** @var Visibility */
    protected Visibility $_productVisibility;

    /** @var PricingHelper */
    protected PricingHelper $_pricingHelper;

    /** @var float */
    protected float $_minPrice;

    /** @var float */
    protected float $_maxPrice;

    /** @var string */
    protected string $_sortBy;

    /**
     * @param Context $context
     * @param CollectionFactory $_productCollectionFactory
     * @param Product $_productHelper
     * @param StoreManagerInterface $_storeManager
     * @param Visibility $_productVisibility
     * @param PricingHelper $_pricingHelper
     */
    public function __construct(
        Context $context,
        CollectionFactory $_productCollectionFactory,
        Product $_productHelper,
        StoreManagerInterface $_storeManager,
        Visibility $_productVisibility,
        PricingHelper $_pricingHelper
    ) {
        $this->_productCollectionFactory = $_productCollectionFactory;
        $this->_productHelper = $_productHelper;
        $this->_storeManager = $_storeManager;
        $this->_productVisibility = $_productVisibility;
        $this->_pricingHelper = $_pricingHelper;
        $this->_sortBy = Select::SQL_ASC;
    }

    /**
     * Set minimum & maximum price values for collection filter
     *
     * @param array $values
     * @return $this
     */
    public function setPriceRange(array $values): static
    {
        if (isset($values['min_price'])) {
            $this->_minPrice = (int) $values['min_price'];
        }
        if (isset($values['max_price'])) {
            $this->_maxPrice = (int) $values['max_price'];
        }
        $this->_productCollection = null;
        return $this;
    }

    /**
     * Set "sort by" value for product collection
     *
     * @param string $value
     * @return $this
     */
    public function setSortBy(string $value): static
    {
        switch ($value) {
            case 'asc':
                $this->_sortBy = Select::SQL_ASC;
                break;
            case 'desc':
                $this->_sortBy = Select::SQL_DESC;
                break;
        }
        $this->_productCollection = null;
        return $this;
    }

    /**
     * Get product collection filtered by price
     *
     * @param boolean $toArray
     * @return Collection|array
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getProductCollection(bool $toArray = true): Collection|array
    {
        $_productCollection = $this->_productCollectionFactory->create();
        $_productCollection->addAttributeToSelect('name')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('*')->addFinalPrice()
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addFieldToFilter('price', [
          'from' => $this->_minPrice,
          'to' => $this->_maxPrice
        ])
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('thumbnail')
        ->addAttributeToFilter('status', Status::STATUS_ENABLED)
        ->setVisibility($this->_productVisibility->getVisibleInSiteIds())
        ->joinField(
            'qty',
            'cataloginventory_stock_item',
            'qty',
            'product_id=entity_id'
        )
        ->setOrder('price', $this->_sortBy)
        ->setPageSize(self::PAGE_SIZE)
        ->setCurPage(self::CURRENT_PAGE);
        $this->_productCollection = $_productCollection;

        if ($toArray) {
            return $this->productArray();
        } else {
            return $this->_productCollection;
        }
    }

    /**
     * Extract data from product collection to array
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function productArray(): array
    {
        $productArray = [];
        foreach ($this->_productCollection as $product) {
           // echo "<pre>";
            //print_r($product->getData());exit;
            $productData = [
              'thumbnail' => $this->_storeManager->getStore()
                  ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                  . 'catalog/product' . $product->getThumbnail(),
              'sku' => $product->getSku(),
              'name' => $product->getName(),
              'qty' => (int)(($product->getQty()) ? $product->getQty() : 0),
              'final_price' => $this->_pricingHelper->currency(
                  $product->getFinalPrice(),
                  true,
                  false
              ),
              'price' => $this->_pricingHelper->currency(
                  $product->getData('price'),
                  true,
                  false
              ),
              'url' => $this->_productHelper->getProductUrl($product->getId())
            ];
            $productArray[] = $productData;
        }

        return $productArray;
    }
}
