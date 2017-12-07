<?php

namespace Redstage\Blog\Model\ResourceModel;

/**
 * Blog Resource Model
 */
class Blog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('redstage_blog', 'blog_id');
    }
}
