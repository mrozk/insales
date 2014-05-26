function closePopup()
{
    jQuery(function($){
        $.modal().close();
    })
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
            alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
        }
    };

    DDelivery.delivery('ddelivery', 'http://phpshop.ddelivery.ru/phpshop/modules/ddelivery/class/example/ajax.php', params, callback);
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