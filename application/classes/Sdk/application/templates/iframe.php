<!doctype html>
<html lang="ru-RU">
    <head>
        <meta charset="UTF-8">
        <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?=$staticURL?>css/screen.css?<?=$version?>"/>
    </head>
    <body>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/modernizr.custom.76185.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.mCustomScrollbar.concat.min.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.custom-radio-checkbox.min.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.formtips.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.maskedinput.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/jquery.JSON.min.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.map.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.header.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.courier.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.contact_form.js?<?=$version?>"></script>
    <script type="text/javascript" src="<?=$staticURL?>js/ddelivery.type_form.js?<?=$version?>"></script>
    <script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" async="async" type="text/javascript"></script>
    <div id="ddelivery"></div>
    <div id="ddelivery_loader">
        <div class="map-popup">
            <div class="map-popup__head">
                <p>DDelivery. Доставка в удобную Вам точку.</p>

                <div class="map-popup__head__close">&nbsp;</div>
            </div>
            <!--map-popup__head end-->
            <div class="map-popup__main">
                <div class="map-popup__main__overlay">&nbsp;</div>
                <div class="map-popup__main__delivery">
                    <div class="loader">
                        <p>Подождите пожалуйста, мы ищем лучшие предложения</p>
                        <img src="<?=$staticURL?>/img/ajax_loader_horizont.gif"/>
                    </div>
                    <div>
                        <p class="load_error">
                            Произошла ошибка, <a href="javascript:void(0)">повторить запрос</a>
                        </p>
                    </div>
                </div>

            </div>
            <div class="map-popup__bott">
                <a href="http://ddelivery.ru/" target="blank">Сервис доставки DDelivery.ru</a>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter25675664 = new Ya.Metrika({id:25675664,
                        webvisor:true,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true});
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>


    <script>

        var DDeliveryIframe = (function () {
            //Тут можно определить приватные переменные и методы

            var componentUrl, staticUrl, lastData;

            function repeatLastQuery() {
                DDeliveryIframe.ajaxPage(lastData);
            }
            //Объект, содержащий публичное API
            return {
                componentUrl: null,
                staticUrl: null,
                orderId: null,
                init: function (_componentUrl, _staticUrl) {

                    /*
                    if(!window.parent || window.parent == window) {
                        //document.location.href='http://www.ddelivery.ru/';
                    }
                    */
                    // Инициализация модуля. В ней мы инициализируем все остальные модули на странице
                    this.componentUrl = componentUrl = _componentUrl;
                    this.staticUrl = staticUrl = _staticUrl;
                    // Да, нужно его подрубить тут
                    Header.init();

                    this.ajaxPage({});

                    $('#ddelivery_loader .load_error a').click(repeatLastQuery);
                },
                ajaxPage: function (data) {
                    lastData = data;
                    var th = this;
                    if (this.orderId)
                        data.order_id = this.orderId;
                    $('#ddelivery').hide();
                    $('#ddelivery_loader').show();

                    $('#ddelivery_loader .loader').show();
                    $('#ddelivery_loader .load_error').hide();

                    $.post(componentUrl, data, function (dataHtml) {

                        $('#ddelivery_loader').hide();

                        $('#ddelivery').html(dataHtml.html).show();

                        if (typeof(dataHtml.orderId) != 'undefined' && dataHtml.orderId) {
                            th.orderId = dataHtml.orderId;
                        }

                        $(window).trigger('ajaxPageRender', {params: data, result: dataHtml});

                        th.render(dataHtml);

                    }, 'json').fail(function(responce, errorType) {

                        if(typeof(console.log) != 'undefined')
                            console.log(responce.responseText);
                        $('#ddelivery_loader .loader').hide();
                        $('#ddelivery_loader .load_error').show();
                    });
                    $(window).trigger('ajaxPageRequest', {params: data});
                },
                ajaxData: function (data, callBack) {
                    if (this.orderId)
                        data.order_id = this.orderId;
                    $.post(componentUrl, data, function(result){
                        $(window).trigger('ajaxDataResult', {params: data, result: result});
                        callBack(result);
                    }, 'json');
                },
                render: function (data) {
                    // У всех
                    Header.init();

                    if (typeof(data.js) != 'undefined' && data.js.length > 0) {
                        var js = data.js.split(',');
                        for (var k = 0; k < js.length; k++) {
                            switch (js[k]) {
                                case 'courier':
                                    Courier.init();
                                    break;
                                case 'map':
                                    Map.init(data);
                                    break;
                                case 'contactForm':
                                    ContactForm.init();
                                    break;
                                case 'typeForm':
                                    TypeForm.init();
                                    break;
                                case 'close':
                                    DDeliveryIframe.close();
                                    break;
                                case 'change':
                                    DDeliveryIframe.postMessage('change', data);
                                    break;
                            }
                        }
                    }
                },
                postMessage: function(action, data) {
                    // Отправляем сообщение родительскому окну
                    var dataJSON = $.toJSON({action:action, data: data});
                    window.parent.postMessage(dataJSON, '*');
                },
                close: function(){
                    DDeliveryIframe.postMessage('close', {});
                }
            }
        })();

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



        $(function(){

            DDeliveryIframe.init(<?=json_encode($scriptURL)?>, <?=json_encode($staticURL)?>);
        });
    </script>





    </body>
</html>