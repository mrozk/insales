function closePopup()
{
    jQuery(function($){
        $.modal().close();
    })
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
                //alert(data.order_lines.length);
                for(var i = 0; i < data.order_lines.length; i++)
                {
                    //console.log( data.order_lines[i] );
                    product_list +=  ( data.order_lines[i].product_id + '_' + data.order_lines[i].quantity + ',' );

                }
                product_list += '-' + ORDER.total_price;
            }

           //http://mrozk.myinsales.ru/alert(typeof JSON.stringify(data) );
            /*
            $.ajax({
                data: { str : 'sdf' },
                type: 'POST',
                dataType: 'jsonp',
                async: false,
                jsonpCallback: 'jsonCallback',
                contentType: "application/json",
                url: "http://insales.ddelivery.ru/sdk/takecart/",
                success: function(zata)
                {

                    console.log(zata);
                }
            });
            */
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
        change: function(data) {
            CheckoutDelivery.find(220167).toExternal().setPrice(data.clientPrice);
            closePopup();
            alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
        }
    };
    product_str = sendCart();
    alert(product_str);
    DDelivery.delivery('ddelivery', 'http://insales.ddelivery.ru/sdk/?iframe=1&pr=' + product_str, params, callback);
}
$(function(){
    $(document).ready(function(){
        $('#order_delivery_variant_id_220167').parent().next().append("<a id=\"startDD\" " +
                " href=\"javascript:void(0);\">Выбрать способ доставки</a>" +
                "<div class=\"modal\" id=\"test-modal\" style=\"display: none\"><div id=\"ddelivery\"></div></div>");


        $('#startDD').on('click', function(){
            DDeliveryStart();
        });


    });
});