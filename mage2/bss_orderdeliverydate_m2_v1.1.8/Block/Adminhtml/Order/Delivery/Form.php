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
namespace Bss\OrderDeliveryDate\Block\Adminhtml\Order\Delivery;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $helperBss;

    protected $order;

    protected $template = 'Bss_OrderDeliveryDate::deliverydate/form.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Sales\Model\Order $order,
        \Bss\OrderDeliveryDate\Helper\Data $helperBss,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_systemStore = $systemStore;
        $this->order = $order;
        $this->helperBss = $helperBss;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('bss_deliverydate_form');
        $this->setTitle(__('Delivery Date Information'));
    }

    public function getOrderId()
    {
        return $this->_coreRegistry->registry('order_id');
    }

    protected function _prepareForm()
    {
        $orderId = $this->getOrderId();
        $order = $this->order->load($orderId);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('orderdeliverydate/*/deliverySave', ['order_id' => $orderId]), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('bss_delivery_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Delivery Date Information'), 'class' => 'fieldset-wide']
        );

        $dateFormat = $this->helperBss->formatDate();
        $dateFormat = str_replace("m", "M", $dateFormat);
        $fieldset->addField(
            'shipping_arrival_date',
            'date',
            ['name' => 'shipping_arrival_date', 'label' => __('Delivery Date'), 'title' => __('Delivery Date'), 'date_format' => $dateFormat]
        );

        $empty = ['value' => '', 'label' => __('-- Please Select --')];
        $options = $this->helperBss->getTimeSlot();
        array_unshift($options, $empty);
        $fieldset->addField(
            'shipping_arrival_timeslot',
            'select',
            ['name' => 'shipping_arrival_timeslot', 'label' => __('Delivery Time Slot'), 'title' => __('Delivery Time Slot'),'values' => $options,]
        );

        if($this->helperBss->isShowShippingComment()){
            $fieldset->addField(
                'shipping_arrival_comments',
                'textarea',
                ['name' => 'shipping_arrival_comments', 'label' => __('Shipping Arrival Comment'), 'title' => __('Shipping Arrival Comment')]
            );
        }
        $form->setValues($order->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
