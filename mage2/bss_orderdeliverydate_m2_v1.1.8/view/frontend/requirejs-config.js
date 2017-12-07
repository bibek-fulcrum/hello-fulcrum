var config = {
    "map": {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default" : "Bss_OrderDeliveryDate/js/shipping-save-processor-override"
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Bss_OrderDeliveryDate/js/model/place-order-deliverydate': true
            }
        }
    }
};
