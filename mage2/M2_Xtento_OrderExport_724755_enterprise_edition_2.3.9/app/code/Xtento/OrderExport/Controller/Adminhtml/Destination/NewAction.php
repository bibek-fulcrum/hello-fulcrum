<?php

/**
 * Product:       Xtento_OrderExport (2.3.9)
 * ID:            Vqe1Nlt+QHcjmGhTx6IEBsWMPAIAPerLtUmPxDsEfQM=
 * Packaged:      2017-11-06T09:34:59+00:00
 * Last Modified: 2015-08-09T14:36:11+00:00
 * File:          app/code/Xtento/OrderExport/Controller/Adminhtml/Destination/NewAction.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Controller\Adminhtml\Destination;

class NewAction extends \Xtento\OrderExport\Controller\Adminhtml\Destination
{
    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $result->forward('edit');
    }
}