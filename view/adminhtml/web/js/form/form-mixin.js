define([
    'jquery',
    'Magento_Ui/js/form/adapter',
    'mage/url'
], function ($, adapter, url) {
    'use strict';

    var replaceUrl = function (url, replace) {
        return url.replace('emailtester/index/index', 'emailtester/index/' + replace);
    };

    var mixin = {
        initAdapter: function () {
            this._super();
            adapter.on({
                'emailtester-preview': this.emailTesterSubmitToPreview.bind(this, true, {}),
                'emailtester-send': this.emailTesterSubmitToSend.bind(this, true, {})
            }, this.selectorPrefix, this.eventPrefix);

            return this;
        },
        emailTesterSubmitToPreview: function (redirect, data) {
            function getValueFromInput(inputName) {
                var $input = $('[name=' + inputName + ']')
                if ($input && $input.val() !== undefined) {
                    return $input.val();
                }
            }

            function convertKeysToQueryString(keys) {
                return keys.map(function (key) {
                    var value = getValueFromInput(key);
                    return key + '=' + value;
                }).join('&');
            }

            var redirectUrl = window.emailtester.previewUrl;
            var formKeys = ['sender', 'email', 'store_id', 'template', 'customer_id', 'product_id', 'order_id'];
            redirectUrl += '?' + convertKeysToQueryString(formKeys);
            window.open(redirectUrl, '_blank');
        },
        emailTesterSubmitToSend: function (redirect, data) {
            this.source.client.urls.save = replaceUrl(this.source.submit_url, 'send');
            return this.save(redirect, data);
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
