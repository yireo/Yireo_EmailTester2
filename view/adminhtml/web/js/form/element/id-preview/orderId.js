define([
        'Yireo_EmailTester2/js/form/element/id-preview'
    ], function (IdPreviewField) {

        return IdPreviewField.extend({
            defaults: {
                idPreviewUrl: window.emailtester.orderIdAjaxUrl,
                imports: {
                    searchValue: 'emailtester_form.emailtester_form.order_fieldset.order_search:value'
                }
            }
        });
    }
);