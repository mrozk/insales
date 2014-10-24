var topWindow = parent;

while(topWindow != topWindow.parent) {
    topWindow = topWindow.parent;
}
/*
function enableDDButton(){
    $('.startDD').removeAttr('disabled');
}
*/
if(typeof(topWindow.DDeliveryIntegration) == 'undefined')
    topWindow.DDeliveryIntegration = (function(){
        var th = {};
        var variantID;
        var activeBtn;
        var status = 'Выберите условия доставки';
        th.getStatus = function(){
            return status;
        };

        function hideCover() {
            document.body.removeChild(document.getElementById('ddelivery_cover'));
            document.getElementsByTagName('body')[0].style.overflow = "";
        }

        function showPrompt() {
            /*
                var cover = document.createElement('div');
                cover.id = 'ddelivery_cover';
                document.body.appendChild(cover);
                document.getElementById('ddelivery_container').style.display = 'block';
            */
            var cover = document.createElement('div');
            cover.id = 'ddelivery_cover';
            cover.appendChild(div);
            document.body.appendChild(cover);
            document.getElementById('ddelivery_container').style.display = 'block';

            document.body.style.overflow = 'hidden';
        }

        var callback = {
            close: function(data){
                console.log(data.orderId);
                hideCover();
            },
            change: function(data) {
                console.log(data);
                if( DDeliveryTokenManager.getIsOnePageType() ){
                    fillFields(data);
                }

                $( '#' + activeBtn).parent().find('.moto_moto').remove();
                $( '#' + activeBtn).parent().append( '<div class="moto_moto" style="' +
                                                     'margin-top: 0px; color:#E98B73" >' + data.comment + '</div>' );

                th.setInternalFields(variantID, { orderId:data.orderId, insalesId:ddConfig.insalesId, comment:data.comment} );
                // Сетим нужные поля

                CheckoutDelivery.find( variantID ).toExternal().setPrice(data.clientPrice);


                DDeliveryTokenManager.setFilterPayment(data.filter);
                DDeliveryTokenManager.cleanMethods(DDeliveryTokenManager.getFilterPayment());

                $( '#' + activeBtn).parent().find('.dd_last_check').val(data.orderId);
                $('#price_' + variantID).css('display','block');
                status = data.comment;

                hideCover();
                document.getElementById('ddelivery_container').style.display = 'none';
            }
        }

        function fillFields(data){
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

        th.setInternalFields = function( varID, data ){
            CheckoutDelivery.find( varID ).setFieldsValues( [{fieldId: ddConfig.fieldOrderId, value: data.orderId }] );
            CheckoutDelivery.find( varID ).setFieldsValues( [{fieldId: ddConfig.fieldInsId, value: data.insalesId}] );
            CheckoutDelivery.find( varID ).setFieldsValues( [{fieldId: ddConfig.fieldInsComment, value: data.comment }] );
        }
        th.typeOfWindow = null;

        th.openPopup = function(){

            showPrompt();
            /*
            var body = document.getElementsByTagName('body')[0];
            body.style = 'overflow:hidden;';
            console.log(body.style);
            $()
            */
            document.getElementById('ddelivery_popup').innerHTML = '';
            //$('#ddelivery_cover').appendChild('#ddelivery_container');
            // Назначаем параметры
            var param = {};
            if( DDeliveryTokenManager.getIsOnePageType() ){
                param.client = {
                    name:$('#client_name').val(),
                    phone:$('#client_phone').val(),
                    email:$('#client_email').val()
                };
                var paramsToAdd = {};
                paramsToAdd.fields_values = $('input[id^="shipping_address_field_"]').serializeArray();
                $.each(paramsToAdd.fields_values, function(i, field){
                    var element = $('input[name="' + field.name + '"]');
                    var text = element.parent().parent().find('label[for^="shipping_address_field_"]').text();
                    text = text.trim();
                    text = text.slice(0, -1);
                    field.name = text;
                });


                /*
                console.log( paramsToAdd );
                console.log( window.ORDER.shipping_address );
                */
                //window.ORDER.shipping_address
                /*
                console.log( paramsToAdd );
                console.log( window.ORDER.shipping_address );
                */
                param.address = paramsToAdd;
                console.log( param.address );


            }else{
                param.client = window.ORDER.client;
                param.address = window.ORDER.shipping_address;
            }

            param.token = DDeliveryTokenManager.getToken();
            param.wayId = variantID;


            if( DDeliveryIntegration.typeOfWindow != null ){
                param.type_of_window = DDeliveryIntegration.typeOfWindow;
            }
            // Назначаем параметры
            var url =  ddConfig.sdkPath + '?' + $.param(param);

            DDelivery.delivery('ddelivery_popup', url, {}, callback);
            //return;

        };
        var style = document.createElement('STYLE');
        /*
        style.innerHTML = ' #delivery_info_ddelivery_all a{display: none;} ' +
                ' #ddelivery_popup { display: inline-block; position:relative; vertical-align: middle; margin: 10px auto; width: 1000px; height: 650px;} ' +
                ' #ddelivery_container { position:fixed;top:0;left:0;  z-index: 9999;display: none; width: 100%; height: 100%; text-align: center;  } ' +
                ' #ddelivery_container:before { display: inline-block; height: 100%; content: \'\'; vertical-align: middle;} ' +
                ' #ddelivery_cover { position: fixed; top: 0; left: 0; right:0; bottom:0;  overflow:auto; z-index: 9000; width: 100%; height: 100%; background-color: #000; background: rgba(0, 0, 0, 0.5); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = #7F000000, endColorstr = #7F000000); } ';
        */
        style.innerHTML = // Скрываем ненужную кнопку
            " #delivery_info_ddelivery_all a{display: none;} " +
            " #ddelivery_popup { display: inline-block; vertical-align: middle; margin: 10px auto; width: 1000px; height: 650px;} " +
            " #ddelivery_container {  z-index: 9999;display: none; width: 100%; height: 100%; text-align: center;  } " +
            " #ddelivery_container:before { display: inline-block; height: 100%; content: ''; vertical-align: middle;} " +
            " #ddelivery_cover {overflow: auto;position: fixed; top: 0; left: 0; right:0; bottom:0; z-index: 9000; width: 100%; height: 100%; background-color: #000; background: rgba(0, 0, 0, 0.5); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = #7F000000, endColorstr = #7F000000); } ";


        var body = document.getElementsByTagName('body')[0];

        //body.style = 'overflow:hidden;';

        body.appendChild(style);
        var div = document.createElement('div');
        div.innerHTML = '<div id="ddelivery_popup"></div>';
        div.id = 'ddelivery_container';
        body.appendChild(div);

        $(document).ready(function(){


             $("input[name='commit']").on('click', function(){
                 var element;
                 $('input[name="order[delivery_variant_id]"]').each(function(){
                     if( $(this).attr('checked') == 'checked' ){
                         activeBtn = $(this).val();
                         element = $(this);
                     }
                 });

                 if( ddConfig.wayId.indexOf(parseInt(activeBtn)) != -1){
                     if( element.parent().parent().find('.dd_asset_conteiner').find('.dd_last_check').val() != '' ){
                         //$('#order_form').submit();
                         return true;
                     }else{
                         alert('Уточните выбор текущего способа доставки доставки ');
                         return false;
                     }
                 }else{
                     return true;
                 }
                 return false;
             });

            var buttonCode1 = '<div class=\"dd_asset_conteiner\" style=\"position: relative\">' +
                '<input type=\"hidden\" class=\"dd_last_check\" value=\"\">' +
                '<button id="dd_start1" ' + //disabled="disabled" ' +
                ' class=\"startDD button\" style=\"display:block;clear:both;\" ' +
                ' href=\"javascript:void(0);\" >Выбрать</button></div>';

            var buttonCode2 = '<div class=\"dd_asset_conteiner\" style=\"position: relative\">' +
                '<input type=\"hidden\" class=\"dd_last_check\" value=\"\">' +
                '<button id="dd_start2" ' + //disabled="disabled" ' +
                ' class=\"startDD button\" style=\"display:block;clear:both;\" ' +
                ' href=\"javascript:void(0);\" >Выбрать</button></div>';


            $('#order_delivery_variant_id_' + ddConfig.wayId[0]).parent().next().append(buttonCode1 );

            if( ddConfig.wayId.length == 2 ){
                $('#order_delivery_variant_id_' + ddConfig.wayId[1]).parent().next().append( buttonCode2 );
            }



            $('.startDD').on('click', function(){
                if(  ddConfig.wayId.length == 2  ){
                    if( $(this).attr('id') == 'dd_start1' ){
                        DDeliveryIntegration.typeOfWindow = 'onlyMap';
                        variantID = ddConfig.wayId[0];
                        activeBtn = 'dd_start1';
                    }else if($(this).attr('id') == 'dd_start2'){
                        DDeliveryIntegration.typeOfWindow = 'onlyCourier';
                        variantID = ddConfig.wayId[1];
                        activeBtn = 'dd_start2';
                    }
                }else{
                    variantID = ddConfig.wayId[0];
                    activeBtn = 'dd_start1';
                }
                $('#order_delivery_variant_id_' + variantID).click();

                DDeliveryIntegration.openPopup();

                return false;
            });




            // Спрятать плохие поля
            /*
            $('.delivery_variants .radio_button').on('change',function(){
                if( DDeliveryTokenManager.getIsOnePageType() ){
                    if( ddConfig.wayId.indexOf(  parseInt( $(this).val())) != -1 ){
                        DDeliveryTokenManager.updatePriceAndPoint();
                    }
                }

                if( ddConfig.wayId.indexOf(  parseInt( $(this).val())) != -1 ){
                    $('#shipping_address_city').attr('disabled','disabled');
                    $('#shipping_address_zip').attr('disabled','disabled');
                }else{
                    $('#shipping_address_city').removeAttr('disabled');
                    $('#shipping_address_zip').removeAttr('disabled');
                }

            });
             */
        });



        return th;
    })();
var DDeliveryIntegration = topWindow.DDeliveryIntegration;
window.get_external_delivery_price = function(id, url) {
    if( ddConfig.wayId.indexOf(id) != -1 ){
        if( (ddConfig.wayId.length > 1) && (id == ddConfig.wayId[1]) ){
            draw_external_price(ddConfig.wayId[0], { 'delivery_price': 0});
            draw_external_price(ddConfig.wayId[1], { 'delivery_price': 0});
            return;
        }else{
            console.log('Token when go to srver' + DDeliveryTokenManager.getToken());
            console.log('Идем на сервак');
            external_params = {token: DDeliveryTokenManager.getToken()}
            return $.ajax({
                url: url,
                type: 'GET',
                jsonpCallback: 'jsonCallback3',
                dataType: 'jsonp',
                data: external_params,
                success: function(data) {
                    console.log(data);
                    if( data.success == "true" ){
                        DDeliveryTokenManager.setMethod(data.userInfo.method);
                        if( ddConfig.wayId.indexOf(DDeliveryTokenManager.getMethod()) == -1 ){
                            draw_external_price(ddConfig.wayId[0], { 'delivery_price': 0});
                            return;
                        }
                        DDeliveryTokenManager.setFilterPayment(data.userInfo.filter);
                        DDeliveryTokenManager.cleanMethods(DDeliveryTokenManager.getFilterPayment());
                        //alert('clean methods' + data.userInfo.filter);

                        $('#order_delivery_variant_id_' + data.userInfo.method).parent().parent().find('.dd_asset_conteiner .moto_moto').remove();
                        $('#order_delivery_variant_id_' + data.userInfo.method).parent().parent().find('.dd_asset_conteiner .dd_last_check').val(data.userInfo.order_id);
                        $('#order_delivery_variant_id_' + data.userInfo.method).parent().parent().find('.dd_asset_conteiner').append('<div class="moto_moto" style="' +
                        'margin-top: 0px; color:#E98B73" >' + data.userInfo.comment + '</div>');

                        var variantID = parseInt( data.userInfo.method );

                        DDeliveryIntegration.setInternalFields(variantID, { orderId:data.userInfo.order_id, insalesId:ddConfig.insalesId, comment:data.userInfo.comment} );
                        /*
                        CheckoutDelivery.find( variantID ).setFieldsValues( [{fieldId: ddConfig.fieldOrderId, value: data.order_id }] );
                        CheckoutDelivery.find( variantID ).setFieldsValues( [{fieldId: ddConfig.fieldInsId, value: ddConfig.insalesId}] );
                        CheckoutDelivery.find( variantID ).setFieldsValues( [{fieldId: ddConfig.fieldInsComment, value: data.comment }] );
                        */
                        return draw_external_price( parseInt(data.userInfo.method), { 'delivery_price': data.userInfo.price});
                    }else{
                        console.log('Цены нет' +  DDeliveryTokenManager.getToken());
                        draw_external_price(ddConfig.wayId[0], { 'delivery_price': 0});
                    }
                    console.log(data);
                }
            });
        }
    }else{
        return $.ajax({
            url: '/delivery/for_external.json',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var cache_key, external_params;
                external_params = {
                    price: data.price,
                    weight: data.weight,
                    region: data.region,
                    city: data.city,
                    zip: data.zip || 0
                };
                $.each($("#order_delivery_variant_id_" + id).data(), function(k, v) {
                    if (k.match(/^external/)) {
                        return external_params[k] = v;
                    }
                });

                 cache_key = $.param(external_params);
                 if (external_deliveries_cache[id][cache_key]) {
                 draw_external_price(id, external_deliveries_cache[id][cache_key]);
                 return;
                 }

                return $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'jsonp',
                    data: external_params,
                    success: function(data) {
                        external_deliveries_cache[id][cache_key] = data;
                        return draw_external_price(id, data);
                    }
                });
            }
        });
    }

};
window.get_payments = function() {
    $('#payment_gateways :radio').each(function() {
        return disable_element(this);
    });
    return $.ajax({
        url: '/payment/for_order.json',
        type: 'PUT',
        dataType: 'json',
        data: $('#order_form').formSerialize(),
        timeout: 10000,
        success: function(data) {

            var input;
            window.payment_error_reported = false;
            window.payment_errors_counter = 0;
            $.each(data, function() {
                var input, price_string;
                price_string = parseFloat(this.price) ? (this.price > 0 ? '+' : '-') + ' ' + InSales.formatMoney(Math.abs(this.price)) : '';
                $("#summ_" + this.id).html(price_string).attr('data-price', this.price);
                if ((input = $("#order_payment_gateway_id_" + this.id)).is(':radio')) {
                    enable_element(input);
                    if (this.selected) {
                        return input.click();
                    }
                }
            });
            var checkedVariant = $('input[name="order[delivery_variant_id]"]:checked').val();
            if( DDeliveryTokenManager.getFilterPayment().length > 0 && ( DDeliveryTokenManager.getMethod() == parseInt( checkedVariant )) ){
                DDeliveryTokenManager.cleanMethods(DDeliveryTokenManager.getFilterPayment());
            }

            if ((input = $('#payment_gateways :radio:checked')).is(':disabled') || !input.length) {
                return $('#payment_gateways :radio:enabled').first().click();
            }
        },
        error: function(xhr, status, error_thrown) {
            if (status !== 'error' && status !== 'timeout') {
                return;
            }
            if (window.payment_errors_counter >= 6) {
                if (window.Rollbar && !window.payment_error_reported) {
                    Rollbar.error("Cant get payments: " + status, {
                        status: status,
                        error: error_thrown,
                        response_text: xhr.responseText,
                        headers: xhr.getAllResponseHeaders(),
                        data: $('#order_form').formSerialize(),
                        status_code: xhr.statusCode(),
                        status_text: xhr.statusText
                    });
                    window.payment_error_reported = true;
                }
                enable_deliveries_and_payments();
                select_first_deliveries_and_payments();
            }
            if (window.payment_errors_counter < 6) {
                window.debounced_payments();
            }
            return window.payment_errors_counter++;
        }
    });
};
/*
window.get_deliveries = function() {
    disable_deliveries_and_payments();
    return $.ajax({
        url: '/delivery/for_order.json',
        type: 'PUT',
        dataType: 'json',
        data: $('#order_form').formSerialize(),
        timeout: 10000,
        success: function(data) {
            console.log(data);
            var input;
            window.delivery_errors_counter = 0;
            window.delivery_error_reported = false;
            $.each(data, function() {
                var input;
                if (this.errors.price) {
                    $("#delivery_error_" + this.id).html(this.errors.price).show();
                } else if (CheckoutDelivery.find(this.id)) {
                    CheckoutDelivery.find(this.id).setPrice(this.price);
                }
                if ((input = $("#order_delivery_variant_id_" + this.id)).is(':radio')) {
                    enable_element(input);
                    if (this.selected) {
                        return input.click();
                    }
                }
            });
            if ((input = $('#delivery_variants :radio:checked')).is(':disabled') || !input.length) {
                $('#delivery_variants :radio:enabled').first().click();
            }
            return $(document).trigger('calc_delivery');
        },
        error: function(xhr, status, error_thrown) {
            if (status !== 'error' && status !== 'timeout') {
                return;
            }
            if (window.delivery_errors_counter >= 6) {
                if (window.Rollbar && !window.delivery_error_reported) {
                    Rollbar.error("Cant get deliveries: " + status, {
                        status: status,
                        error: error_thrown,
                        response_text: xhr.responseText,
                        headers: xhr.getAllResponseHeaders(),
                        data: $('#order_form').formSerialize(),
                        status_code: xhr.statusCode(),
                        status_text: xhr.statusText
                    });
                    window.delivery_error_reported = true;
                }
                enable_deliveries_and_payments();
                select_first_deliveries_and_payments();
            }
            if (window.delivery_errors_counter < 6) {
                window.debounced_deliveries();
            }
            return window.delivery_errors_counter++;
        }
    });
};
*/




