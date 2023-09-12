<?php
/**
 * @author Nilay
 * @package NilayVy\ProductsInRange
 */
namespace NilayVy\ProductsInRange\Controller\Ajax;

use NilayVy\ProductsInRange\Model\ProductData;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action
{
    protected const TIMES = 5;
    
    /** @var JsonFactory */
    protected $_resultJsonFactory;

    /** @var ProductData */
    protected $_productData;

    /** @var float */
    protected $_minPrice;

    /** @var float */
    protected $_maxPrice;

    /**
     * @param Context $context
     * @param JsonFactory $_resultJsonFactory
     * @param ProductData $_productData
     */
    public function __construct(
        Context $context,
        JsonFactory $_resultJsonFactory,
        ProductData $_productData
    ) {
        $this->_resultJsonFactory = $_resultJsonFactory;
        $this->_productData = $_productData;
        parent::__construct($context);
    }

    /**
     * "Products in Range" load grid results
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultJson = $this->_resultJsonFactory->create();
        
        if (!is_numeric($this->getRequest()->getPost('min_price')) ||
         !is_numeric($this->getRequest()->getPost('max_price'))) {
            return $resultJson->setData([
                'error' => 'Min and max price are reuired field. Price should be numeric.'
            ]);
            
        }

        if (!$this->validateFormData()) {
            return $resultJson->setData([
            'error' => 'Maximum range shoud not greater then '.self::TIMES.'x of min range. Please try again later.'
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
        if (!$this->getRequest()->getPost('min_price') ||
          !$this->getRequest()->getPost('max_price')) {
            return false;
        }

        $this->_minPrice = (int) $this->getRequest()->getPost('min_price');
        $this->_maxPrice = (int) $this->getRequest()->getPost('max_price');

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
    protected function getProductData()
    {
        return $this->_productData->setPriceRange([
        'min_price' => $this->_minPrice,
        'max_price' => $this->_maxPrice
        ])->setSortBy(
            $this->getRequest()->getPost('sort_by')
        )->getProductCollection();
    }
}
