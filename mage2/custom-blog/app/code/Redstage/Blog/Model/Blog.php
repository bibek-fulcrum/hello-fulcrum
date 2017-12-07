<?php

namespace Redstage\Blog\Model;

/**
 * Blog Model
 *
 * @method \Redstage\Blog\Model\Resource\Page _getResource()
 * @method \Redstage\Blog\Model\Resource\Page getResource()
 */
class Blog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Redstage\Blog\Model\ResourceModel\Blog');
    }

}
