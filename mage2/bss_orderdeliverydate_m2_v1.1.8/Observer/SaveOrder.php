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
namespace Bss\OrderDeliveryDate\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveOrder implements ObserverInterface
{
  protected $helper;

  protected $request;

	public function __construct(
    \Magento\Framework\App\RequestInterface $request,
		\Bss\OrderDeliveryDate\Helper\Data $helper
	)
  {
    $this->helper = $helper;
    $this->request = $request;
  }

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
  	if ($this->helper->isEnabled()) {
      $order = $observer->getOrder();
      $params = $this->request->getParams();

      if (isset($params['shipping_arrival_date'])) {
        $order->setShippingArrivalDate($params['shipping_arrival_date']);
      }
      if (isset($params['delivery_time_slot'])) {
        $order->setShippingArrivalTimeslot($params['delivery_time_slot']);
      }
      if (isset($params['shipping_arrival_comments'])) {
        $order->setShippingArrivalComments($params['shipping_arrival_comments']);
      }
      $order->save();
    }
  }
}
