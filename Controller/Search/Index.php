<?php
/**
 * @author Nilay
 * @package NilayVy\ProductsInRange
 */
namespace NilayVy\ProductsInRange\Controller\Search;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /** @var PageFactory */
    protected $_resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $_resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $_resultPageFactory
    ) {
        $this->_resultPageFactory = $_resultPageFactory;
        parent::__construct($context);
    }

    /**
     * "Products in Range" search grid page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
      /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Search Products'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
