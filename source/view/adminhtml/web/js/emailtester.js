jQuery(function() {

    jQuery("#customer_select").change(function() {
        jQuery('#customer_search').val('');
        jQuery('#customer_id').val(jQuery('#customer_select').val());
    });

    jQuery("#customer_search").autocomplete({
        source: function (request, response) {
            $.post(emailtester_customer_ajax_url, request, response);
        },
        minLength: 1,
        select: function( event, ui ) {
            jQuery('#customer_id').val(ui.item.id);
            jQuery('#customer_select').val('');
        }
    });

    jQuery("#order_select").change(function() {
        jQuery('#order_search').val('');
        jQuery('#order_id').val(jQuery('#order_select').val());
    });

    jQuery("#order_search").autocomplete({
        source: emailtester_order_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            jQuery('#order_id').val(ui.item.id);
            jQuery('#order_select').val('');
        }
    });

    jQuery("#product_select").change(function() {
        jQuery('#product_search').val('');
        jQuery('#product_id').val(jQuery('#product_select').val());
    });

    jQuery("#product_search").autocomplete({
        source: emailtester_product_ajax_url,
        minLength: 1,
        select: function( event, ui ) {
            jQuery('#product_id').val(ui.item.id);
            jQuery('#product_select').val('');
        }
    });
});
