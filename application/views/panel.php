<?php defined('SYSPATH') or die('No direct script access.');?>
<style type="text/css">
    .form-group{
        margin-top: 10px;
        clear: both;
    }
</style>



<!-- Main jumbotron for a primary marketing message or call to action -->
<!--
<div class="jumbotron">
    <div class="container">
        <h1>Hello, world!</h1>
        <p>This is a template for a simple marketing or informational website. It includes a large callout called a jumbotron and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
        <p><a class="btn btn-primary btn-lg" role="button">Learn more &raquo;</a></p>
    </div>
</div>
-->
    <?php

        //print_r($usersettings->usersetting);

    ?>
            <h1>Настройки</h1>
            <form role="form" id="insales-form" action="cabinet/save/" method="post">
                <h3>Основые</h3>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">API ключ из личного кабинета</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'API ключ из личного кабинета',
                                'class' => 'form-control'
                            );
                            echo Form::input('api', $usersettings->usersetting->api, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="rezhim" class="col-sm-6 control-label">Режим работы</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'class' => 'form-control'
                            );
                            $options = array(
                                '1' => 'Тестирование (stage.ddelivery.ru)',
                                '2' => 'Продакшн (cabinet.ddelivery.ru)'
                            );
                            echo Form::select('rezhim', $options, $usersettings->usersetting->rezhim, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="declared" class="col-sm-6 control-label">Какой % от стоимости товара страхуется</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Какой % от стоимости товара страхуется',
                                'class' => 'form-control'
                            );
                            echo Form::input('declared', $usersettings->usersetting->declared, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <h3>Соответствие полей</h3>
                </div>
                <div class="form-group">
                    <label for="width" class="col-sm-6 control-label">Ширина</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Ширина',
                                'class' => 'form-control'
                            );
                            echo Form::input('width', $usersettings->usersetting->width, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="length" class="col-sm-6 control-label">Длина</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Длина',
                                'class' => 'form-control'
                            );
                            echo Form::input('length', $usersettings->usersetting->length, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="height" class="col-sm-6 control-label">Высота</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Высота',
                                'class' => 'form-control'
                            );
                            echo Form::input('height', $usersettings->usersetting->height, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="weight" class="col-sm-6 control-label">Вес</label>
                    <div class="col-sm-10">
                        <?php
                             $attrs = array(
                                 'placeholder' => 'Вес',
                                 'class' => 'form-control'
                             );
                            echo Form::input('weight', $usersettings->usersetting->weight, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="status" class="col-sm-6 control-label">Статус для отправки</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'class' => 'form-control'
                            );
                            $options = array(
                                '1' => 'Статус',
                                '2' => 'Статус 2'
                            );
                            echo Form::select('status', $options, $usersettings->usersetting->status, $attrs);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="secondname" class="col-sm-6 control-label">Фамилия</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Фамилия',
                                'class' => 'form-control'
                            );
                            echo Form::input('secondname', $usersettings->usersetting->secondname, $attrs);
                        ?>
                    </div>
                </div>

                <div class="form-group" >
                    <label for="firstname" class="col-sm-6 control-label">Имя</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Имя',
                                'class' => 'form-control'
                            );
                            echo Form::input('firstname', $usersettings->usersetting->firstname, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <h3>Габариты по умолчанию</h3>
                </div>
                <div class="form-group" >
                    <label for="plan_width" class="col-sm-6 control-label">Ширина, см</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Ширина, см',
                                'class' => 'form-control'
                            );
                            echo Form::input('plan_width', $usersettings->usersetting->plan_width, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="plan_length" class="col-sm-6 control-label">Длина, см</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Длина, см',
                                'class' => 'form-control'
                            );
                            echo Form::input('plan_lenght', $usersettings->usersetting->plan_lenght, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="plan_height" class="col-sm-6 control-label">Высота, см</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Высота, см',
                                'class' => 'form-control'
                            );
                            echo Form::input('plan_height', $usersettings->usersetting->plan_height, $attrs);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="plan_weight" class="col-sm-6 control-label">Вес</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Вес, кг',
                                'class' => 'form-control'
                            );
                            echo Form::input('plan_weight', $usersettings->usersetting->plan_weight, $attrs);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <h3>Настройка способов доставки</h3>
                </div>
                <div class="form-group" >
                    <label for="avalible_way" class="col-sm-6 control-label">Доступные способы</label>
                    <div class="col-sm-10">
                        <?php
                            $attrs = array(
                                'class' => 'form-control'
                            );
                            $options = array(
                                '1' => 'ПВЗ и Курьеры',
                                '2' => 'ПВЗ',
                                '3' => 'Курьеры'
                            );
                            echo Form::select('type', $options, $usersettings->usersetting->type, $attrs);
                        ?>

                    </div>
                </div>
                <div class="form-group">
                    <h4>Дотупные компании ПВЗ</h4>
                </div>
                <div class="form-group" >
                    <label class="checkbox-inline">
                        <?php

                            echo Form::checkbox('pvz_companies[]', '1', false );
                        ?>
                         DPD
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-inline">
                        <?php
                            echo Form::checkbox('pvz_companies[]', '2', false );
                        ?>
                        IML
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        <?php
                            echo Form::checkbox('pvz_companies[]', '3', false );
                        ?>
                        Hermes-dpd
                    </label>
                </div>


                <div class="form-group">
                    <label class="checkbox-inline">
                        <?php
                            echo Form::checkbox('pvz_companies[]', '4', false );
                        ?>
                        Logibox
                    </label>
                </div>


                <div class="form-group">
                    <label class="checkbox-inline">
                        <?php
                            echo Form::checkbox('pvz_companies[]', '5', false );
                        ?>
                        Pickpoint
                    </label>
                </div>

                <div class="form-group" >
                    <h4>Дотупные компании Курьерская доставка</h4>
                </div>

                <div class="form-group" >
                    <label class="checkbox-inline">
                        <?php
                            echo Form::checkbox('cur_companies[]', '1', false );
                        ?>
                        DPD
                    </label>
                </div>


                <div class="form-group" >
                    <label class="checkbox-inline">
                        <?php
                            echo Form::checkbox('cur_companies[]', '2', false );
                        ?>
                        СДЭК
                    </label>
                </div>


                <div class="form-group" >
                    <label class="checkbox-inline">
                        <?php
                            echo Form::checkbox('cur_companies[]', '3', false );
                        ?>
                        IML
                    </label>
                </div>

                <div class="form-group" >
                    <h3>Настройка цены доставки</h3>
                </div>

                <div class="navbar-form" >

                    <div class="form-group">
                        <?php
                            $attrs = array(
                                'placeholder' => 'От',
                                'class' => 'form-control'
                            );
                            echo Form::input('from1', $usersettings->usersetting->from1, $attrs);
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                            $attrs = array(
                                'placeholder' => 'До',
                                'class' => 'form-control'
                            );
                            echo Form::input('to1', $usersettings->usersetting->to1, $attrs);
                        ?>
                    </div>
                    <div class="form-group">
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
                            echo Form::select('val1', $options, $usersettings->usersetting->val1, $attrs);
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Сума',
                                'class' => 'form-control'
                            );
                            echo Form::input('sum1', $usersettings->usersetting->sum1, $attrs);
                        ?>
                    </div>
                </div>

                <div class="navbar-form" >
                    <div class="form-group">
                        <?php
                        $attrs = array(
                            'placeholder' => 'От',
                            'class' => 'form-control'
                        );
                        echo Form::input('from2', $usersettings->usersetting->from2, $attrs);
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        $attrs = array(
                            'placeholder' => 'До',
                            'class' => 'form-control'
                        );
                        echo Form::input('to2', $usersettings->usersetting->to2, $attrs);
                        ?>
                    </div>
                    <div class="form-group">
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
                        echo Form::select('val2', $options, $usersettings->usersetting->val2, $attrs);
                        ?>
                    </div>


                    <div class="form-group">
                        <?php
                        $attrs = array(
                            'placeholder' => 'Сума',
                            'class' => 'form-control'
                        );
                        echo Form::input('sum2', $usersettings->usersetting->sum2, $attrs);
                        ?>
                    </div>
                </div>

                <div class="navbar-form" >
                    <div class="form-group">
                        <?php
                        $attrs = array(
                            'placeholder' => 'От',
                            'class' => 'form-control'
                        );
                        echo Form::input('from3', $usersettings->usersetting->from3, $attrs);
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        $attrs = array(
                            'placeholder' => 'До',
                            'class' => 'form-control'
                        );
                        echo Form::input('to3', $usersettings->usersetting->to3, $attrs);
                        ?>
                    </div>
                    <div class="form-group">
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
                            echo Form::select('val3', $options, $usersettings->usersetting->val2, $attrs);
                        ?>
                    </div>


                    <div class="form-group">
                        <?php
                        $attrs = array(
                            'placeholder' => 'Сума',
                            'class' => 'form-control'
                        );
                        echo Form::input('sum3', $usersettings->usersetting->sum2, $attrs);
                        ?>
                    </div>
                </div>

                <div class="navbar-form" >
                    <div class="form-group">
                        Округление цены доставки для покупателя
                    </div>

                    <div class="form-group">
                        <?php
                            $attrs = array(
                                'class' => 'form-control',
                                'style' => 'width: 200px;'
                            );
                            $options = array(
                                '1' => 'Округлять в меньшую сторону',
                                '2' => 'Округлять в большую сторону',
                                '3' => 'Округлять цену в математически',
                            );
                            echo Form::select('okrugl', $options, $usersettings->usersetting->okrugl, $attrs);
                        ?>
                    </div>

                    <div class="form-group">
                        Шаг
                    </div>

                    <div class="form-group">
                        <?php
                            $attrs = array(
                                'placeholder' => 'Шаг',
                                'class' => 'form-control'
                            );
                            echo Form::input('shag', $usersettings->usersetting->shag, $attrs);
                        ?>
                    </div>

                    <div class="form-group" >
                        <label class="checkbox-inline">
                            <?php
                                echo Form::checkbox('zabor', '1', false );
                            ?>
                            Выводить стоимость забора в цене доставки
                        </label>
                    </div>

                </div>

            </form>
            <!--
            <p><input type="password" placeholder="Password" class="form-control"></p>
            -->
            <!--
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
            -->
