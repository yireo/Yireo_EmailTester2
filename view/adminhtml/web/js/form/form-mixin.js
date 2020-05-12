define([
    'Magento_Ui/js/form/adapter',
    'mage/url'
], function (adapter, url) {
    'use strict';

    var replaceUrl = function(url, replace) {
        return url.replace('emailtester/index/index', 'emailtester/index/' + replace);
    };

    var mixin = {
        initAdapter: function () {
            this._super();
            adapter.on({
                'preview': this.submitToPreview.bind(this, true, {}),
                'send': this.submitToSend.bind(this, true, {})
            }, this.selectorPrefix, this.eventPrefix);

            return this;
        },
        submitToPreview: function (redirect, data) {
            function convertObjectToQueryString(object)
            {
                return Object.keys(object).map(function(key) {
                    if (!object[key]) {
                        return;
                    }
                    return key + '=' + object[key];
                }).join('&');
            }

            var formValues = this.source.data;
            var redirectUrl = window.emailtester.previewUrl;
            redirectUrl += '?' + convertObjectToQueryString(formValues);
            window.open(redirectUrl,'_blank');
        },
        submitToSend: function (redirect, data) {
            this.source.client.urls.save = replaceUrl(this.source.submit_url, 'send');
            return this.save(redirect, data);
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
