function closePopup()
{
    jQuery(function($){
        $.modal().close();
    })
}
function getHost()
{
    pathArray = window.location.href.split( '/' );
    protocol = pathArray[0];
    host = pathArray[2];
    url22 = ( protocol + '://' + host + '/' );
    return url22;
}
function sendCart()
{
    var product_list = '';
    /*
    pathArray = window.location.href.split( '/' );
    protocol = pathArray[0];
    host = pathArray[2];
    url22 = ( protocol + '://' + host + '/cart_items.json' );
    */
    $.ajax({
        dataType: "json",
        url: 'cart_items.json',
        async: false,
        success: function(data)
        {
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
    return product_list;
}

function fillFeields(data)
{
    $( '#client_name').val(data.userInfo.secondName + ' ' + data.userInfo.firstName);
    $( '#client_phone').val(data.userInfo.toPhone);
    $( '#shipping_address_address').val(data.comment);
}

function DDeliveryStart(){

    jQuery('#test-modal').modal().open();

    var params = {
        //orderId: 4 // Если у вас есть id заказа который изменяется, то укажите его в этом параметре
    };
    var callback = {
        close: function(){
            closePopup();
            //alert('Окно закрыто');
        },
        change: function(data)
        {
            fillFeields(data);
            $( '.dd_asset_conteiner').append( '<div style="position: absolute;margin-top: 10px; color:#E98B73" >' + data.comment + '</div>' );
            var variant_id = $('.id_dd').parent().parent().find('.radio_button').val();
            CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field2_id, value: ddelivery_insales._id}] );
            //alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
            CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field_id, value: data.orderId }] );
            CheckoutDelivery.find( variant_id ).toExternal().setPrice(data.clientPrice);
            $('.dd_last_check').val(data.orderId);
            closePopup();
        }
    };
    product_str = sendCart();

    //alert(ddelivery_insales._id);
    DDelivery.delivery('ddelivery', ddelivery_insales.url + 'sdk/?iframe=1&pr=' + product_str + '&insales_id=' + ddelivery_insales._id, params, callback);
}
$(function(){
    $(document).ready(function(){
        $('#create_order').on('click',function(){
            var variant_id = $('.id_dd').parent().parent().find('.radio_button').val();
            checked = $('.id_dd').parent().parent().find('.radio_button').attr("checked");
            if( checked == 'checked' )
            {
                if( $('.dd_last_check').val() != '' )
                {
                    //alert($('.dd_last_check').val());
                    return true;
                }
                else
                {
                    alert('Выберите точку доставки DDelivery');
                }
                //console.log(CheckoutDelivery.find( variant_id ).fieldsValues[0].value);
                    //CheckoutDelivery.find( variant_id ).fieldsValues[0].value
            }
            else
            {
                return true;
            }
            return false;
        });
        var variant_id = $('.id_dd').parent().parent().find('.radio_button').val();
        //alert( ddelivery_insales.field2_id );
        //alert( ddelivery_insales.field_id );
        //CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field2_id, value: ddelivery_insales._id}] );
        //CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field_id, value: '361' }] );

        /*
            CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: 1677899, value: 'test'}] );
            alert ( CheckoutDelivery.find( variant_id ).fieldsValues[0].value );
        */
        $('#order_delivery_variant_id_' + variant_id).parent().next().append("<div class='dd_asset_conteiner' style='position: relative'>" +
            "<input type='hidden' class='dd_last_check' value=''>" +
            "<a class=\"button\" style='display:block;position: absolute;top: -25px; left:120px;min-width: 190px' id=\"startDD\" " +
                " href=\"javascript:void(0);\">Выбрать способ доставки</a>" +
                "<div class=\"modal\" id=\"test-modal\" style=\"display: none\"><div id=\"ddelivery\"></div></div>");

        $('#startDD').on('click', function(){
            DDeliveryStart();
        });


    });
});