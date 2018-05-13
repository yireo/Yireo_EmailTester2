define([
        'Yireo_EmailTester2/js/form/element/id-preview'
    ], function (IdPreviewField) {

        return IdPreviewField.extend({
            defaults: {
                idPreviewUrl: window.emailtester.productIdAjaxUrl,
                imports: {
                    searchValue: 'emailtester_form.emailtester_form.product_fieldset.product_search:value'
                }
            }
        });
    }
);