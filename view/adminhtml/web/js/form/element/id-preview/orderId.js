define([
        'Yireo_EmailTester2/js/form/element/id-preview'
    ], function (IdPreviewField) {
        'use strict';

        return IdPreviewField.extend({
            defaults: {
                idPreviewUrl: window.yireo_react.orderIdAjaxUrl,
                imports: {
                    searchValue: 'emailtester_form.emailtester_form.order_fieldset.order_search:value'
                }
            }
        });
    }
);
