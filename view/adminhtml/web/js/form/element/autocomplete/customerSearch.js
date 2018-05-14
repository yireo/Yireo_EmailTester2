define([
    'Yireo_EmailTester2/js/form/element/autocomplete'
    ], function (Autocomplete) {
        'use strict';

        return Autocomplete.extend({
            defaults: {
                autocompleteUrl: window.emailtester.customerSearchAjaxUrl
            }
        });
    }
);