define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'mage/calendar'
    ],
    function($, ko, Component) {
        'use strict';

        $.extend(true, $, {
            calendarConfig: {
                serverTimezoneOffset: parseInt(window.checkoutConfig.bss_delivery_time_zone)
            }
        });

        return Component.extend({
            defaults: {
                template: 'Bss_OrderDeliveryDate/delivery-date'
            },

            bssDeliveryEnable: ko.observable(window.checkoutConfig.bss_delivery_enable),

            bssDeliveryTimeSlot: ko.observable(window.checkoutConfig.bss_delivery_has_timeslot),

            bssShippingComment: ko.observable(window.checkoutConfig.bss_shipping_comment),

            listTimeSlot: function() {
                var listTimeSlot = window.checkoutConfig.bss_delivery_timeslot;
                var newTimeSlotArray = [];
                $.each(listTimeSlot, function(index, value) {
                    newTimeSlotArray[index] = value.value;
                });
                return newTimeSlotArray;
            },

            initDate: function () {
                var self = this,
                    image = '',
                    icon = false;
                if (window.checkoutConfig.bss_delivery_icon) {
                    image = window.checkoutConfig.bss_delivery_icon;
                    icon = true;
                }
                $("#shipping_arrival_date").calendar({
                    showsTime: false,
                    controlType: 'select',
                    showOn: "button",
                    buttonImage: image,
                    buttonImageOnly: icon,
                    buttonText: "Select date",
                    dateFormat: window.checkoutConfig.bss_delivery_date_fomat,
                    beforeShowDay: self.configDate
                });
            },

            configDate: function (date) {
                var minTime = jQuery.datepicker._getTimezoneDate().getTime() + (window.checkoutConfig.bss_delivery_process_time - 1) * 60 * 60 * 24 * 1000;
                if (date.getTime() < minTime) {
                    return false;
                }

                var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                var day = date.getDay(),
                    block_out_holidays = window.checkoutConfig.bss_delivery_block_out_holidays;
                var day_off_arr = [];
                var day_off = window.checkoutConfig.bss_delivery_day_off;
                if (day_off) {
                    day_off_arr = day_off.split(',');
                }
                for (var i = 0;i < day_off_arr.length; i++){
                    day_off_arr[i] = parseInt(day_off_arr[i]);
                }
                if(day_off_arr.indexOf(day) == -1 && block_out_holidays.indexOf(string) == -1) {
                    return[true, ''];  
                } 

                return [false, ''];
            }
        });
    }
);
