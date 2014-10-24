<style type="text/css">
    .bg-success{
        padding: 10px;
    }
    .error{
        border: 2px solid #D12121;
    }
</style>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#insales-form').submit(function(){

            var matchInt = /^[0-9\-]{1,}$/;
            var matchFloat = /^[0-9\.\-]{1,}$/;
            var error = 0;
            var submit = 0;
            $('#insales-form').find('.form-control').each(function(){
                $(this).removeClass('error');

                if( $(this).hasClass('req') ){
                    if( $(this).val() == '' ){
                        $(this).addClass('error');
                        error = 1;
                    }
                }
                if( $(this).hasClass('int') && $(this).val() != '' ){
                    var value =  $(this).val();
                    if( !matchInt.test( value ) ){
                        $(this).addClass('error');
                        error = 1;
                    }
                }

                if( $(this).hasClass('float') && $(this).val() != '' ){
                    var value =  $(this).val();
                    if( !matchFloat.test( value ) ){
                        $(this).addClass('error');
                        error = 1;
                    }
                }
            });
            if( error == 0 ){
                return true;
            }else{
                alert('Обнаружена ошибка в заполнении полей');
            }
           return false;
        });
    });
</script>
<?php
    $companiesArray = \DDelivery\DDeliveryUI::getCompanySubInfo();
?>
<?php

if( empty($usersettings->settings) ){

    $companiesArrayIDs = array_keys($companiesArray);
    $pvz_companies = implode(',', $companiesArrayIDs);
    $cur_companies = implode(',', $companiesArrayIDs);

    $settings =
        array(
            'api' => '',
            'rezhim' => '',
            'declared' => '',
            'width' => '',
            'height' => '',
            'length' => '',
            'weight' => '',
            'status' => '',
            'secondname' => '',
            'firstname' => '',
            'plan_width' => '',
            'plan_lenght' => '',
            'plan_height' => '',
            'plan_weight' => '',
            'type' => '',
            'pvz_companies' => $pvz_companies,
            'cur_companies' => $cur_companies,
            'from1' => '',
            'to1' => '',
            'val1' => '',
            'sum1' => '',
            'from2' => '',
            'to2' => '',
            'val2' => '',
            'sum2' => '',
            'from3' =>'',
            'to3' => '',
            'val3' => '',
            'sum3' => '',
            'okrugl' => '',
            'shag' => '',
            'zabor' => '',
            'payment' => '',
            'address' => '',
            'theme' => '',
            'form' => '',
            'common_caption' => '',
            'self_caption' => '',
            'courier_caption',
            'common_description' => '',
            'self_description' => '',
            'courier_description' => '',
            'source_params'  => '',

            'params_width' => '',
            'params_length' => '',
            'params_height'  => ''

        );
}else{
    $settings = json_decode( $usersettings->settings, true );
}
?>


<?php
if(empty($settings['address'])){
    $address = array('street' => '','house' => '','corp' => '','flat' => '');
}else{
    $address = json_decode($settings['address'],true);
}

$pvz_companies = explode( ',', $settings['pvz_companies'] );
$cur_companies = explode( ',', $settings['cur_companies'] );

//$allowed_cur_payment =  explode( ',', $settings['allowed_cur_payment']);
//$allowed_self_payment = explode(',', $settings['allowed_self_payment'])

?>

<?php
if( strpos( $_SERVER['REQUEST_URI'], 'admin')){
    $user = true;

}else{
    $user = null;
}
if ($user !== null){
?>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand active" style="color: #fff" href="javascript:void(0)">Настройки DDelivery Insales</a>
            </div>
            <div style="margin-top: 5px;">
                <a  class="btn btn-warning"  href="/admin/">Вернуться назад</a>
                <a class="btn btn-success" href="/admin/main/addway/?id=<?php echo $id; ?>">Добавить способ доставки в Insales</a>
                <a class="btn btn-success" onclick="jQuery('#insales-form').submit();" href="javascript:void(0)">Сохранить</a>
                <a class="btn btn-success" href="/admin/auth/logout/">Выход</a>
            </div>
        </div>
    </div>
<?php
}else{ ?>
    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand active" style="color: #fff" href="javascript:void(0)">Настройки DDelivery Insales</a>
            </div>
            <?php /*
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li ><a href="http://<?php echo $usersettings->shop . '/admin/installed_applications/';  ?>">Вернуться в кабинет</a></li>
                    <li ><a  href="<?php echo $base_url . 'cabinet/addway/'?>">Добавить способ доставки в Insales</a></li>
                    <li><a class="btn btn-xs btn-success" onclick="jQuery('#insales-form').submit();" href="javascript:void(0)">Сохранить</a></li>
                    <li><a href="http://ddelivery.ru">DDelivery.ru</a></li>

                </ul>
            </div><!--/.nav-collapse -->
            <?php */ ?>
            <div style="margin-top: 5px;">
                <a  class="btn btn-warning"  href="http://<?php echo $usersettings->shop . '/admin/installed_applications/';  ?>">Вернуться в кабинет</a>
                <a class="btn btn-success" href="<?php echo $base_url . 'cabinet/addway/'?>">Добавить способ доставки в Insales</a>
                <a class="btn btn-success" onclick="jQuery('#insales-form').submit();" href="javascript:void(0)">Сохранить</a>
                <a class="btn btn-lg btn-link" href="http://ddelivery.ru">DDelivery.ru</a>
            </div>
        </div>
    </div>
<?php
}
?>


<div class="container theme-showcase" style="margin-top: 50px;">
<?php
if( !empty($message['success'])){
?>
<p class="bg-success" style="text-align: center" >
    <?php echo  $message['success']; ?>
</p>
<?php
}
?>
<form role="form" id="insales-form" action="<?php echo (($user !== null)?'/admin/main/save/?id=' . $id :$base_url . 'cabinet/save/');?>" method="post">
    <div class="jumbotron">
        <!-- <h1>Jumbotron heading</h1> -->
        <p class="lead">
            Уважаемые пользователи! Мы постарались сделать настройки наиболее гибкими, но от вас требуется внимательность при выборе параметров.
            Если Вам непонятно значение каких-то настроек, просим связатся с менеджерами DDelivery. В случае, если Вам потребуется больше настроек, так же
            просим связатся с клиентским отделом.
        </p>
        <!-- <p><a class="btn btn-lg btn-success" href="#" role="button">Sign up today</a></p> -->
    </div>

    <div class="page-header">
        <h3>Основные</h3>
    </div>

    <div class="row marketing">
        <div class="col-lg-6">
            <h4>API ключ из личного кабинета</h4>
            <p class="bg-success">Ключ можно получить в личном кабинете DDelivery.ru, зарегистрировавшись на сайте ( для новых клиентов )</p>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'API ключ из личного кабинета',
                    'class' => 'form-control req',
                    'required' => ''
                );
                echo Form::input('api', $settings['api'], $attrs);
                ?>
            </p>

            <h4>Режим работы</h4>
            <p class="bg-success">Для отладки модуля используйте, пожалуйста, режим тестирования.</p>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                $options = array(
                    '1' => 'Тестирование (stage.ddelivery.ru)',
                    '2' => 'Рабочий (cabinet.ddelivery.ru)'
                );
                echo Form::select('rezhim', $options, $settings['rezhim'], $attrs);
                ?>
            </p>

            <h4>Статус для отправки</h4>
            <p class="bg-success">Выберите статус, при котором заявки из вашей системы будут уходить в DDelivery.
                Помните, что отправка означает готовность отгрузить заказ на следующий рабочий день</p>
            <p>
                <?php

                $attrs = array(
                    'class' => 'form-control'
                );
                $options = array(
                    'new' => 'новый',
                    'accepted' => 'в обработке',
                    'approved' => 'согласован',
                    'dispatched' => 'отгружен',
                    'delivered' => 'доставлен',
                    'declined' => 'отменен',
                );
                echo Form::select('status', $options, $settings['status'], $attrs);

                ?>
            </p>

        </div>

        <div class="col-lg-6">

            <h4>Какой % от стоимости товара страхуется</h4>
            <p class="bg-success">Вы можете снизить оценочную стоимость для уменьшения стоимости доставки за счет снижения размеров страховки</p>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Какой % от стоимости товара страхуется',
                    'class' => 'form-control req int'
                );
                echo Form::input('declared', (isset($settings['declared'])?$settings['declared']:100), $attrs);
                ?>
            </p>

            <h4>Тема оформления</h4>
            <p class="bg-success">Вы можете выбрать цветовую схему оформления модуля, которая будет показана клиенту</p>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                $options = array(
                    'default' => 'По умолчанию',
                    'blue' => 'Blue'
                );
                echo Form::select('theme', $options, $settings['theme'], $attrs);
                ?>
            </p>

            <h4>Доступные способы</h4>
            <p class="bg-success"> Настройка влияет на то, какие методы будут отображатся. Для того чтобы настройки вступили в действие нажмите "Сохранить" -> "Добавить способ доставки в Insales"</p>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                $options = array(
                    '1' => 'ПВЗ и Курьеры',
                    '2' => 'ПВЗ',
                    '3' => 'Курьеры',
                    '4' => 'Разделить ПВЗ и курьеры'
                );
                echo Form::select('type', $options, $settings['type'], $attrs);
                ?>
            </p>

            <h4>Дополнительное подтверждение заказа в модуле</h4>
            <p class="bg-success"> Эта настройка устанавливает, запрашивать ли с пользователя информацию о себе в модуле DDelivery</p>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                $options = array(
                    '0' => 'требуется',
                    '1' => 'не требуется'
                );
                echo Form::select('form', $options, $settings['form'], $attrs);
                ?>
            </p>





        </div>
    </div>



    <div class="row marketing">
        <div class="page-header">
            <h3>Специализированные настройки</h3>
            <p class="bg-success">Для того чтобы настройки вступили в действие нажмите "Сохранить" -> "Добавить способ доставки в Insales"</p>
        </div>

        <div class="col-lg-6">

            <h4>Подпись способа доставки </h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'DDelivery - сервис доставки',
                    'class' => 'form-control'
                );
                echo Form::input('common_caption', $settings['common_caption'], $attrs);
                ?>
            </p>

            <h4>Описание способа доставки </h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Доставка товаров во все населенные пункты России + пункты самовывоза в 150 городах',
                    'class' => 'form-control'
                );
                echo Form::input('common_description', $settings['common_description'], $attrs);
                ?>
            </p>

        </div>
        <div class="col-lg-6">
            <h4>Подпись способа доставки "Самовывоз"</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'DDelivery - самовывоз',
                    'class' => 'form-control'
                );
                echo Form::input('self_caption', $settings['self_caption'], $attrs);
                ?>
            </p>


            <h4>Описание способа доставки "Самовывоз"</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Доставка товаров во все населенные пункты России + пункты самовывоза в 150 городах',
                    'class' => 'form-control'
                );
                echo Form::input('self_description', $settings['self_description'], $attrs);
                ?>
            </p>



            <h4>Подпись способа доставки "Курьерская доставка"</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'DDelivery - курьерская доставка',
                    'class' => 'form-control'
                );
                echo Form::input('courier_caption',$settings['courier_caption'], $attrs);
                ?>
            </p>


            <h4>Описание способа доставки "Курьерская доставка"</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Доставка товаров во все населенные пункты России + пункты самовывоза в 150 городах',
                    'class' => 'form-control'
                );
                echo Form::input('courier_description', $settings['courier_description'], $attrs);
                ?>
            </p>


        </div>
    </div>





    <div class="row marketing">
        <div class="page-header">
            <h3>Габариты по умолчанию</h3>
        </div>
        <p class="bg-success">Данные габариты используются для определения цены доставки в случае, если у товара не прописаны размеры.
                              Просим Вас внимательней отнестись к вводу данных
        </p>
        <div class="col-lg-6">


            <h4>Ширина по умолчанию, см</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Ширина, см',
                    'class' => 'form-control req int'
                );
                echo Form::input('plan_width', (isset($settings['plan_width'])?$settings['plan_width']:10), $attrs);
                ?>
            </p>

            <h4>Длина по умолчанию, см</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Длина, см',
                    'class' => 'form-control req int'
                );
                echo Form::input('plan_lenght', (isset($settings['plan_lenght'] )?$settings['plan_lenght']:10), $attrs);
                ?>
            </p>

        </div>
        <div class="col-lg-6">
            <h4>Высота по умолчанию, см</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Высота, см',
                    'class' => 'form-control req int'
                );
                echo Form::input('plan_height', (isset($settings['plan_height'] )?$settings['plan_height']:10), $attrs);
                ?>
            </p>

            <h4>Вес по умолчанию, кг</h4>
            <p>
                <?php
                $attrs = array(
                    'placeholder' => 'Вес, кг',
                    'class' => 'form-control req float'
                );
                echo Form::input('plan_weight', (isset($settings['plan_weight'])?$settings['plan_weight']:1), $attrs);
                ?>
            </p>

        </div>
    </div>


    <div class="page-header">
        <h3>Соответствие полей</h3>
    </div>
    <div class="row marketing">
        <div class="col-lg-6">
            <h4>Источник габаритов товара</h4>
            <p class="bg-success">Откуда будут браться габариты товара</p>
            <p>
                <?php
                $source_params = array();
                $source_params['0'] = 'Дополнительные поля';
                $source_params['1'] = 'Параметры  товаров';
                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('source_params',  $source_params, $settings['source_params'], $attrs);

                ?>
            </p>
            <?php /*
            <h4>Источник информации</h4>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                $gabarits = array();
                $gabarits[0] = 'Пораметры';
                $gabarits[1] = 'Дополнительные поля магазина';
                echo Form::select('width', $gabarits, $settings['width'], $attrs);
                ?>
            </p>
            */ ?>
            <h4>Ширина "Дополнительные поля"</h4>
            <p class="bg-success"> Выберите поле, соответствующее полю "ширина товара" в Вашей системе</p>
            <p>
                <?php

                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('width', $fields, $settings['width'], $attrs);

                ?>
            </p>
            <h4>Длина "Дополнительные поля"</h4>
            <p class="bg-success"> Выберите поле, соответствующее полю "длина товара" в Вашей системе</p>
            <p>
                <?php

                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('length', $fields, $settings['length'], $attrs);
                ?>
            </p>

            <h4>Высота "Дополнительные поля"</h4>
            <p class="bg-success">Выберите поле, соответствующее полю "высота товара" в Вашей системе</p>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                echo Form::select('height', $fields, $settings['height'], $attrs);
                ?>
            </p>


            <h4>Ширина "Параметры  товаров"</h4>
            <p class="bg-success"> Выберите поле, соответствующее полю "ширина товара" в Вашей системе</p>
            <p>
                <?php

                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('params_width', $characteristics, $settings['params_width'], $attrs);

                ?>
            </p>
            <h4>Длина "Параметры  товаров"</h4>
            <p class="bg-success"> Выберите поле, соответствующее полю "длина товара" в Вашей системе</p>
            <p>
                <?php

                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('params_length', $characteristics, $settings['params_length'], $attrs);
                ?>
            </p>

            <h4>Высота "Параметры  товаров"</h4>
            <p class="bg-success">Выберите поле, соответствующее полю "высота товара" в Вашей системе</p>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                echo Form::select('params_height', $characteristics, $settings['params_height'], $attrs);
                ?>
            </p>




        </div>
        <div class="col-lg-6">

            <h4>Улица</h4>
            <p class="bg-success">Выберите поле, соответствующее полю "Улица" в Вашей системе</p>
            <p>
                <?php


                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('address[street]', $addr_fields, $address['street'], $attrs);

                ?>
            </p>

            <h4>Номер дома</h4>
            <p class="bg-success">Выберите поле, соответствующее полю "Номер дома" в Вашей системе</p>
            <p>
                <?php


                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('address[house]', $addr_fields, $address['house'], $attrs);

                ?>
            </p>


            <h4>Квартира</h4>
            <p class="bg-success">Выберите поле, соответствующее полю "Квартира" в Вашей системе</p>
            <p>
                <?php


                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('address[flat]', $addr_fields, $address['flat'], $attrs);

                ?>
            </p>



            <h4>Корпус</h4>
            <p class="bg-success">Выберите поле, соответствующее полю "Корпус" в Вашей системе</p>
            <p>
                <?php


                $attrs = array(
                    'class' => 'form-control'
                );

                echo Form::select('address[corp]', $addr_fields, $address['corp'], $attrs);

                ?>
            </p>

            <h4>Оплата на месте</h4>
            <p class="bg-success">Выберите поле, соответствующее полю способу оплаты "оплата на месте".
                Например, "оплата курьеру". У вас в системе может быть только один, такой способ
            </p>
            <p>
                <?php
                $attrs = array(
                    'class' => 'form-control'
                );
                ///print_r($payment);
                echo Form::select('payment', $payment, $settings['payment'], $attrs);
                ?>
            </p>
        </div>


    </div>


    <div class="row marketing">

        <div class="page-header">
            <h3>Настройка способов доставки</h3>
        </div>


        <div class="col-lg-6">

                <h4>Доступные компании ПВЗ</h4>
                <p class="bg-success">Выберите компании доставки, которые вы бы хотели сделать доступными для ваших клиентов</p>

            <?php
            $mid = ceil(count($companiesArray)/2);
            $i = 0;
            foreach( $companiesArray as $key => $item ){
                if( ($i%$mid) == 0 ){?>
                    <!--<div class="col-lg-5">-->
                <?php
                }
                ?>
                <p>
                    <label class="checkbox-inline">
                        <?php
                        echo Form::checkbox('pvz_companies[]', $key, in_array($key, $pvz_companies) );
                        echo $item['name'];
                        ?>
                    </label>
                </p>
                <?php
                if( ($i%$mid) == ( $mid - 1 )  ){?>
                    <!--</div>-->
                <?php
                }
                ?>

                <?php
                $i++;
            }
            ?>
        </div>




        <div class="col-lg-6">
            <h4>Доступные компании Курьерская доставка</h4>
            <p class="bg-success">Выберите компании доставки, которые вы бы хотели сделать доступными для ваших клиентов</p>
            <?php
            $mid = ceil(count($companiesArray)/2);
            $i = 0;
            foreach( $companiesArray as $key => $item ){
                if( ($i%$mid) == 0 ){?>
                    <!--<div class="col-lg-5">-->
                <?php
                }
                ?>
                <p>
                    <label class="checkbox-inline">
                        <?php
                        echo Form::checkbox('cur_companies[]', $key, in_array($key, $cur_companies) );
                        echo $item['name'];
                        ?>
                    </label>
                </p>
                <?php
                if( ($i%$mid) == ( $mid - 1 )  ){?>
                    <!--</div>-->
                <?php
                }
                ?>

                <?php
                $i++;
            }
            ?>

        </div>

        <div class="row marketing">

            <div class="page-header">
                <h3>Настройка цены доставки</h3>
            </div>

            <p class="bg-success" >
                Как меняется стоимость доставки в зависимости от размера заказа в руб. Вы можете гибко настроить
                условия доставки,чтобы учесть вашу маркетинговую политику.
            </p>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'col-lg-1',
                    'placeholder' => 'От',
                    'class' => 'form-control int'
                );
                echo Form::input('from1', $settings['from1'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'col-lg-1',
                    'placeholder' => 'До',
                    'class' => 'form-control int'
                );
                echo Form::input('to1', $settings['to1'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'form-control',
                    'style' => 'width: 200px;'
                );
                $options = array(
                    '1' => 'Клиент оплачивает все',
                    '2' => 'Магазин оплачивает все',
                    '3' => 'Магазин оплачивает процент от стоимости доставки',
                    '4' => 'Магазин оплачивает конкретную сумму от доставки. Если сумма больше, то всю доставку<'
                );
                echo Form::select('val1', $options, $settings['val1'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'placeholder' => 'Сумма',
                    'class' => 'form-control int'
                );
                echo Form::input('sum1', $settings['sum1'], $attrs);
                ?>
            </div>

        </div>

        <div class="row marketing">
            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'col-lg-1',
                    'placeholder' => 'От',
                    'class' => 'form-control int'
                );
                echo Form::input('from2', $settings['from2'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'col-lg-1',
                    'placeholder' => 'До',
                    'class' => 'form-control int'
                );
                echo Form::input('to2', $settings['to2'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'form-control',
                    'style' => 'width: 200px;'
                );
                $options = array(
                    '1' => 'Клиент оплачивает все',
                    '2' => 'Магазин оплачивает все',
                    '3' => 'Магазин оплачивает процент от стоимости доставки',
                    '4' => 'Магазин оплачивает конкретную сумму от доставки. Если сумма больше, то всю доставку<'
                );
                echo Form::select('val2', $options, $settings['val2'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'placeholder' => 'Сумма',
                    'class' => 'form-control int'
                );
                echo Form::input('sum2', $settings['sum2'], $attrs);
                ?>
            </div>

            </div>

        <div class="row marketing">
            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'col-lg-1',
                    'placeholder' => 'От',
                    'class' => 'form-control int'
                );
                echo Form::input('from3', $settings['from3'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'col-lg-1',
                    'placeholder' => 'До',
                    'class' => 'form-control int'
                );
                echo Form::input('to3', $settings['to3'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'form-control',
                    'style' => 'width: 200px;'
                );
                $options = array(
                    '1' => 'Клиент оплачивает все',
                    '2' => 'Магазин оплачивает все',
                    '3' => 'Магазин оплачивает процент от стоимости доставки',
                    '4' => 'Магазин оплачивает конкретную сумму от доставки. Если сумма больше, то всю доставку<'
                );
                echo Form::select('val3', $options, $settings['val3'], $attrs);
                ?>
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'placeholder' => 'Сумма',
                    'class' => 'form-control int'
                );
                echo Form::input('sum3', $settings['sum3'], $attrs);
                ?>
            </div>
        </div>

        <div class="row marketing">
            <div class="col-lg-3">
                Округление цены доставки для покупателя
            </div>
            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'class' => 'form-control',
                    'style' => 'width: 200px;'
                );
                $options = array(
                    '1' => 'Округлять в меньшую сторону',
                    '2' => 'Округлять в большую сторону',
                    '3' => 'Округлять цену математически',
                );
                echo Form::select('okrugl', $options, $settings['okrugl'], $attrs);
                ?>
            </div>
            <div class="col-lg-3" style="text-align: center">
                Шаг
            </div>

            <div class="col-lg-3">
                <?php
                $attrs = array(
                    'placeholder' => 'Шаг',
                    'class' => 'form-control float'
                );
                echo Form::input('shag', $settings['shag'], $attrs);
                ?>
            </div>


        </div>
        <div class="row marketing">
            <p class="bg-success" >
                В некоторых случаях есть необходимость включить цену забора
            </p>
            <label class="checkbox-inline">
                <?php
                echo Form::checkbox('zabor', '1', ( $settings['zabor'] )? true : false );
                ?>
                Выводить стоимость забора в цене доставки
            </label>
        </div>

   </div>


    <div class="footer">
        <p>&copy; ddelivery 2014</p>
    </div>
</form>
</div> <!-- /container -->


