<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_OrderDeliveryDate
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\OrderDeliveryDate\Block\Plugin\Checkout;

class LayoutProcessorPlugin
{
    protected $helper;
    const DELIVERY_FORM_DISPLAY_AT_SHIPPING_ADDRESS = 0;
    const DELIVERY_FORM_DISPLAY_AT_SHIPPING_METHOD = 1;
    const DELIVERY_FORM_DISPLAY_AT_REVIEW_PAYMENTS = 2;

    public function __construct(
        \Bss\OrderDeliveryDate\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        $container = null;
        if (!$this->helper->isEnabled()) {
            return $jsLayout;
        }
        if ($this->helper->getDisplayAt() == self::DELIVERY_FORM_DISPLAY_AT_SHIPPING_ADDRESS) {
            $container = 'before-form';
        } elseif ($this->helper->getDisplayAt() == self::DELIVERY_FORM_DISPLAY_AT_SHIPPING_METHOD) {
            $container = 'before-shipping-method-form';
        }
        $dateFormat = $this->helper->getDateFormat();
        // before place order
        if ($this->helper->getDisplayAt() == self::DELIVERY_FORM_DISPLAY_AT_REVIEW_PAYMENTS) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['beforeMethods']['children']['delivery-date'] = [
                                    'component' => 'Bss_OrderDeliveryDate/js/view/delivery-date',
                                    'displayArea' => 'delivery-date',
                                    'sortOrder' => 11
                                ];
        } else {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children'][$container]['children']['delivery-date'] = [];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children'][$container]['children']['delivery-date']['component'] = 'Bss_OrderDeliveryDate/js/view/delivery-date';
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children'][$container]['children']['delivery-date']['sortOrder'] = 10;
        }

        return $jsLayout;
    }
}
