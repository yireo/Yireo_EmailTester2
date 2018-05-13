define([
        'Yireo_EmailTester2/js/form/element/id-preview'
    ], function (IdPreviewField) {

        return IdPreviewField.extend({
            defaults: {
                idPreviewUrl: window.emailtester.customerIdAjaxUrl,
                imports: {
                    searchValue: 'emailtester_form.emailtester_form.customer_fieldset.customer_search:value'
                }
            }
        });
    }
);