<?php
/**
 * @author
 * @package NilayVy\ProductsInRange
 */
namespace NilayVy\ProductsInRange\Controller\Search;

use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\RedirectInterface;

class Index implements HttpGetActionInterface
{
    /** @var PageFactory */
    protected PageFactory $_resultPageFactory;

    /** @var RedirectInterface */
    protected RedirectInterface $_redirect;

    /**
     * @param PageFactory $_resultPageFactory
     * @param RedirectInterface $_redirect
     */
    public function __construct(
        PageFactory $_resultPageFactory,
        RedirectInterface $_redirect
    ) {
        $this->_resultPageFactory = $_resultPageFactory;
        $this->_redirect = $_redirect;
    }

    /**
     * Products in Range" search grid page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Search Products'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
