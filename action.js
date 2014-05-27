function closePopup()
{
    jQuery(function($){
        $.modal().close();
    })
}
function sendCart()
{
    url = 'http://mrozk.myinsales.ru/cart_items.json';
    $.ajax({
        dataType: "json",
        url: url,
        async: false,
        success: function(data)
        {
            //console.log(data);
            var str =JSON.stringify(data);
            //alert(typeof JSON.stringify(data) );

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
        }
    });
}
function DDeliveryStart(){
    sendCart();
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

    DDelivery.delivery('ddelivery', 'http://insales.ddelivery.ru/sdk/', params, callback);
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