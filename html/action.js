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
            var product_list = '';

            $.ajax({
                dataType: "json",
                url: 'cart_items.json',
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
            return product_list;
        }
        function fillFeields(data)
        {
            $( '#client_name').val(data.userInfo.secondName + ' ' + data.userInfo.firstName);
            $( '#client_phone').val(data.userInfo.toPhone);
            $( '#shipping_address_address').val(data.comment);
        }
        th.openPopup = function(){
            showPrompt();
            document.getElementById('ddelivery_popup').innerHTML = '';
            //jQuery('#ddelivery_popup').html('').modal().open();
            var params = {
                formData: {}
            };
            /*
            $($('#order_form').serializeArray()).each(function(){
                params.formData[this.name] = this.value;
            });
            */
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
                    var variant_id = $('.id_dd').parent().parent().find('.radio_button').val();
                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field2_id, value: ddelivery_insales._id}] );
                    //alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
                    CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field_id, value: data.orderId }] );
                    CheckoutDelivery.find( variant_id ).toExternal().setPrice(data.clientPrice);
                    $('.dd_last_check').val(data.orderId);

                    status = data.comment;
                    document.getElementById('ddelivery').getElementsByTagName('SPAN').innerHTML = data.comment;

                    hideCover();
                    document.getElementById('ddelivery_container').style.display = 'none';

                    //$('#ID_DELIVERY_ddelivery_all').click();
                }
            };
            //url_params = "?" + ( $.param(params) ) + "";
            product_str = sendCart();
            params.iframe = 1;
            params.pr = product_str;
            params.insales_id = ddelivery_insales._id;
            params.address = $('#shipping_address_address').val();
            parametrs = $.param(params);
            url = ddelivery_insales.url + "sdk/?" + parametrs;

            DDelivery.delivery('ddelivery_popup', url, {/*orderId: 4*/}, callback);

            return void(0);
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


        $('#order_delivery_variant_id_' + variant_id).parent().next().append("<div class='dd_asset_conteiner' style='position: relative'>" +
            "<input type='hidden' class='dd_last_check' value=''>" +
            "<button onclick=\"return false\" class=\"button\" style='display:block;position: absolute;top: -25px; left:120px;min-width: 190px' id=\"startDD\" " +
            " href=\"javascript:void(0);\" >Выбрать способ доставки</button>" +
            "<div class=\"modal\" id=\"test-modal\" style=\"display: none\"><div id=\"ddelivery\"></div></div>");

        $('#startDD').on('click', function(){
            DDeliveryIntegration.openPopup();
        });


    });
});


/*

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


    var callback = {
        close: function(){
            closePopup();
            //alert('Окно закрыто');
        },
        change: function(data)
        {
            fillFeields(data);
            $( '.moto_moto').empty();
            $( '.dd_asset_conteiner').append( '<div class="moto_moto" style="position: absolute;' +
                'margin-top: 10px; color:#E98B73" >' + data.comment + '</div>' );
            var variant_id = $('.id_dd').parent().parent().find('.radio_button').val();
            CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field2_id, value: ddelivery_insales._id}] );
            //alert(data.comment+ ' интернет магазину нужно взять с пользователя за доставку '+data.clientPrice+' руб. OrderId: '+data.orderId);
            CheckoutDelivery.find( variant_id ).setFieldsValues( [{fieldId: ddelivery_insales.field_id, value: data.orderId }] );
            CheckoutDelivery.find( variant_id ).toExternal().setPrice(data.clientPrice);
            $('.dd_last_check').val(data.orderId);
            closePopup();
        }
    };

    var params = {
        //orderId: 4 // Если у вас есть id заказа который изменяется, то укажите его в этом параметре
        displayContactForm: true
    };

    product_str = sendCart();
    url = ddelivery_insales.url + 'sdk/?iframe=1&pr=' + product_str + '&insales_id=' + ddelivery_insales._id;

    DDelivery.delivery('ddelivery', url, params, callback);

}
$(function(){
    $(document).ready(function(){

        $('.loader').css('display','none');

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


        $('#order_delivery_variant_id_' + variant_id).parent().next().append("<div class='dd_asset_conteiner' style='position: relative'>" +
            "<input type='hidden' class='dd_last_check' value=''>" +
            "<button onclick=\"return false\" class=\"button\" style='display:block;position: absolute;top: -25px; left:120px;min-width: 190px' id=\"startDD\" " +
                " href=\"javascript:void(0);\" >Выбрать способ доставки</button>" +
                "<div class=\"modal\" id=\"test-modal\" style=\"display: none\"><div id=\"ddelivery\"></div></div>");

        $('#startDD').on('click', function(){
            DDeliveryStart();
        });


    });
});
    */