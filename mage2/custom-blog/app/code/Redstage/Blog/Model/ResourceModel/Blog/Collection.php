<?php

/**
 * Blog Resource Collection
 */
namespace Redstage\Blog\Model\ResourceModel\Blog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Redstage\Blog\Model\Blog', 'Redstage\Blog\Model\ResourceModel\Blog');
    }
}
