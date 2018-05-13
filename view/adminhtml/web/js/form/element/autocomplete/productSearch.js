define([
        'Yireo_EmailTester2/js/form/element/autocomplete'
    ], function (Autocomplete) {
        return Autocomplete.extend({
            defaults: {
                autocompleteUrl: window.emailtester.productSearchAjaxUrl
            }
        });
    }
);