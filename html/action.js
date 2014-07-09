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
            if (!window.location.origin)
                window.location.origin = window.location.protocol+"//"+window.location.host;
            $.ajax({
                dataType: "json",
                url: window.location.origin + '/cart_items.json',
                async: false,
                success: function(data)
                {
                    console.log(data);
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
            $( '#shipping_address_address').val(data.comment);
            $('#shipping_address_field_' + ddelivery_insales.house).val(data.userInfo.toHouse);
            $('#shipping_address_field_' + ddelivery_insales.street).val(data.userInfo.toStreet);
            $('#shipping_address_field_' + ddelivery_insales.flat).val(data.userInfo.toFlat);
        }
        th.openPopup = function(){
            $('.id_dd').parent().parent().find('.radio_button').attr("checked","checked");
            showPrompt();
            document.getElementById('ddelivery_popup').innerHTML = '';
            //jQuery('#ddelivery_popup').html('').modal().open();
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
            checked = $('#order_delivery_variant_id_' + ddelivery_insales.delivery_id).attr("checked");
            if( checked == 'checked' )
            {
                if( $('.dd_last_check').val() != '' )
                {
                    return true;
                }
                else
                {
                    alert('Выберите точку доставки DDelivery');
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
            "<button onclick=\"return false\" class=\"button\" style='max-height:20px;font:12px Tahoma,sans-serif; padding:  2px 9px;display:block;position: absolute;top: -30px; left:65px;min-width: 190px' id=\"startDD\" " +
            " href=\"javascript:void(0);\" >Выбрать способ доставки</button>" );

        $('#startDD').on('click', function(){
            DDeliveryIntegration.openPopup();
        });


    });
});
