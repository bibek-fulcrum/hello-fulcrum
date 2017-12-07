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
namespace Bss\OrderDeliveryDate\Model\Plugin\Checkout;

class ShippingInformationManagementPlugin
{
    protected $quoteRepository;
    protected $helper;
    const DELIVERY_FORM_DISPLAY_AT_SHIPPING_ADDRESS = 0;
    const DELIVERY_FORM_DISPLAY_AT_SHIPPING_METHOD = 1;
    const DELIVERY_FORM_DISPLAY_AT_REVIEW_PAYMENTS = 2;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Bss\OrderDeliveryDate\Helper\Data $helper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        if (!$this->helper->isEnabled()) {
          return;
        }
        if ($this->helper->getDisplayAt() == self::DELIVERY_FORM_DISPLAY_AT_SHIPPING_ADDRESS || $this->helper->getDisplayAt() == self::DELIVERY_FORM_DISPLAY_AT_SHIPPING_METHOD) {
          $extAttributes = $addressInformation->getExtensionAttributes();
          $shippingArrivalDate = $extAttributes->getShippingArrivalDate();
          $deliveryTimeSlot = $extAttributes->getDeliveryTimeSlot();
          $shippingArrivalComments = $extAttributes->getShippingArrivalComments();
          $quote = $this->quoteRepository->getActive($cartId);
          if (isset($shippingArrivalDate)) {
            $quote->setShippingArrivalDate($shippingArrivalDate);
          }
          if (isset($deliveryTimeSlot)) {
            $quote->setShippingArrivalTimeslot($deliveryTimeSlot);
          }
          if (isset($shippingArrivalComments)) {
            $quote->setShippingArrivalComments($shippingArrivalComments);
          }
        }
    }
}
