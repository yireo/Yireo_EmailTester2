define([
    'underscore'
], function (_) {
    'use strict';

    var mixin = {
        'emailtester-preview': '#emailtesterPreview',
        'emailtester-send': '#emailtesterSend'
    };

    return function (target) {
        return _.extend(target, mixin);
    };
});
