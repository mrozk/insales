<?php

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/example/IntegratorShop.php');

class Controller_Sdk extends Controller
{
    public function action_index()
    {
        $IntegratorShop = new IntegratorShop();
        $ddeliveryUI = new DDeliveryUI($IntegratorShop);
        $ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());
        // В зависимости от параметров может выводить полноценный html или json
        //echo $ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());
    }
}