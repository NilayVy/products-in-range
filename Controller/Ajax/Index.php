<?php
/**
 * @author Nilay
 * @package Nilay\ProductsInRange
 */
namespace Nilay\ProductsInRange\Controller\Ajax;

use Nilay\ProductsInRange\Model\ProductData;
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
     
        if (!$this->validateFormData()) {
            
            if(!$this->numberValidate($this->getRequest()->getParam('min_price')) || $this->getRequest()->getParam('min_price')==""){
                return $resultJson->setData([
                    'error' => 'Min price is required field and should be valid formate'
                ]);
            }

            if(!$this->numberValidate($this->getRequest()->getParam('max_price')) || $this->getRequest()->getParam('max_price')==""){
                return $resultJson->setData([
                    'error' => 'Max Price is required field and should be valid formate'
                ]);
            }
            if($this->getRequest()->getParam('sort_by')==""){
                return $resultJson->setData([
                    'error' => 'Short by is required field'
                ]);
            }
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
        $this->_minPrice = (float) $this->getRequest()->getPost('min_price');
        $this->_maxPrice = (float) $this->getRequest()->getPost('max_price');
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

    private function numberValidate($value)
    {
        return preg_match('/^([0-9]+)$/', $value);
    }
}
