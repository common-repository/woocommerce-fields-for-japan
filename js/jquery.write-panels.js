jQuery(document).ready(function($) {
    $('button.load_customer_billing').click(function(){
        var answer = confirm(wcbcf_writepanel_params.load_message);
        if (answer) {

            // Get user ID to load data for
            var user_id = $('#customer_user').val();

            if (!user_id) {
                alert(woocommerce_writepanel_params.no_customer_selected);
                return false;
            }

            var data = {
                user_id:            user_id,
                type_to_load:       'billing',
                action:             'woocommerce_get_customer_details',
                security:           woocommerce_writepanel_params.get_customer_details_nonce
            };

            $(this).closest('.edit_address').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_writepanel_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

            $.ajax({
                url: woocommerce_writepanel_params.ajax_url,
                data: data,
                type: 'POST',
                success: function( response ) {
                    var info = response;

                    if (info) {
                        $('input#_billing_cellphone').val( info.billing_cellphone );
                    }

                    $('.edit_address').unblock();
                }
            });
        }
        return false;
    });


    $('button.load_customer_shipping').click(function(){
        var answer = confirm(wcbcf_writepanel_params.load_message);
        if (answer) {

            // Get user ID to load data for
            var user_id = $('#customer_user').val();

            if (!user_id) {
                alert(woocommerce_writepanel_params.no_customer_selected);
                return false;
            }

            var data = {
                user_id:            user_id,
                type_to_load:       'shipping',
                action:             'woocommerce_get_customer_details',
                security:           woocommerce_writepanel_params.get_customer_details_nonce
            };

            $(this).closest('.edit_address').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_writepanel_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

            $.ajax({
                url: woocommerce_writepanel_params.ajax_url,
                data: data,
                type: 'POST',
                success: function( response ) {
                    var info = response;

                    if (info) {
                    }

                    $('.edit_address').unblock();
                }
            });
        }
        return false;
    });

    $('button.billing-same-as-shipping').click(function(){
        var answer = confirm(wcbcf_writepanel_params.copy_message);
        if (answer) {
        }

        return false;
    });

});
