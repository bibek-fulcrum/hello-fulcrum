<?php

/**
 * Product:       Xtento_CustomOrderNumber (2.1.4)
 * ID:            Vqe1Nlt+QHcjmGhTx6IEBsWMPAIAPerLtUmPxDsEfQM=
 * Packaged:      2017-11-06T09:34:50+00:00
 * Last Modified: 2015-07-25T17:59:41+00:00
 * File:          app/code/Xtento/CustomOrderNumber/Model/System/Config/Source/CounterReset.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomOrderNumber\Model\System\Config\Source;

class CounterReset implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('--- No automatic reset ---')],
            ['value' => 'daily', 'label' => __('Reset every day')],
            ['value' => 'monthly', 'label' => __('Reset every month')],
            ['value' => 'yearly', 'label' => __('Reset every year')]
        ];
    }
}
