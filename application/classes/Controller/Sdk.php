<?php

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/example/IntegratorShop.php');

class Controller_Sdk extends Controller
{

    public function action_takecart()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 1000');

        $cart = $this->request->post('str');
        $session = Session::instance();
        $session->set( 'cart', $cart );
        echo 'jsonCallback(
            {
                "sites": "'  .  $cart . '"
            }
        );
        ';
    }

    public function action_index()
    {

        echo $this->request->post('cart');
        $IntegratorShop = new IntegratorShop( $this->request );
        $ddeliveryUI = new DDeliveryUI($IntegratorShop);
        $order = $ddeliveryUI->getOrder();
        $order->city = 151185;
        $session = Session::instance();
        $cart = $session->get('cart');
        print_r( $cart );
        //print_r( $ddeliveryUI->getCourierPointsForCity($order) );
        //print_r($ddeliveryUI->getSelfPoints( $order ));

        //$ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());
        //$order = $ddeliveryUI->getOrder();
        //$order->city =
        //$ddeliveryUI->getSelfPoints();
        //echo json_encode(array('komoro'));
        // В зависимости от параметров может выводить полноценный html или json
        //$ddelivery    UI->render(isset($_REQUEST) ? $_REQUEST : array());
        //echo strlen('{"comment":"","delivery_date":null,"delivery_description":"DDelivery (DDelivery)","delivery_from_hour":null,"delivery_price":0.0,"delivery_title":"DDelivery","delivery_to_hour":null,"number":null,"payment_description":null,"payment_title":null,"items_count":1,"items_price":12600.0,"order_lines":[{"added_at":1,"product_id":27913632,"quantity":1,"sku":null,"title":"Samsung Galaxy Tab 2 7.0 P3100 8Gb","variant_id":42474764,"weight":null,"image_url":"http://static2.insales.ru/images/products/1/5271/32822423/thumb_i_2_.jpg","sale_price":12600.0}],"discounts":[],"total_price":12600.0}') ;
    }
}