/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

define([
    "jquery",
    "jquery/ui",
    "domReady!"
], function ($) {

    "use strict";
    var EmailTester = (function () {

        return {
            customer_ajax_url: '',
            order_ajax_url: '',
            product_ajax_url: '',

            setCustomerAjaxUrl: function (url) {
                this.customer_ajax_url = url + '?form_key' + window.FORM_KEY;
            },

            setOrderAjaxUrl: function (url) {
                this.order_ajax_url = url + '?form_key' + window.FORM_KEY;
            },

            setProductAjaxUrl: function (url) {
                this.product_ajax_url = url + '?form_key' + window.FORM_KEY;
            },

            runCustomerSelect: function () {
                $("#customer_select").change(function () {
                    $('input[name=customer_search]').val('');
                    $('input[name=customer_id]').val($('#customer_select').val());
                });
            },

            runCustomerSearch: function () {
                $("input[name=customer_search]").autocomplete({
                    source: function (request, response) {
                        $.post(this.customer_ajax_url, request, response);
                    },
                    minLength: 1,
                    select: function (event, ui) {
                        $('input[name=customer_id]').val(ui.item.value);
                        //$('#customer_search').attr('value', ui.item.label);
                        //$('#customer_select').val('');
                    }
                });
            },

            runProductSelect: function () {
                $("#product_select").change(function () {
                    $('input[name=product_search]').val('');
                    $('input[name=product_id]').val($('#product_select').val());
                });
            },

            runProductSearch: function () {
                $("input[name=product_search]").autocomplete({
                    source: this.product_ajax_url,
                    minLength: 1,
                    select: function (event, ui) {
                        $('input[name=product_id]').val(ui.item.value);
                        //$('#product_search').val(ui.item.label);
                        //$('#product_select').val('');
                    }
                });
            },

            runOrderSelect: function () {
                $("#order_select").change(function () {
                    $('input[name=order_search]').val('');
                    $('input[name=order_id]').val($('#order_select').val());
                });
            },

            runOrderSearch: function () {
                //console.log($("input[name=order_search]"));
                $("input[name=order_search]").autocomplete({
                    source: this.order_ajax_url,
                    minLength: 1,
                    select: function (event, ui) {
                        $('input[name=order_id]').val(ui.item.value);
                        //$('#order_search').val(ui.item.label);
                        //$('#order_select').val('');
                    }
                });
            },

            run: function (config) {
                this.setCustomerAjaxUrl(config.customer_ajax_url);
                this.setOrderAjaxUrl(config.order_ajax_url);
                this.setProductAjaxUrl(config.product_ajax_url);

                this.runCustomerSelect();
                this.runCustomerSearch();

                this.runProductSelect();
                this.runProductSearch();

                this.runOrderSelect();
                this.runOrderSearch();
            }
        };
    })();

    return function (config) {
        EmailTester.run(config);
    };
});
