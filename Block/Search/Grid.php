<?php
/**
 * @author Nilay
 * @package Nilay\ProductsInRange
 */
namespace Nilay\ProductsInRange\Block\Search;

use Magento\Framework\View\Element\Template;

class Grid extends Template
{
  /**
   * Set search title
   *
   * @return $this
   */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Search Products'));
        return parent::_prepareLayout();
    }

  /**
   * Get form submission URL
   *
   * @return string
   */
    public function getAjaxUrl()
    {
        return $this->getUrl('productrange/ajax/index');
    }
}
