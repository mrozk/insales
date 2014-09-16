<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @var array $config Global blog configuration
 * @var Model_User $user Global Kohana user object
 *
 * @author     Novichkov Sergey(Radik) <novichkovsergey@yandex.ru>
 * @copyright  Copyrights (c) 2012 Novichkov Sergey
 */
?>

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
                <a  class="navbar-brand active" style="color: #fff" href="javascript:void(0)">Админка DDelivery Insales</a>
            </div>

            <div style="margin-top: 5px;">
                <?php
                /*
                <a  class="btn btn-warning"  href="#>">Вернуться в кабинет</a>
                <a class="btn btn-success" href="#">Добавить способ доставки в Insales</a>
                <?php */
                ?>
                <div style="text-align: right">
                    <a class="btn btn-success" onclick="" href="/admin/auth/logout/">Выход</a>
                </div>
            </div>
        </div>
    </div>



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

    <div class="page-header">
        <h3>Список пользователей</h3>
    </div>

    <div class="row marketing">
        <div class="row" style="text-align: center">
            <div class="col-md-1"><h4>ID</h4></div>
            <div class="col-md-4"><h4>Магазин</h4></div>
            <div class="col-md-1"><h4>Insales ID</h4></div>
            <div class="col-md-4"><h4>API ключ DDelivery</h4></div>
        </div>
            <?php
            if( count($insalesusers) > 0 ){
                foreach( $insalesusers as $item ){
                    $settings = json_decode($item->settings);
            ?>
                    <div class="row" style="text-align: center">
                            <div class="col-md-1"><?php echo $item->id;?></div>
                            <div class="col-md-4"><a href="/admin/main/user/?id=<?php echo $item->id; ?>" class=""><?php echo $item->shop;?></a></div>
                            <div class="col-md-1"><?php echo $item->insales_id;?></div>
                            <div class="col-md-4"><?php echo $settings->api;?></div>
                    </div>
            <?php
                }
            }
            ?>



    </div>
</div>