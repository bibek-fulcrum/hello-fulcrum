<?php

/**
 * Product:       Xtento_OrderExport (2.3.9)
 * ID:            Vqe1Nlt+QHcjmGhTx6IEBsWMPAIAPerLtUmPxDsEfQM=
 * Packaged:      2017-11-06T09:34:59+00:00
 * Last Modified: 2017-08-17T12:56:09+00:00
 * File:          app/code/Xtento/OrderExport/Test/SerializedToJsonDataConverter.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Test;

use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Serializer used to convert data to JSON
 *
 * This class is not a test. We had to place it here to avoid code compilation on pre-2.2 systems, where the implemented interface doesn't exist.
 */
class SerializedToJsonDataConverter implements \Magento\Framework\DB\DataConverter\DataConverterInterface
{
    /**
     * @var Serialize
     */
    private $serialize;

    /**
     * @var Json
     */
    private $json;

    /**
     * Constructor
     *
     * @param Serialize $serialize
     * @param Json $json
     */
    public function __construct(
        Serialize $serialize,
        Json $json
    ) {
        $this->serialize = $serialize;
        $this->json = $json;
    }

    /**
     * Convert from serialized to JSON format
     *
     * @param string $value
     *
     * @return string
     */
    public function convert($value)
    {
        $isSerialized = $this->isSerialized($value);
        if (!$isSerialized) {
            return $value;
        }
        $unserialized = $this->serialize->unserialize($value);
        return $this->json->serialize($unserialized);
    }

    /**
     * Check if value is serialized string
     *
     * @param string $value
     *
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean)preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }
}