/**
 * Created by mrozk on 9/30/14.
 */

var DDeliveryTokenManager = (function(){

    var token;

    var order;

    var objectConfig;

    var productIdsString = "";


    function setCookie(name, value, options) {
        options = options || {};
        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires*1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }
        value = encodeURIComponent(value);
        var updatedCookie = name + "=" + value;

        for(var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }
        document.cookie = updatedCookie;
    }
    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
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
        if( cookieCart == getProductString(order) ){
            return true;
        }
        return false;
    }

    function initInsalesToken(){

        var productIds = getProductString(order);

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

                console.log(getCookie('ddelivery_token'));
                token = data;
                console.log(token);
            }
        });

    }

    function checkToken( tokenBody ){
        var result = false;
        var tokenBody = tokenBody;
        $.ajax({
            url: objectConfig.ddBaseUrl,
            type: 'GET',
            jsonpCallback: 'jsonCallback2',
            async: false,
            dataType: "jsonp",
            data: {
                tokenBody: tokenBody
            },
            success:function( data ){
                alert(data);
                console.log( data );
            }
        });
        return result;
    }

    return{
        init: function(object_config){
            if( typeof (window.ORDER) != 'undefined'){
                var th = this;
                objectConfig = object_config;
                order = window.ORDER;
                // setCookie("ddelivery_token", "xxxxx", {expires:1});
                // token = getCookie('ddelivery_token');
                //setCookie("ddelivery_token", "xxxxx", 1);
                //alert(getCookie("token"));
                token = getCookie("ddelivery_token");
                //console.log(document.cookie);
                //alert(token);

                if( typeof (token) != 'undefined'){

                    if( !checkToken(token) ){
                        console.log('Token has expired');
                    }

                    if( checkCartEquals() ){
                        console.log('checkCartEquals');
                    }else{
                        console.log('notCheckCartEquals');
                    }
                }else{
                    initInsalesToken();
                }
            }else{
                console.log('doNothing');
            }

        },
        checkToken: function(){

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


var ddConfig = {
    ddBaseUrl:"http://devinsales.ddelivery.ru/sdk/putcart/29/",
    sdkPath: "http://devinsales.ddelivery.ru/sdk/",
    wayId: [242743, 242744]
}


window.onload = function(){
    DDeliveryTokenManager.init(ddConfig);
}
/*
 window.onload = function(){
 //setCookie("ddelivery_token", "xxxxx", {expires:1});
 //alert(getCookie("token"));
 ddeliveryToken = getCookie("ddelivery_token");

 if( ddeliveryToken == undefined){
 alert('empty token');
 }else{
 alert('not empty token');
 }
 };
 */