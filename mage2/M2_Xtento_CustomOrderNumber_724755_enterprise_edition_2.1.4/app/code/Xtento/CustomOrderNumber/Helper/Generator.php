<?php

/**
 * Product:       Xtento_CustomOrderNumber (2.1.4)
 * ID:            Vqe1Nlt+QHcjmGhTx6IEBsWMPAIAPerLtUmPxDsEfQM=
 * Packaged:      2017-11-06T09:34:50+00:00
 * Last Modified: 2017-10-16T16:20:56+00:00
 * File:          app/code/Xtento/CustomOrderNumber/Helper/Generator.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomOrderNumber\Helper;

class Generator extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Xtento\CustomOrderNumber\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Xtento\CustomOrderNumber\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Generator constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Module $moduleHelper
     * @param Config $configHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Xtento\CustomOrderNumber\Logger\Logger $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Xtento\CustomOrderNumber\Helper\Module $moduleHelper,
        \Xtento\CustomOrderNumber\Helper\Config $configHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Xtento\CustomOrderNumber\Logger\Logger $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
        $this->moduleHelper = $moduleHelper;
        $this->configHelper = $configHelper;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->localeDate = $localeDate;
        $this->eventManager = $context->getEventManager();
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Generate increment ID for orders/invoices/shipment/credit memos
     *
     * @param Sequence $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function generateIncrementId($object, $entityType, $originalSequence)
    {
        $storeId = $object->getStoreId();

        if (!$this->moduleHelper->isModuleEnabled()) {
            return $originalSequence;
        }

        try {
            if (!$originalSequence) {
                // There was a problem. Don't hook in either.
                return $originalSequence;
            }

            // Is the module enabled?
            if (!$this->moduleHelper->isModuleEnabled()) {
                return $originalSequence;
            }

            // Is supported entity_type?
            if (!$this->configHelper->isSupportedEntityType($entityType)) {
                return $originalSequence;
            }

            // Is (order/invoice/...) number customizer enabled for this store ID?
            if (!$this->configHelper->getConfigFlag($entityType, 'enabled', $storeId)) {
                return $originalSequence;
            }

            // Shall we create a new number or will the order number be used instead?
            // Just for invoice/shipment/credit memo numbers
            if ($entityType == 'invoice' || $entityType == 'shipment' || $entityType == 'creditmemo') {
                if ($this->configHelper->getConfigFlag($entityType, 'same_as_order', $storeId)) {
                    return $originalSequence;
                }
            }

            $newIncrementId = $this->generateCustomIncrementId($entityType, $storeId);
            if (!$newIncrementId || empty($newIncrementId)) {
                $this->logger->warning(
                    __(
                        'Attention: For %1 ID %2, an empty increment ID was generated: %3',
                        $entityType,
                        $originalSequence,
                        $newIncrementId
                    )
                );
                return $originalSequence;
            }

            // Check if increment ID exists already, if yes return non-existing increment ID
            if ($this->isIncrementIdExisting($entityType, $newIncrementId)) {
                // Try creating another ID, see again if that already exists
                $newIncrementId = $this->generateCustomIncrementId($entityType, $storeId);
                if ($this->isIncrementIdExisting($entityType, $newIncrementId)) {
                    $this->logger->warning(
                        __(
                            'Attention: Generated %1 increment_id "%2" already exists, two attempts were made. Using Magento increment_id "%3" instead.',
                            $entityType,
                            $newIncrementId,
                            $originalSequence
                        )
                    );
                    return $originalSequence;
                }
            }
        } catch (\Exception $e) {
            $this->logger->alert('Exception while generating new increment ID: '. $e->getMessage(). ' - ' . $e->getTraceAsString());
            return $originalSequence;
        }

        return $newIncrementId;
    }


    protected function isIncrementIdExisting($entityType, $incrementId)
    {
        if ($entityType == \Magento\Sales\Model\Order::ENTITY) {
            $entity = '\Magento\Sales\Model\Order';
        } else {
            if ($entityType == 'invoice') {
                $entity = '\Magento\Sales\Model\Order\Invoice';
            } else {
                if ($entityType == 'shipment') {
                    $entity = '\Magento\Sales\Model\Order\Shipment';
                } else {
                    if ($entityType == 'creditmemo') {
                        $entity = '\Magento\Sales\Model\Order\Creditmemo';
                    } else {
                        $this->logger->warning(
                            __('Attention: Specified entity %1 is not supported by the extension.', $entityType)
                        );
                        return true;
                    }
                }
            }
        }

        // Check if increment ID exists
        $objectIds = $this->objectManager->create($entity)
            ->getCollection()
            ->addAttributeToFilter('increment_id', $incrementId)
            ->getAllIds();
        if (!empty($objectIds)) {
            return true;
        }
        return false;
    }

    protected function generateCustomIncrementId($entityType, $storeId)
    {
        $incrementIdFormat = $this->configHelper->getConfigValue(
            $entityType,
            'id_format',
            $storeId
        ); // Increment ID format
        $incrementBy = $this->configHelper->prepareIntPositive(
            $this->configHelper->getConfigValue($entityType, 'increment_by', $storeId)
        ); // Increase counter by X
        $incrementPadding = $this->configHelper->prepareIntPositive(
            $this->configHelper->getConfigValue($entityType, 'padding', $storeId)
        ); // Counter padding
        $resetCounter = $this->configHelper->getConfigValue(
            $entityType,
            'reset_counter',
            $storeId
        ); // Don't reset, daily, weekly, ...
        $lastResetDate = $this->configHelper->getConfigValueFromDb(
            $entityType,
            'reset_date',
            $storeId
        ); // Last reset date
        $countFromValue = $this->configHelper->prepareIntPositive(
            $this->configHelper->getConfigValue($entityType, 'count_from', $storeId)
        ); // Start counting from...
        $forceResetCounterNow = $this->configHelper->getConfigValueFromDb(
            $entityType,
            'force_reset_counter',
            $storeId
        );

        $incrementCounter = $this->configHelper->getConfigValueFromDb(
            $entityType,
            'increment_counter',
            $storeId
        );
        $currentCounterValue = $this->configHelper->prepareIntPositive($incrementCounter->getValue());
        if ($incrementCounter && $currentCounterValue > 0) {
            $lastResetDateValue = $lastResetDate->getValue();
            if ($resetCounter !== '' && !empty($lastResetDateValue)) {
                // Counter must be reset daily/weekly/yearly
                $dateFormat = false;
                if ($resetCounter == "daily") {
                    $dateFormat = "Y-m-d";
                } elseif ($resetCounter == "monthly") {
                    $dateFormat = "Y-m";
                } elseif ($resetCounter == "yearly") {
                    $dateFormat = "Y";
                }
                if ($dateFormat) {
                    $dateHasChanged = false;
                    /*if (!$lastResetDateValue) {
                        $dateHasChanged = true;
                    }*/
                    if ($this->localeDate->scopeDate()->format($dateFormat) != $this->localeDate->scopeDate(null, $lastResetDateValue)->format($dateFormat)) {
                        $dateHasChanged = true;
                    }
                    // Reset counter
                    if ($dateHasChanged) {
                        $currentCounterValue = $countFromValue;
                    }
                }
            }

            if ($incrementBy < 1) {
                $incrementBy = 1;
            }
            $newCounterValue = $currentCounterValue + $incrementBy;

            if ($forceResetCounterNow->getValue() === '1') {
                $newCounterValue = $countFromValue;
                $forceResetCounterNow->setValue('')->save();
            }
        } else {
            $newCounterValue = $countFromValue;
        }

        // Save current date
        $dateToday = $this->localeDate->scopeDate()->format('Y-m-d');
        $lastResetDate->setValue($dateToday)->save();
        // Update last ID field
        $incrementCounter->setValue($newCounterValue)->save();

        // Padding
        if ($incrementPadding > 0) {
            $newCounterValue = str_pad($newCounterValue, $incrementPadding, 0, STR_PAD_LEFT);
        }

        // Replace variables
        $replaceableVariables = [
            '/%d%/' => $this->localeDate->scopeDate()->format('j'),
            '/%dd%/' => $this->localeDate->scopeDate()->format('d'),
            '/%m%/' => $this->localeDate->scopeDate()->format('n'),
            '/%mm%/' => $this->localeDate->scopeDate()->format('m'),
            '/%yy%/' => $this->localeDate->scopeDate()->format('y'),
            '/%yyyy%/' => $this->localeDate->scopeDate()->format('Y'),
            '/%h%/' => $this->localeDate->scopeDate()->format('G'),
            '/%hh%/' => $this->localeDate->scopeDate()->format('H'),
            '/%ii%/' => $this->localeDate->scopeDate()->format('i'),
            '/%ss%/' => $this->localeDate->scopeDate()->format('s'),
            '/%uniqid%/' => uniqid(),
            '/%store_id%/' => $storeId,
            '/%counter%/' => $newCounterValue,
            '/%rand3%/' => rand(100, 999),
            '/%rand4%/' => rand(1000, 9999),
            '/%rand5%/' => rand(10000, 99999),
            '/%rand6%/' => rand(100000, 999999),
            '/%rand7%/' => rand(1000000, 9999999),
            '/%rand8%/' => rand(10000000, 99999999),
            '/%rand9%/' => rand(100000000, 999999999),
        ];

        // Ability to add custom variables to the increment_id format using an event
        $transportObject = new \Magento\Framework\DataObject;
        $transportObject->setCustomVariables([]);
        $transportObject->setExistingVariables($replaceableVariables);
        $this->eventManager->dispatch(
            'xtento_customordernumber_replace_variables_before',
            ['transport' => $transportObject]
        );
        $replaceableVariables = array_merge($replaceableVariables, $transportObject->getCustomVariables());

        // Generate new increment_id
        $newIncrementId = preg_replace(
            array_keys($replaceableVariables),
            array_values($replaceableVariables),
            $incrementIdFormat
        );

        #var_dump($incrementIdFormat, $newIncrementId, $this->_isIncrementIdExisting($newIncrementId));
        #die();

        if ($this->scopeConfig->isSetFlag('customordernumber/developer/refresh_config_cache')) {
            $this->refreshConfigCache();
        }
        return $newIncrementId;
    }

    protected function refreshConfigCache()
    {
        $this->cacheTypeList->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
    }
}
