/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

define([
    "jquery",
    "jquery/ui"
], function ($, JUI) {

    "use strict";
    var EmailTester = (function () {

        var EmailTesterClass = {

            customer_ajax_url: '',

            order_ajax_url: '',

            product_ajax_url: '',

            setCustomerAjaxUrl: function (url) {
                EmailTesterClass.customer_ajax_url = url + '?form_key' + window.FORM_KEY;
            },

            setOrderAjaxUrl: function (url) {
                EmailTesterClass.order_ajax_url = url + '?form_key' + window.FORM_KEY;
            },

            setProductAjaxUrl: function (url) {
                EmailTesterClass.product_ajax_url = url + '?form_key' + window.FORM_KEY;
            },

            runCustomerSelect: function () {
                $("#customer_select").change(function () {
                    $('#customer_search').val('');
                    $('#customer_id').val($('#customer_select').val());
                });
            },

            runCustomerSearch: function () {
                $("#customer_search").autocomplete({
                    source: function (request, response) {
                        $.post(EmailTesterClass.customer_ajax_url, request, response);
                    },
                    minLength: 1,
                    select: function (event, ui) {
                        $('#customer_id').val(ui.item.value);
                        //$('#customer_search').attr('value', ui.item.label);
                        //$('#customer_select').val('');
                    }
                });
            },

            runOrderSelect: function () {
                $("#order_select").change(function () {
                    $('#order_search').val('');
                    $('#order_id').val($('#order_select').val());
                });
            },

            runOrderSearch: function () {
                $("#order_search").autocomplete({
                    source: EmailTesterClass.order_ajax_url,
                    minLength: 1,
                    select: function (event, ui) {
                        $('#order_id').val(ui.item.value);
                        //$('#order_search').val(ui.item.label);
                        //$('#order_select').val('');
                    }
                });
            },

            runProductSelect: function () {
                $("#product_select").change(function () {
                    $('#product_search').val('');
                    $('#product_id').val($('#product_select').val());
                });
            },

            runProductSearch: function () {
                $("#product_search").autocomplete({
                    source: EmailTesterClass.product_ajax_url,
                    minLength: 1,
                    select: function (event, ui) {
                        $('#product_id').val(ui.item.value);
                        //$('#product_search').val(ui.item.label);
                        //$('#product_select').val('');
                    }
                });
            },

            run: function (config) {

                EmailTesterClass.setCustomerAjaxUrl(config.customer_ajax_url);
                EmailTesterClass.setOrderAjaxUrl(config.order_ajax_url);
                EmailTesterClass.setProductAjaxUrl(config.product_ajax_url);

                EmailTesterClass.runCustomerSelect();
                EmailTesterClass.runCustomerSearch();
                EmailTesterClass.runOrderSelect();
                EmailTesterClass.runOrderSearch();
                EmailTesterClass.runProductSelect();
                EmailTesterClass.runProductSearch();
            }
        };

        return {
            run: EmailTesterClass.run
        };
    })();

    return function (config) {
        EmailTester.run(config);
    };
});
