define([
        'Magento_Ui/js/form/element/abstract',
        'knockout',
        'jquery'
    ], function (Element, ko, $) {
        'use strict';

        return Element.extend({
            defaults: {
                idPreviewUrl: '',
                valueUpdate: 'keyup',
                elementTmpl: 'Yireo_EmailTester2/form/element/id-preview.html'
            },
            initialize: function () {
                this._super();

                this.disabled(true);
                this.searchValue = ko.observable();
                this.previewValue = ko.observable();

                this.loadPreviewData = ko.computed(function () {
                    var self = this;
                    var ajaxUrl = this.idPreviewUrl + '?id=' + this.searchValue();

                    $.get(ajaxUrl, function (data) {
                        self.previewValue(data.label);
                        self.value(data.id);
                    }).fail(function () {
                        self.previewValue('Invalid entry');
                    });

                    return this.previewValue();
                }, this);
            }
        });
    }
);