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
            function convertObjectToQueryString(object) {
                return Object.keys(object).map(function (key) {
                    if (!object[key]) {
                        return;
                    }

                    var value = object[key];
                    var $input = $('input[name=' + key + ']')
                    if ($input && $input.val() !== undefined) {
                        value = $input.val();
                    }

                    return key + '=' + value;
                }).join('&');
            }

            var formValues = this.source.data;
            var redirectUrl = window.emailtester.previewUrl;
            redirectUrl += '?' + convertObjectToQueryString(formValues);
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
