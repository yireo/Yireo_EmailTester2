define([
        'Magento_Ui/js/form/element/abstract',
        'knockout',
        'jquery'
    ], function (Element, ko, $) {

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
                valueUpdate: 'blur',
                elementTmpl: 'Yireo_EmailTester2/form/element/autocomplete.html',
                autocompleteUrl: '',
                autocompleteTargetElement: '',
                autocompleteOptions: {
                    source: '',
                    minLength: 1
                }
            },
            initialize: function() {
                this._super();
                this.autocompleteOptions.source = this.autocompleteUrl;
                this.autocompleteOptions.select = this.onSelect;
                this.autocompleteOptions.select.autocompleteTargetElement = this.autocompleteTargetElement;
            },
            onSelect: function (event, ui) {
                console.log('Hello World: ' + this.autocompleteTargetElement);
                var $target = $(this.autocompleteTargetElement);
                $target.val(ui.item.value);
            }
        });
    }
);