define([
    'Yireo_EmailTester2/js/form/element/autocomplete'
    ], function (Autocomplete) {

    var orderAjaxUrl = 'https://magento2.yireo.dev/admin/emailtester/ajax/order/key/b9d22b029a3b2f65b315d38876495058b545c0ce57a103af19e3624549deefd2/';

    return Autocomplete.extend({
            'defaults': {
                'autocompleteUrl': orderAjaxUrl,
                'autocompleteTargetElement': 'input[name=order_id]'
            }
        });
    }
);