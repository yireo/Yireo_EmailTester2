define([
        'Yireo_EmailTester2/js/form/element/id-preview'
    ], function (IdPreviewField) {
        'use strict';

        return IdPreviewField.extend({
            defaults: {
                idPreviewUrl: window.yireo_react.productIdAjaxUrl,
                imports: {
                    searchValue: 'emailtester_form.emailtester_form.product_fieldset.product_search:value'
                }
            }
        });
    }
);
