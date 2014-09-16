var topWindow = parent;
//console.log( ddelivery_insales.delivery_id[1] );

while(topWindow != topWindow.parent) {
    topWindow = topWindow.parent;
}

if(typeof(topWindow.DDeliveryProtocolManager) == 'undefined')
    topWindow.DDeliveryProtocolManager = (function(){
        var th = {};
        var productList = {};
        var productIdsString = '';
        var orderPrice;
        th.token = '';
        if (!window.location.origin)
            window.location.origin = window.location.protocol+"//"+window.location.host;

        function getProductsInfoFromInsales(){
            $.ajax({
                dataType: "json",
                url: window.location.origin + '/products_by_id/' + productIdsString + '.json',
                async: false,
                success:function( data ){
                    if( data.status == "ok" ){
                        $.each( data.products, function( key,value ){
                            product =  productList[value.id];
                            product.product_field_values = value.product_field_values;
                        })
                    }
                }
            });
            console.log(productList);
        };
        function getProductsInfo(){
            $.ajax({
                dataType: "json",
                url: window.location.origin + '/cart_items.json',
                async: false,
                success: function(data){

                   if( data.items_count > 0 ){
                       $.each( data.order_lines,function( key,value ){
                           productIdsString += ( value.product_id + '(_)' + value.quantity + ',' );
                       });
                   }
                }
            });
        };

        th.setCartToInsales = function( key_on_server ){
            var url = ddelivery_insales.url + "sdk/putcart/";
            $.ajax({
                url: url,
                type: 'POST',
                jsonpCallback: 'jsonCallback',
                async: false,
                dataType: "jsonp",
                data: {
                    token: key_on_server,
                    data: JSON.stringify(productList)
                },
                success:function(){
                    alert('hello');
                }
            });
        };
        th.getProductList = function(){
            return JSON.stringify(productList);
        };
        th.getProductString = function(){
            return productIdsString;
        };
        th.checkServer = function(){
            alert('hello');
        };
        th.updatePriceAndSend = function( key_on_server ){

            getProductsInfo();
            //getProductsInfoFromInsales();
            th.token = key_on_server;
            // setCartToInsales( th.token );
        };
        return th;
})();
var DDeliveryProtocolManager = topWindow.DDeliveryProtocolManager;

function updatePriceAndSend( key_on_server ){
    DDeliveryProtocolManager.updatePriceAndSend( key_on_server );
}
function enableDDButton(){
    $('.startDD').removeAttr('disabled');
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
        th.typeOfWindow = null;
        th.openPopup = function(){
            showPrompt();
            document.getElementById('ddelivery_popup').innerHTML = '';
            var callback = {
                close: function(){
                    hideCover();
                    document.getElementById('ddelivery_container').style.display = 'none';
                },
                change: function(data) {

                    fillFeields(data);
                    $( '.moto_moto').empty();


                    var variant_id;
                    var activeBtn;
                    if(  ddelivery_insales.delivery_id.length == 2  ){
                        if( DDeliveryIntegration.typeOfWindow == 'onlyMap' ){
                            variant_id = ddelivery_insales.delivery_id[0];
                            console.log('onlyMap');
                            activeBtn = 'dd_start1';
                        }else if( DDeliveryIntegration.typeOfWindow == 'onlyCourier' ){
                            console.log('onlyCourier');
                            variant_id = ddelivery_insales.delivery_id[1];
                            activeBtn = 'dd_start2';
                        }
                    }else{
                        console.log('onlyBoth');
                        variant_id = ddelivery_insales.delivery_id[0];
                        activeBtn = 'dd_start1';
                    }

                    $( '#' + activeBtn).parent().append( '<div class="moto_moto" style="' +
                        'margin-top: 0px; color:#E98B73" >' + data.comment + '</div>' );

                    //var variant_id = ddelivery_insales.delivery_id;
                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field2_id, value: ddelivery_insales._id}] );
                    //alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);

                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field_id, value: data.orderId }] );
                    CheckoutDelivery.find( variant_id ).toExternal().setPrice(data.clientPrice);
                    $('.dd_last_check').val(data.orderId);
                    $('#price_' + variant_id).css('display','block');
                    status = data.comment;

                    hideCover();
                    document.getElementById('ddelivery_container').style.display = 'none';

                    $('#shipping_address_city').attr('disabled','disabled');
                    $('#shipping_address_zip').attr('disabled','disabled');
                }
            };
            order_form = $('#order_form').serializeArray();

            var params =  {};
            if( DDeliveryIntegration.typeOfWindow != null ){
                params.type_of_window = DDeliveryIntegration.typeOfWindow;
            }

            params.client_name = $('#client_name').val();
            params.client_phone = $('#client_phone').val();
            parametrs = $.param(params);
            order_form = $.param(order_form);

            url = ddelivery_insales.url + "sdk/?token=" + DDeliveryProtocolManager.token + "&items=" + DDeliveryProtocolManager.getProductString()
                  + "&" + parametrs + "&" + order_form ;

            DDelivery.delivery('ddelivery_popup', url, {}, callback);

        };
        var style = document.createElement('STYLE');
        style.innerHTML = // Скрываем ненужную кнопку
            ' #delivery_info_ddelivery_all a{display: none;} ' +
                ' #ddelivery_popup { display: inline-block; position:relative; vertical-align: middle; margin: 10px auto; width: 1000px; height: 650px;} ' +
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

        $(".button" ).on('click',function(){
            var activeBtn = 0;
            $('input[name="order[delivery_variant_id]"]').each(function(){
                if( $(this).attr('checked') == 'checked' ){
                    activeBtn = $(this).val();
                }
            });

            if( ddelivery_insales.delivery_id.indexOf(parseInt(activeBtn)) != -1){
                if( $('.dd_last_check').val() != '' ){
                    $('#order_form').submit();
                    return true;
                }else{
                    alert('Уточните выбор текущего способа доставки доставки ');
                    return false;
                }
            }
        });


        var buttonCode1 = '<div class=\"dd_asset_conteiner\" style=\"position: relative\">' +
                            '<input type=\"hidden\" class=\"dd_last_check\" value=\"\">' +
                            '<button id="dd_start1" disabled="disabled" onclick=\"return false\" class=\"startDD button\" style=\"max-height:18px;font:12px Tahoma,sans-serif; padding:  2px 9px;display:block;position: absolute;top: -32px; left:225px;min-width: 150px\" ' +
                            ' href=\"javascript:void(0);\" >Выбрать</button></div>';

        var buttonCode2 =    '<div class=\"dd_asset_conteiner\" style=\"position: relative\">' +
                             '<input type=\"hidden\" class=\"dd_last_check\" value=\"\">' +
                             '<button id="dd_start2" disabled="disabled" onclick=\"return false\" class=\"startDD button\" style=\"max-height:18px;font:12px Tahoma,sans-serif; padding:  2px 9px;display:block;position: absolute;top: -32px; left:225px;min-width: 150px\" ' +
                             ' href=\"javascript:void(0);\" >Выбрать</button></div>';



        $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id[0]).parent().next().append(buttonCode1 );

        if( ddelivery_insales.delivery_id.length == 2 ){
            $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id[1]).parent().next().append(buttonCode2);
        }

        // Спрятать плохие поля
        $('.delivery_variants .radio_button').on('change',function(){
            if( ddelivery_insales.delivery_id.indexOf(  parseInt( $(this).val())) != -1 ){
                $('#shipping_address_city').attr('disabled','disabled');
                $('#shipping_address_zip').attr('disabled','disabled');
            }else{
                $('#shipping_address_city').removeAttr('disabled');
                $('#shipping_address_zip').removeAttr('disabled');
            }
        });
        // Клик по кнопке
        $('.startDD').on('click', function(){
            var radio;
            if(  ddelivery_insales.delivery_id.length == 2  ){
                if( $(this).attr('id') == 'dd_start1' ){
                    DDeliveryIntegration.typeOfWindow = 'onlyMap';
                    radio = ddelivery_insales.delivery_id[0];
                }else if($(this).attr('id') == 'dd_start2'){
                    DDeliveryIntegration.typeOfWindow = 'onlyCourier';
                    radio = ddelivery_insales.delivery_id[1];
                }
            }else{
                radio = ddelivery_insales.delivery_id[0];
            }
            $('#order_delivery_variant_id_' + radio).click();
            DDeliveryIntegration.openPopup();

        });


    });
});

// IE 7 not support Array.indexOf
if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement, fromIndex) {
        if (this === undefined || this === null) {
            throw new TypeError('"this" is null or not defined');
        }

        var length = this.length >>> 0; // Hack to convert object.length to a UInt32

        fromIndex = +fromIndex || 0;

        if (Math.abs(fromIndex) === Infinity) {
            fromIndex = 0;
        }

        if (fromIndex < 0) {
            fromIndex += length;
            if (fromIndex < 0) {
                fromIndex = 0;
            }
        }

        for (; fromIndex < length; fromIndex++) {
            if (this[fromIndex] === searchElement) {
                return fromIndex;
            }
        }

        return -1;
    };
}



