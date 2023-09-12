<?php
/**
 * @author Nilay
 * @package NilayVy\ProductsInRange
 */
namespace NilayVy\ProductsInRange\Controller\Ajax;

use Magento\Framework\View\Result\Page;
use NilayVy\ProductsInRange\Model\ProductData;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index implements HttpGetActionInterface, HttpPostActionInterface
{
    protected const TIMES = 5;

    /** @var RequestInterface */
    protected RequestInterface $_request;

    /** @var JsonFactory */
    protected JsonFactory $_resultJsonFactory;

    /** @var ProductData */
    protected ProductData $_productData;

    /** @var float */
    protected float $_minPrice;

    /** @var float */
    protected float $_maxPrice;

    /**
     * @param RequestInterface $_request
     * @param JsonFactory $_resultJsonFactory
     * @param ProductData $_productData
     */
    public function __construct(
        RequestInterface $_request,
        JsonFactory $_resultJsonFactory,
        ProductData $_productData
    ) {
        $this->_request = $_request;
        $this->_minPrice = (int) $this->_request->getParam('min_price') ?? null;
        $this->_maxPrice = (int) $this->_request->getParam('max_price') ?? null;
        $this->_resultJsonFactory = $_resultJsonFactory;
        $this->_productData = $_productData;
    }

    /**
     * "Products in Range" load grid results
     *
     * @return Page
     */
    public function execute()
    {
        $resultJson = $this->_resultJsonFactory->create();

        if (!is_numeric($this->_minPrice) ||
         !is_numeric($this->_maxPrice)) {
            return $resultJson->setData([
                'error' => 'Min and max price are reuired field. Price should be numeric.'
            ]);
        }

        if (!$this->validateFormData()) {
            return $resultJson->setData([
            'error' => 'Maximum range shoud not greater then '.self::TIMES.'x of min range. Please try again later.'
            ]);
        }

        if (empty($this->getProductData())) {
            return $resultJson->setData([
                'error' => 'No Product data found'
            ]);
        }

        return $resultJson->setData($this->getProductData());
    }

    /**
     * Validate form values
     *
     * @return boolean
     */
    protected function validateFormData()
    {
        if (!$this->_minPrice ||
          !$this->_maxPrice) {
            return false;
        }

        if ($this->_maxPrice < $this->_minPrice) {
            return false;
        }
        if ($this->_maxPrice > ($this->_minPrice * self::TIMES)) {
            return false;
        }

        return true;
    }

    /**
     * Get product data for grid
     *
     * @return array
     */
    protected function getProductData(): array
    {
        return $this->_productData->setPriceRange([
        'min_price' => $this->_minPrice,
        'max_price' => $this->_maxPrice
        ])->setSortBy(
            $this->_request->getParam('sort_by')
        )->getProductCollection();
    }
}
