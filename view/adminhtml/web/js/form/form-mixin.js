define([
    'Magento_Ui/js/form/adapter'
], function (adapter) {
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
            this.source.client.urls.save = replaceUrl(this.source.submit_url, 'preview');
            return this.save(redirect, data);
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
