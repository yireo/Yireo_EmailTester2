define([
    'underscore'
], function (_) {
    'use strict';

    var mixin = {
        'preview': '#preview',
        'send': '#send'
    };

    return function (target) {
        return _.extend(target, mixin);
    };
});
