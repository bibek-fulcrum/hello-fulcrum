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
namespace Bss\OrderDeliveryDate\Model;

class CompositeConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    protected $bssHelper;

    public function __construct(
        \Bss\OrderDeliveryDate\Helper\Data $bssHelper
    ) {
        $this->bssHelper = $bssHelper;
    }

    public function getConfig()
    {
        $output = [];
        if ($this->bssHelper->isEnabled()) {
            $output['bss_delivery_enable'] = (boolean) $this->bssHelper->isEnabled();
            if ($this->bssHelper->getTimeSlot()) {
                $output['bss_delivery_timeslot'] = $this->bssHelper->getTimeSlot();
                $output['bss_delivery_has_timeslot'] = true;
            }
            $day_off = $this->bssHelper->getDayOff();
            $block_out_holidays = unserialize($this->bssHelper->getBlockHoliday());
            $process_time = 0;
            $current_time = (int) $this->bssHelper->getStoreTimestamp();
            $cut_off_time_convert = $this->bssHelper->getCutOffTime();
            $process_time = $this->bssHelper->getProcessingTime();
            if($cut_off_time_convert && $current_time > $cut_off_time_convert){
                $process_time = $process_time + 1;
            }
            $output['bss_shipping_comment'] = (boolean) $this->bssHelper->isShowShippingComment();
            $output['bss_delivery_process_time'] = $process_time;
            $output['bss_delivery_block_out_holidays'] = json_encode($block_out_holidays);
            $output['bss_delivery_day_off'] = $day_off;
            $output['bss_delivery_date_fomat'] = $this->bssHelper->getDateFormat();
            $output['bss_delivery_current_time'] = $current_time;
            $output['bss_delivery_time_zone'] = $this->bssHelper->getTimezoneOffsetSeconds();
            if ($this->bssHelper->getIcon()) {
                $output['bss_delivery_icon'] = $this->bssHelper->getIcon();
            }
        }
        return $output;
    }
}
