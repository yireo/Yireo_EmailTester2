define([
        'Magento_Ui/js/form/element/abstract',
        'knockout',
        'jquery'
    ], function (Element, ko, $) {

        'use strict';

        ko.bindingHandlers.autocomplete = {
            init: function(element, valueAccessor) {
                var options = ko.utils.unwrapObservable(valueAccessor());
                $(element).autocomplete(options);
                ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
                    $(element).autocomplete("destroy");
                });
            }
        };

        return Element.extend({
            defaults: {
                valueUpdate: 'keyup',
                elementTmpl: 'Yireo_EmailTester2/form/element/autocomplete.html',
                autocompleteUrl: '',
                autocompleteOptions: {
                    source: '',
                    minLength: 1
                }
            },
            initialize: function() {
                this._super();
                this.autocompleteOptions.source = this.autocompleteUrl;
            }
        });
    }
);