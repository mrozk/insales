<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @var array $config Global blog configuration
 * @var Model_User $user Global Kohana user object
 *
 * @author     Novichkov Sergey(Radik) <novichkovsergey@yandex.ru>
 * @copyright  Copyrights (c) 2012 Novichkov Sergey
 */
?>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav pull-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Authorized as: <?php //echo $user->username ?></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo URL::site('/admin/auth/logout') ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="container jumbotron">
<?php
    foreach($usr_ins as $user)
    {
        $insales_api = InsalesApi::insales_api_client($user['shop'], 'ddelivery', $user['passwd']);
        /*
        $arr = array("delivery-variant"=>array("title" => 'Курьером', 'description' => 'Супер быстрая доставка',
        array('delivery-locations-attributes'=> array('delivery-location'=>array('region'=>'Респ Адыгея',
        'city' => 'Майкоп')))), 'type' => 'DeliveryVariant::PriceDepend');
        */
        $xmlstring =
        '<?xml version="1.0" encoding="UTF-8"?>
<product>
    <category-id type="integer">478</category-id>
    <title>Van Gogh Ruled Peach Notebook</title>
    <description>asdasdasd</description>
    <short-description>Алая записная книжка "Ван Гог" в линейку</short-description>
    <properties-attributes type="array">
        <properties-attribute>
            <title>Бумага</title>
            <value>в линейку</value>
        </properties-attribute>
    </properties-attributes>
    <variants-attributes type="array">
        <variant>
            <sku>QP021MVEN-r</sku>
            <quantity type="integer" nil="true"/>
            <price type="decimal">740.0</price>
            <cost-price type="decimal">487.0</cost-price>
            <old-price type="decimal" nil="true"/>
        </variant>
    </variants-attributes>
</product>';
        $xml = simplexml_load_string($xmlstring);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $orders = $insales_api('POST', '/admin/delivery_variants.xml', $array);
        var_dump($orders);
        //open_basedir is set
    }
?>
</div>

<style type="text/css">
    .jumbotron{ padding-top: 40px; }
</style>