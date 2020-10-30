define([
    'underscore'
], function (_) {
    'use strict';

    var mixin = {
        'emailtester-preview': '#preview',
        'emailtester-send': '#send'
    };

    return function (target) {
        return _.extend(target, mixin);
    };
});
