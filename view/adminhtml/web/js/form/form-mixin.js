define([
    'jquery',
    'Magento_Ui/js/form/adapter',
    'mage/url'
], function ($, adapter, url) {
    'use strict';

    const replaceUrl = function (url, replace) {
        return url.replace('emailtester/index/index', 'emailtester/index/' + replace);
    };

    const mixin = {
        initAdapter: function () {
            this._super();
            adapter.on({
                'emailtester-preview': this.emailTesterSubmitToPreview.bind(this, true, {}),
                'emailtester-send': this.emailTesterSubmitToSend.bind(this, true, {})
            }, this.selectorPrefix, this.eventPrefix);

            return this;
        },
        emailTesterGetFormKeys: function() {
            return ['sender', 'email', 'store_id', 'template', 'customer_id', 'product_id', 'order_id'];
        },
        emailTesterSubmitToPreview: function () {
            let redirectUrl = window.emailtester.previewUrl;
            redirectUrl += '?' + this.emailTesterConvertKeysToQueryString(this.emailTesterGetFormKeys());
            window.open(redirectUrl, '_blank');
        },
        emailTesterSubmitToSend: function () {
            let redirectUrl = window.emailtester.sendUrl;
            redirectUrl += '?' + this.emailTesterConvertKeysToQueryString(this.emailTesterGetFormKeys());
            window.location = redirectUrl;
            return false;
        },
        emailTesterGetValueFromInput: function (inputName) {
            const $input = $('[name=' + inputName + ']')
            if ($input && $input.val() !== undefined) {
                return $input.val();
            }
        },
        emailTesterConvertKeysToQueryString: function (keys) {
            return keys.map((function (key) {
                return key + '=' + this.emailTesterGetValueFromInput(key);
            }).bind(this)).join('&');
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
