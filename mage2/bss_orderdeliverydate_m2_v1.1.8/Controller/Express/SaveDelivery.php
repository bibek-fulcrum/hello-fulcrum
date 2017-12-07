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
namespace Bss\OrderDeliveryDate\Controller\Express;

class SaveDelivery extends \Magento\Framework\App\Action\Action
{
    protected $cartHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
        parent::__construct($context);
        $this->cartHelper = $cartHelper;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $quote = $this->cartHelper->getQuote();
        if (isset($params['delivery_arrival_date'])) {
            $quote->setShippingArrivalDate($params['delivery_arrival_date']);
        }
        if (isset($params['delivery_time_slot'])) {
            $quote->setShippingArrivalTimeslot($params['delivery_time_slot']);
        }
        if (isset($params['shipping_arrival_comments'])) {
            $quote->setShippingArrivalComments($params['shipping_arrival_comments']);
        }
        $quote->save();
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
