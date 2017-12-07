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
namespace Bss\OrderDeliveryDate\Controller\Adminhtml\Order;

class DeliverySave extends \Magento\Backend\App\Action
{
    protected $order;

    protected $helperBss;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Bss\OrderDeliveryDate\Helper\Data $helperBss
    ) {
        parent::__construct($context);
        $this->order = $order;
        $this->helperBss = $helperBss;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($params['order_id']) {
            $orderInfo = $this->order->load($params['order_id']);
            if($this->helperBss->isShowShippingComment()){
                $orderInfo->setShippingArrivalDate($params['shipping_arrival_date'])
                    ->setShippingArrivalTimeslot($params['shipping_arrival_timeslot'])
                    ->setShippingArrivalComments($params['shipping_arrival_comments'])
                    ->save();
            }else{
                $orderInfo->setShippingArrivalDate($params['shipping_arrival_date'])
                    ->setShippingArrivalTimeslot($params['shipping_arrival_timeslot'])
                    ->save();
            }
            return $resultRedirect->setPath('sales/*/view', ['order_id' => $params['order_id']]);
        } else {
            return $resultRedirect->setPath('sales/*/');
        }
    }
}
