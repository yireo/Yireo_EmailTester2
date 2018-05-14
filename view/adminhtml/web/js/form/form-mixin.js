define([
    'Magento_Ui/js/form/adapter'
], function (adapter) {
    'use strict';

    var mixin = {
        initAdapter: function () {
            this._super();
            adapter.on({
                'preview': this.save.bind(this, true, {}),
                'send': this.save.bind(this, true, {})
            }, this.selectorPrefix, this.eventPrefix);

            return this;
        },
    };

    return function (target) {
        return target.extend(mixin);
    };
});
