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
namespace Bss\OrderDeliveryDate\Helper;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Visitor Observer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $localeDate;
    protected $storeManager;
    protected $date;
    protected $timezone;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        TimezoneInterface $timezone,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->localeDate = $localeDate;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
    }

    public function isEnabled()
    {
        $active =  $this->scopeConfig->getValue('orderdeliverydate/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($active != 1) {
            return false;
        }

        return true;
    }

    public function isShowShippingComment()
    {
        $active =  $this->scopeConfig->getValue('orderdeliverydate/general/shipping_comment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($active != 1) {
            return false;
        }

        return true;
    }

    public function getDisplayAt()
    {
        $active =  $this->scopeConfig->getValue('orderdeliverydate/general/on_which_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $active;
    }

    public function getProcessingTime()
    {
        $process_time =  $this->scopeConfig->getValue('orderdeliverydate/general/process_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$process_time) {
            return 0;
        }
        return $process_time;
    }

    public function getCutOffTime()
    {
        $cut_off_time =  $this->scopeConfig->getValue('orderdeliverydate/general/cut_off_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$cut_off_time || $cut_off_time == '00,00,00') {
            return false;
        }

        $cutOffDate = $this->localeDate->date()->format('Y-m-d') . ' ' . str_replace(',', ':', $cut_off_time);
        $cut_off_time_convert = strtotime($cutOffDate);

        return $cut_off_time_convert;
    }

    public function getBlockHoliday()
    {
        $block_out_holidays =  $this->scopeConfig->getValue('orderdeliverydate/general/block_out_holidays', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $response = [];
        if ($block_out_holidays) {
            $block_out_holidays_arr = unserialize($block_out_holidays);
            if ($block_out_holidays_arr) {
                foreach ($block_out_holidays_arr as $holidays) {
                    $newDate = date("Y-m-d", strtotime($holidays['date']));
                    $response[] = $newDate;
                }
            }
        }
        return serialize($response);
    }

    public function getTimeSlot()
    {
        $time_slots = $this->scopeConfig->getValue('orderdeliverydate/general/time_slots', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $result = [];
        if ($time_slots) {
            $time_slot_arr = unserialize($time_slots);
            if ($time_slot_arr) {
                foreach ($time_slot_arr as $time_slot) {
                    $a = $time_slot['note'].' ' . $time_slot['from'] . ' - ' . $time_slot['to'];
                    $b = ['value' => $a, 'label' => $a];
                    array_push($result, $b);
                }
            }
        }
        return $result;
    }

    public function getDayOff()
    {
        $day_off =  $this->scopeConfig->getValue('orderdeliverydate/general/deliverydate_day_off', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (is_null($day_off)) {
            return false;
        }
        return $day_off;
    }

    public function getIcon()
    {
        $icon =  $this->scopeConfig->getValue('orderdeliverydate/general/icon_calendar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!isset($icon)) {
            return false;
        }
        return $this->getMediaUrl() . 'bss/deliverydate/' . $icon;
    }

    public function getDateFormat()
    {
        $dateFormat = $this->scopeConfig->getValue('orderdeliverydate/general/date_fields');
        if ($dateFormat) {
            switch ($dateFormat) {
                case 1:
                    return 'mm/dd/yy';
                    break;
                case 2:
                    return 'dd-mm-yy';
                    break;
                case 3:
                    return 'yy-mm-dd';
                    break;
                default:
                    return 'mm/dd/yy';
                    break;
            }
        }
        return 'mm/dd/yy';
    }

    public function formatDate($date = null)
    {
        $dateFormat = $this->getDateFormat();
        if ($dateFormat) {
            switch ($dateFormat) {
                case 'mm/dd/yy':
                    $dateFormat = 'm/d/Y';
                    break;
                case 'dd-mm-yy':
                    $dateFormat = 'd-m-Y';
                    break;
                case 'yy-mm-dd':
                    $dateFormat = 'Y-m-d';
                    break;
                default:
                    $dateFormat = 'm/d/y';
                    break;
            }
        }
        if($date){
            $date = $this->timezone->date(new \DateTime($date));
            return $date->format($dateFormat);
        }else{
            return $dateFormat;
        }
    }

    public function getStoreTimestamp($store = null)
    {
        return $this->localeDate->scopeTimeStamp($store);
    }

    public function getTimezoneOffsetSeconds()
    {
        return $this->date->getGmtOffset();
    }

    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
