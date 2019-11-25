/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

var config = {
    map: {
        '*': {
            'Magento_Ui/js/form/adapter': 'Yireo_EmailTester2/js/form/adapter'
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/form/form': {
                'Yireo_EmailTester2/js/form/form-mixin': true
            },
            'Yireo_EmailTester2/js/form/adapter/button-provider': {
                'Yireo_EmailTester2/js/form/adapter/button-mixin': true
            }
        }
    }
};