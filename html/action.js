var topWindow = parent;

while(topWindow != topWindow.parent) {
    topWindow = topWindow.parent;
}

if(typeof(topWindow.DDeliveryIntegration) == 'undefined')
    topWindow.DDeliveryIntegration = (function(){
        var th = {};
        var status = 'Выберите условия доставки';
        th.getStatus = function(){
            return status;
        };

        function hideCover() {
            document.body.removeChild(document.getElementById('ddelivery_cover'));
        }

        function showPrompt() {
            var cover = document.createElement('div');
            cover.id = 'ddelivery_cover';
            document.body.appendChild(cover);
            document.getElementById('ddelivery_container').style.display = 'block';
        }

        function sendCart()
        {
            var product_list ='';
            if (!window.location.origin)
                window.location.origin = window.location.protocol+"//"+window.location.host;
            $.ajax({
                dataType: "json",
                url: window.location.origin + '/cart_items.json',
                async: false,
                success: function(data)
                {
                    //console.log(data);
                    if( data.order_lines.length > 0 )
                    {
                        for(var i = 0; i < data.order_lines.length; i++)
                        {
                            product_list +=  ( data.order_lines[i].product_id + '_' + data.order_lines[i].quantity + ',' );

                        }
                        product_list += '-' + ORDER.total_price;
                    }
                }
            });
            //console.log(product_list);
            return product_list;
        }
        function fillFeields(data)
        {
            $( '#client_name').val(data.userInfo.firstName);
            $( '#client_phone').val(data.userInfo.toPhone);
            //typeof(topWindow.DDeliveryIntegration) == 'undefined'
            //$( '#shipping_address_address').val(data.comment);
            if( data.type == "2" ){
                $('#shipping_address_field_' + data.house).val(data.userInfo.toHouse);
                $('#shipping_address_field_' + data.street).val(data.userInfo.toStreet);
                $('#shipping_address_field_' + data.flat).val(data.userInfo.toFlat);
                $('#shipping_address_field_' + data.corp).val(data.userInfo.toHousing);
            }
        }
        th.openPopup = function(){
            //CheckoutDelivery.find( ddelivery_insales.delivery_id ).toExternal().setPrice(200);
            showPrompt();
            document.getElementById('ddelivery_popup').innerHTML = '';
            var params = {
                formData: {}
            };

            order_form = $('#order_form').serializeArray();
            var callback = {
                close: function(){
                    hideCover();
                    document.getElementById('ddelivery_container').style.display = 'none';
                },
                change: function(data) {

                    fillFeields(data);
                    $( '.moto_moto').empty();
                    $( '.dd_asset_conteiner').append( '<div class="moto_moto" style="position: absolute;' +
                        'margin-top: 10px; color:#E98B73" >' + data.comment + '</div>' );
                    var variant_id = ddelivery_insales.delivery_id;
                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field2_id, value: ddelivery_insales._id}] );
                    //alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
                    console.log(variant_id);
                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field_id, value: data.orderId }] );
                    CheckoutDelivery.find( variant_id ).toExternal().setPrice(data.clientPrice);
                    $('.dd_last_check').val(data.orderId);
                    $('#price_' + variant_id).css('display','block');
                    status = data.comment;

                    hideCover();
                    document.getElementById('ddelivery_container').style.display = 'none';
                }
            };
            product_str = sendCart();
            params.iframe = 1;
            params.pr = product_str;
            params.insales_id = ddelivery_insales._id;
            // params.address = $('#shipping_address_address').val();
            params.client_name = $('#client_name').val();
            params.client_phone = $('#client_phone').val();
            parametrs = $.param(params);
            order_form = $.param(order_form);
            url = ddelivery_insales.url + "sdk/?" + parametrs + '&' + order_form;
            $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).click();
            if ( $('.dd_last_check').val() != '' ){
                orderId2 = parseInt($('.dd_last_check').val());
            }else{
                orderId2 = 0;
            }
            DDelivery.delivery('ddelivery_popup', url, {orderId: orderId2}, callback);
            return void(0);
        };
        var style = document.createElement('STYLE');
        style.innerHTML = // Скрываем ненужную кнопку
            ' #delivery_info_ddelivery_all a{display: none;} ' +
                ' #ddelivery_popup { display: inline-block; vertical-align: middle; margin: 10px auto; width: 1000px; height: 650px;} ' +
                ' #ddelivery_container { position: fixed; top: 0; left: 0; z-index: 9999;display: none; width: 100%; height: 100%; text-align: center;  } ' +
                ' #ddelivery_container:before { display: inline-block; height: 100%; content: \'\'; vertical-align: middle;} ' +
                ' #ddelivery_cover {  position: fixed; top: 0; left: 0; z-index: 9000; width: 100%; height: 100%; background-color: #000; background: rgba(0, 0, 0, 0.5); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = #7F000000, endColorstr = #7F000000); } ';
        var body = document.getElementsByTagName('body')[0];
        body.appendChild(style);
        var div = document.createElement('div');
        div.innerHTML = '<div id="ddelivery_popup"></div>';
        div.id = 'ddelivery_container';
        body.appendChild(div);

        return th;
    })();
var DDeliveryIntegration = topWindow.DDeliveryIntegration;

$(function(){
    $(document).ready(function(){
        var variant_id = ddelivery_insales.delivery_id;


        $('#create_order').on('click',function(){
            checked = $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).attr("checked");
            if( checked == 'checked' )
            {
                if( $('.dd_last_check').val() != '' )
                {
                    $('#order_form').submit();
                    return true;
                }
                else
                {
                    alert('Выберите точку доставки DDelivery');
                    return false;
                }
            }
            else
            {
                return true;
            }
            return false;
        });
        $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).parent().next().append('<div class=\"dd_asset_conteiner\" style=\"position: relative\">' +
            '<input type=\"hidden\" class=\"dd_last_check\" value=\"\">' +
            '<button onclick=\"return false\" class=\"button\" style=\"max-height:18px;font:12px Tahoma,sans-serif; padding:  2px 9px;display:block;position: absolute;top: -32px; left:65px;min-width: 190px\" id=\"startDD\" ' +
            ' href=\"javascript:void(0);\" >Выбрать способ доставки</button>' );

        $('#startDD').on('click', function(){
            DDeliveryIntegration.openPopup();
        });


    });
});



/*
var topWindow = parent;

while(topWindow != topWindow.parent) {
    topWindow = topWindow.parent;
}
topWindow.inSettings = (function( data ){

    if( data.result == 'success' ){
        topWindow.DDeliveryIntegration.settings = data.request;
    }else{
        topWindow.DDeliveryIntegration.settings = null;
    }
});
if(typeof(topWindow.DDeliveryIntegration) == 'undefined')
    topWindow.DDeliveryIntegration = (function(){
        var th = {};

        var settings = {};

        var status = 'Выберите условия доставки';
        th.getStatus = function(){
            return status;
        };

        th.renderSettings = function(){
            $.ajax({
                dataType: "jsonp",
                jsonp: "inSettings",
                url: ddelivery_insales.url + "sdk/settings/?client=" + ddelivery_insales._id,
                async: false
            });
        }

        function hideCover() {
            document.body.removeChild(document.getElementById('ddelivery_cover'));
        }

        function showPrompt() {
            var cover = document.createElement('div');
            cover.id = 'ddelivery_cover';
            document.body.appendChild(cover);
            document.getElementById('ddelivery_container').style.display = 'block';
        }

        function sendCart()
        {
            var product_list = {};
            if (!window.location.origin)
                window.location.origin = window.location.protocol+"//"+window.location.host;
            $.ajax({
                dataType: "json",
                url: window.location.origin + '/cart_items.json',
                async: false,
                success: function(data)
                {
                    //console.log(data);
                    if( data.order_lines.length > 0 )
                    {
                        for(var i = 0; i < data.order_lines.length; i++)
                        {
                            product_list[data.order_lines[i].product_id]= data.order_lines[i].quantity; // ( data.order_lines[i].product_id + '_' + data.order_lines[i].quantity + ',' );
                        }
                        product_list["price"] = ORDER.total_price;
                    }
                }
            });
            //console.log(product_list);
            return product_list;
        }
        function fillFeields(data)
        {
            $( '#client_name').val(data.userInfo.firstName);
            $( '#client_phone').val(data.userInfo.toPhone);
            //typeof(topWindow.DDeliveryIntegration) == 'undefined'
            //$( '#shipping_address_address').val(data.comment);

            $('#shipping_address_field_' + data.house).val(data.userInfo.toHouse);
            $('#shipping_address_field_' + data.street).val(data.userInfo.toStreet);
            $('#shipping_address_field_' + data.flat).val(data.userInfo.toFlat);
            $('#shipping_address_field_' + data.corp).val(data.userInfo.toHousing);
        }
        th.openPopup = function(){
            //showPrompt();
            //th.renderSettings();
            //console.log(topWindow.DDeliveryIntegration.settings);
            //renderSettings();
            */
            /*
            console.log( topWindow.DDeliveryIntegration.settings );



            $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).click();
            showPrompt();
            document.getElementById('ddelivery_popup').innerHTML = '';
            var params = {
                formData: {}
            };

            order_form = $('#order_form').serializeArray();
            var callback = {
                close: function(){
                    hideCover();
                    document.getElementById('ddelivery_container').style.display = 'none';
                },
                change: function(data) {

                    fillFeields(data);
                    $( '.moto_moto').empty();
                    $( '.dd_asset_conteiner').append( '<div class="moto_moto" style="position: absolute;' +
                        'margin-top: 10px; color:#E98B73" >' + data.comment + '</div>' );
                    var variant_id = ddelivery_insales.delivery_id;
                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field2_id, value: ddelivery_insales._id}] );
                    //alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field_id, value: data.orderId }] );
                    CheckoutDelivery.find( variant_id ).toExternal().setPrice(data.clientPrice);
                    $('.dd_last_check').val(data.orderId);
                    $('#price_' + variant_id).css('display','block');
                    status = data.comment;

                    hideCover();
                    document.getElementById('ddelivery_container').style.display = 'none';
                }
            };

            //console.log( product_str );
            params.iframe = 1;
            //params.pr = product_str;
            params.insales_id = ddelivery_insales._id;
            // params.address = $('#shipping_address_address').val();
            params.client_name = $('#client_name').val();
            params.client_phone = $('#client_phone').val();
            parametrs = $.param(params);
            order_form = $.param(order_form);
            url = ddelivery_insales.url + "sdk/?" + parametrs + '&' + order_form;
            $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).click();
            if ( $('.dd_last_check').val() != '' ){
                orderId2 = parseInt($('.dd_last_check').val());
            }else{
                orderId2 = 0;
            }
            DDelivery.delivery('ddelivery_popup', url, {orderId: orderId2}, callback);
            return void(0);
            */
/*
        };
        var style = document.createElement('STYLE');
        style.innerHTML = // Скрываем ненужную кнопку
            " #delivery_info_ddelivery_all a{display: none;} " +
                " #ddelivery_popup { display: inline-block; vertical-align: middle; margin: 10px auto; width: 1000px; height: 650px;} " +
                " #ddelivery_container { position: fixed; top: 0; left: 0; z-index: 9999;display: none; width: 100%; height: 100%; text-align: center;  } " +
                " #ddelivery_container:before { display: inline-block; height: 100%; content: ''; vertical-align: middle;} " +
                " #ddelivery_cover {  position: fixed; top: 0; left: 0; z-index: 9000; width: 100%; height: 100%; background-color: #000; background: rgba(0, 0, 0, 0.5); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = #7F000000, endColorstr = #7F000000); } ";
        var body = document.getElementsByTagName('body')[0];
        body.appendChild(style);
        var div = document.createElement('div');
        div.innerHTML = '<div id="ddelivery_popup"></div>';
        div.id = 'ddelivery_container';
        body.appendChild(div);

        return th;
    })();
var DDeliveryIntegration = topWindow.DDeliveryIntegration;

$(function(){
    $(document).ready(function(){
        $('#create_order').on('click',function(){
            checked = $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).attr("checked");
            if( checked == 'checked' )
            {
                if( $('.dd_last_check').val() != '' )
                {
                    $('#order_form').submit();
                    return true;
                }
                else
                {
                    alert('Выберите точку доставки DDelivery');
                    return false;
                }
            }
            else
            {
                return true;
            }
            return false;
        });
        $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).parent().next().append("<div class='dd_asset_conteiner' style='position: relative'>" +
            "<input type='hidden' class='dd_last_check' value=''>" +
            "<button onclick=\"return false\" class=\"button\" style='max-height:18px;font:12px Tahoma,sans-serif; padding:  2px 9px;display:block;position: absolute;top: -32px; left:65px;min-width: 190px' id=\"startDD\" " +
            " href=\"javascript:void(0);\" >Выбрать способ доставки</button>" );

        $('#startDD').on('click', function(){
            DDeliveryIntegration.openPopup();
        });


    });
});
*/