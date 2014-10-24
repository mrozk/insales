/**
 * Created by mrozk on 9/30/14.
 */

var DDeliveryTokenManager = (function(){

    var token;

    var order;

    var objectConfig;

    var productIdsString = "";

    var isOnePageType = 0;

    var filterPayment = [];

    var method = 0;

    function setCookie(cname, cvalue, exdays) {

        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
    }
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
        }
        return "";

    }


    function getProductString( orderInfo ){
        if( productIdsString == "" ){
            if(  orderInfo.order_lines.length > 0 ){
                $.each( orderInfo.order_lines, function( key,value ){
                    productIdsString += ( value.product_id + '(_)' + value.quantity + ',' );
                });
            }
        }
        return productIdsString;
    }

    function checkCartEquals(){
        var cookieCart = getCookie("dd_cookie_cart");
        if( cookieCart == getProductString(window.ORDER) ){
            return true;
        }
        return false;
    }

    function initInsalesToken(){

        var productIds = getProductString(window.ORDER);

        $.ajax({
            url: objectConfig.ddBaseUrl,
            type: 'GET',
            jsonpCallback: 'jsonCallback',
            async: false,
            dataType: "jsonp",
            data: {
                product_id: productIds
            },
            success:function( data ){

                setCookie('ddelivery_token', data, 1);
                setCookie('dd_cookie_cart', productIds, 1);

                console.log('Token when init ' + getCookie('ddelivery_token'));
                token = data;
                //alert('generated token' + token);
                console.log(token);
            }
        });
    }
    function cleanMethods( ids ){
        if( ids.length > 0 ){
            $.each(ids, function(item){
                disable_element( $('#order_payment_gateway_id_' + ids[item]));
            });
        }
    }
    function updatePriceAndPoint(){
        var external_params = {tokenBody:token};
        return $.ajax({
            url: ddConfig.ddGetPoint,
            type: 'GET',
            jsonpCallback: 'jsonCallback2',
            dataType: 'jsonp',
            data: external_params,
            success: function(data) {
                console.log(data);
                console.log(window.ORDER)
                //console.log(data);
                /*
                console.log(data);
                console.log(window.ORDER);
                */
                if (data.success == "true" && (window.ORDER.delivery_price == data.userInfo.price)) {
                    filterPayment = data.userInfo.filter;
                    cleanMethods(filterPayment);
                }else{
                    $('#payment :radio').each(function() {
                        return enable_element(this);
                    });
                }

            }});

    }

    function checkAsyncCart( tokenBody ){
        var returnResult = false;
        var tokenBody = tokenBody;
        $.ajax({
            url: objectConfig.ddBaseUrl,
            type: 'GET',
            jsonpCallback: 'jsonCallback',
            cache: false,
            dataType: "jsonp",
            data: {
                tokenBody: tokenBody
            },
            async: false,
            success:function( data ){

                if( data != 1 ){

                    initInsalesToken();
                }
            }
        });

    }

    return{
        init: function(object_config){

            token = getCookie("ddelivery_token");
            console.log('Token when init ' + token);

            var str = new String(window.location);
            if( str.indexOf('orders') > 0 ){
                setCookie('ddelivery_token', '', 0);
                setCookie('dd_cookie_cart', '', 0);
            }
            //alert( str.indexOf('orders') );
            if( typeof (window.ORDER) != 'undefined'){
                var th = this;

                if( (document.getElementById( 'create_order') != null) && document.getElementsByName('order[delivery_variant_id]').length > 0){
                    isOnePageType = true;
                }else{
                    isOnePageType = false;
                }

                objectConfig = object_config;

                if( typeof (token) == 'undefined' || !checkCartEquals() ){
                    initInsalesToken();
                }else{
                    checkAsyncCart(token)
                }
                var create_order = document.getElementById('create_order');

                if( ( create_order !== null ) && ( !isOnePageType ) ){
                    console.log('not isOnePageType');
                    updatePriceAndPoint();
                }
            }else{
                console.log('doNothing');
            }

        },
        cleanMethods: function( ids ){
            cleanMethods(ids);
        },

        getIsOnePageType: function(){
            return isOnePageType;
        },
        getMethod: function(){
            return method;
        },
        setMethod: function(methodID){
            method = parseInt(methodID);
        },
        setFilterPayment:function(filter){
            filterPayment = filter;
        },
        getFilterPayment:function(){
            return filterPayment;
        },
        getCookie: function(name){
            //var th = this;
            getCookie(name);
        },
        setCookie:function( name, value, options ){
            //var th = this;
            setCookie(name, value, options)
        },
        getToken: function(){
            return token;
        }

    }
})();
window.onload = function(){
    DDeliveryTokenManager.init(ddConfig);
}
if( typeof (disable_element) == 'undefined' ){
    window.disable_element = function(element) {
        return $(element).attr('disabled', 'disabled').attr('checked', false).parents('tr').addClass('not_available').find('.price label span').text('').attr('data-price', 0).end().find('div[id^="delivery_error_"]').hide();
    };
    window.enable_element = function(element) {
        return $(element).removeAttr('disabled').parents('tr').removeClass('not_available');
    };
}
/*
window.get_payments = function() {
    $('#payment_gateways :radio').each(function() {
        return disable_element(this);
    });
    return $.ajax({
        url: '/payment/for_order.json',
        type: 'PUT',
        dataType: 'json',
        data: $('#order_form').formSerialize(),
        timeout: 10000,
        success: function(data) {
            alert('getPaymentsResult');

            var input;
            window.payment_error_reported = false;
            window.payment_errors_counter = 0;
            $.each(data, function() {
                var input, price_string;
                price_string = parseFloat(this.price) ? (this.price > 0 ? '+' : '-') + ' ' + InSales.formatMoney(Math.abs(this.price)) : '';
                $("#summ_" + this.id).html(price_string).attr('data-price', this.price);
                if ((input = $("#order_payment_gateway_id_" + this.id)).is(':radio')) {
                    enable_element(input);
                    if (this.selected) {
                        return input.click();
                    }
                }
            });
            if ((input = $('#payment_gateways :radio:checked')).is(':disabled') || !input.length) {
                return $('#payment_gateways :radio:enabled').first().click();
            }
        },
        error: function(xhr, status, error_thrown) {
            if (status !== 'error' && status !== 'timeout') {
                return;
            }
            if (window.payment_errors_counter >= 6) {
                if (window.Rollbar && !window.payment_error_reported) {
                    Rollbar.error("Cant get payments: " + status, {
                        status: status,
                        error: error_thrown,
                        response_text: xhr.responseText,
                        headers: xhr.getAllResponseHeaders(),
                        data: $('#order_form').formSerialize(),
                        status_code: xhr.statusCode(),
                        status_text: xhr.statusText
                    });
                    window.payment_error_reported = true;
                }
                enable_deliveries_and_payments();
                select_first_deliveries_and_payments();
            }
            if (window.payment_errors_counter < 6) {
                window.debounced_payments();
            }
            return window.payment_errors_counter++;
        }
    });
};
*/