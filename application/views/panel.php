<?php defined('SYSPATH') or die('No direct script access.');?>
<style type="text/css">
    .form-group{
        margin-top: 10px;
        clear: both;
    }
</style>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="javascript:void(0);">Панель настроек Insales - DDelivery</a>
        </div>
        <div class="navbar-collapse collapse">
            <div class="navbar-form navbar-right">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
            <!--
            <form class="navbar-form navbar-right" role="form">
                <div class="form-group">
                    <input type="text" placeholder="Email" class="form-control">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Password" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Sign in</button>
            </form>
            -->
        </div><!--/.navbar-collapse -->
    </div>
</div>

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
<div class="container" style="margin-top: 60px;">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-md-10">
            <h1>Настройки</h1>
            <form role="form">
                <h3>Основые</h3>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">API ключ из личного кабинета</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="API ключ из личного кабинета">
                    </div>
                </div>
                <div class="form-group">
                    <label for="rezhim" class="col-sm-6 control-label">Режим работы</label>
                    <div class="col-sm-10">
                        <select id="rezhim" class="form-control">
                            <option value="">Тестирование (stage.ddelivery.ru)</option>
                            <option value="">Продакшн (cabinet.ddelivery.ru)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="percent" class="col-sm-6 control-label">Какой % от стоимости товара страхуется</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="percent" placeholder="Какой % от стоимости товара страхуется">
                    </div>
                </div>
                <div class="form-group">
                    <h3>Соответствие полей</h3>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-6 control-label">Ширина</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Ширина">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">Длина</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Длина">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">Высота</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Высота">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">Вес</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Вес">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="rezhim" class="col-sm-6 control-label">Статус для отправки</label>
                    <div class="col-sm-10">
                        <select id="rezhim" class="form-control">
                            <option value="">Статус</option>
                            <option value="">Статус 2</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-6 control-label">Фамилия</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Вес">
                    </div>
                </div>

                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">Имя</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Вес">
                    </div>
                </div>
                <div class="form-group" >
                    <h3>Габариты по умолчанию</h3>
                </div>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">Ширина, см</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Ширина">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">Длина, см</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Длина">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="inputEmail3" class="col-sm-6 control-label">Высота, см</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Высота">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-6 control-label">Вес</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Вес">
                    </div>
                </div>

                <div class="form-group">
                    <h3>Настройка способов доставки</h3>
                </div>
                <div class="form-group" >
                    <label for="rezhim" class="col-sm-6 control-label">Доступные способы</label>
                    <div class="col-sm-10">
                        <select id="rezhim" class="form-control">
                            <option value="">Статус</option>
                            <option value="">Статус 2</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <h4>Дотупные компании ПВЗ</h4>
                </div>
                <div class="form-group" >
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> DPD
                    </label>
                </div>

                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> IML
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> Hermes-dpd
                    </label>
                </div>


                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> Logibox
                    </label>
                </div>


                <div class="form-group">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> Pickpoint
                    </label>
                </div>

                <div class="form-group" >
                    <h4>Дотупные компании Курьерская доставка</h4>
                </div>

                <div class="form-group" >
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> DPD
                    </label>
                </div>


                <div class="form-group" >
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> СДЭК
                    </label>
                </div>


                <div class="form-group" >
                    <label class="checkbox-inline">
                        <input type="checkbox" id="inlineCheckbox1" value="option1"> IML
                    </label>
                </div>

                <div class="form-group" >
                    <h3>Настройка цены доставки</h3>
                </div>

                <div class="navbar-form" >
                    <div class="form-group">
                     От
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>
                    <div class="form-group">
                         До
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>
                    <div class="form-group">
                        <select id="rezhim" class="form-control">
                            <option value="">Статус</option>
                            <option value="">Статус 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>

                    <div class="form-group">
                        Сума
                    </div>

                    <div class="form-group">
                        <input type="password" placeholder="Password" class="form-control">
                    </div>
                </div>

                <div class="navbar-form" >
                    <div class="form-group">
                        От
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>
                    <div class="form-group">
                        До
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>
                    <div class="form-group">
                        <select id="rezhim" class="form-control">
                            <option value="">Статус</option>
                            <option value="">Статус 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>

                    <div class="form-group">
                        Сума
                    </div>

                    <div class="form-group">
                        <input type="password" placeholder="Password" class="form-control">
                    </div>
                </div>


                <div class="navbar-form" >
                    <div class="form-group">
                        От
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>
                    <div class="form-group">
                        До
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>
                    <div class="form-group">
                        <select id="rezhim" class="form-control">
                            <option value="">Статус</option>
                            <option value="">Статус 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email" class="form-control">
                    </div>

                    <div class="form-group">
                        Сума
                    </div>

                    <div class="form-group">
                        <input type="text" placeholder="Password" class="form-control">
                    </div>
                </div>

                <div class="navbar-form" >
                    <div class="form-group">
                        Округление цены доставки для покупателя
                    </div>

                    <div class="form-group">
                        <select name="okrugl" id="okrugl" class="form-control">
                            <option value="1">Округлять в меньшую сторону</option>
                            <option value="2">Округлять в большую сторону</option>
                            <option value="3">Округлять цену в математически</option>
                        </select>
                    </div>

                    <div class="form-group">
                        Шаг
                    </div>

                    <div class="form-group">
                        <input type="text" placeholder="shag" name="shag" classa="form-control">
                    </div>

                    <div class="form-group" >
                        <label class="checkbox-inline">
                            <input type="checkbox" id="zabor" name="zabor" value="1"> Выводить стоимость забора в
                                                                                         цене доставки
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
        </div>
        <!--
        <div class="col-md-4">

            <h2>Heading</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>

        </div>
        -->
        <!--
        <div class="col-md-4">

            <h2>Heading</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>

        </div>
        -->
    </div>

    <hr>

    <footer>
        <p>&copy; Company 2014</p>
    </footer>
</div> <!-- /container -->