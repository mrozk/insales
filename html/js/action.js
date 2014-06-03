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
            var variant_id = $('.id_dd').parent().parent().find('.radio_button').val();
            alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
            CheckoutDelivery.find( variant_id ).toExternal().setPrice(data.clientPrice);
            closePopup();
        }
    };
    product_str = sendCart();

    //alert(ddelivery_insales._id);
    DDelivery.delivery('ddelivery', ddelivery_insales.url + 'sdk/?iframe=1&pr=' + product_str + '&insales_id=' + ddelivery_insales._id, params, callback);
}
$(function(){
    $(document).ready(function(){
        var variant_id = $('.id_dd').parent().parent().find('.radio_button').val();
        //alert(variant_id);
        $('#order_delivery_variant_id_' + variant_id).parent().next().append("<a id=\"startDD\" " +
                " href=\"javascript:void(0);\">Выбрать способ доставки</a>" +
                "<div class=\"modal\" id=\"test-modal\" style=\"display: none\"><div id=\"ddelivery\"></div></div>");

        $('#startDD').on('click', function(){
            DDeliveryStart();
        });


    });
});