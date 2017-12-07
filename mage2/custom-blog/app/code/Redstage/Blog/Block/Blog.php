<?php

namespace Redstage\Blog\Block;

/**
 * Blog content block
 */
class Blog extends \Magento\Framework\View\Element\Template
{
    /**
     * Blog collection
     *
     * @var Redstage\Blog\Model\ResourceModel\Blog\Collection
     */
    protected $_blogCollection = null;
    
    /**
     * Blog factory
     *
     * @var \Redstage\Blog\Model\BlogFactory
     */
    protected $_blogCollectionFactory;
    
    /** @var \Redstage\Blog\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Redstage\Blog\Model\ResourceModel\Blog\CollectionFactory $blogCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Redstage\Blog\Model\ResourceModel\Blog\CollectionFactory $blogCollectionFactory,
        \Redstage\Blog\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_blogCollectionFactory = $blogCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve blog collection
     *
     * @return Redstage\Blog\Model\ResourceModel\Blog\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_blogCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared blog collection
     *
     * @return Redstage\Blog\Model\ResourceModel\Blog\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_blogCollection)) {
            $this->_blogCollection = $this->_getCollection();
            $this->_blogCollection->setCurPage($this->getCurrentPage());
            $this->_blogCollection->setPageSize($this->_dataHelper->getBlogPerPage());
            $this->_blogCollection->setOrder('published_at','asc');
        }

        return $this->_blogCollection;
    }
    
    /**
     * Fetch the current page for the blog list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Redstage\Blog\Model\Blog $blogItem
     * @return string
     */
    public function getItemUrl($blogItem)
    {
        return $this->getUrl('*/*/view', array('id' => $blogItem->getId()));
    }
    
    /**
     * Return URL for resized Blog Item image
     *
     * @param Redstage\Blog\Model\Blog $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('blog_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $blogPerPage = $this->_dataHelper->getBlogPerPage();

            $pager->setAvailableLimit([$blogPerPage => $blogPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
}
