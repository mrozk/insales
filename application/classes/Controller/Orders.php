<?php defined('SYSPATH') or die('No direct script access.');

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/mrozk/IntegratorShop.php');

class Controller_Orders extends Controller
{
    public function action_index(){
        echo Kohana::VERSION ;
    }

    public function action_update(){
        if (!isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = file_get_contents("php://input");
        /*
        $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
            ->values(array($HTTP_RAW_POST_DATA, "asdsd"))->execute();
        */
            $data = json_decode( $HTTP_RAW_POST_DATA );

            if( count( $data->fields_values ) ){
                foreach( $data->fields_values as $item ){
                    if ( $item->name == 'ddelivery_id' && !empty( $item->value )){
                        $ddelivery_id = (int) $item->value;
                    }
                    if ( $item->name == 'ddelivery_insales' && !empty( $item->value )){
                        $user_id = (int)$item->value;
                    }
                }
            }

            if( $ddelivery_id && $user_id ){
                $insales_user = ORM::factory('InsalesUser', array('id' => $user_id));
                if($insales_user->loaded()){
                    if( $data->delivery_variant_id == $insales_user->delivery_variant_id  ){
                        $settings = json_decode($insales_user->settings );
                        try{
                            $IntegratorShop = new IntegratorShop( $this->request, $settings );
                            $ddeliveryUI = new DDeliveryUI( $IntegratorShop, true );

                            $query = DB::select('id')->from('ddelivery_orders')->
                                     where( 'add_field1', '=', $user_id )->and_where('shop_refnum', '=', $data->number)->as_object()->execute();
                            if( count($query) ){
                                $order = $ddeliveryUI->initOrder( $query[0]->id );
                                if( $order->localId ){
                                    if( $IntegratorShop->isStatusToSendOrder( $data->fulfillment_status ) && $order->ddeliveryID == 0 ){
                                        if($order->type == \DDelivery\Sdk\DDeliverySDK::TYPE_SELF){
                                            $ddeliveryUI->createSelfOrder($order);
                                        }
                                        elseif( $order->type ==  \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER ){
                                            $ddeliveryUI->createCourierOrder($order);
                                        }
                                    }
                                }
                            }
                        }catch (\DDelivery\DDeliveryException $e){
                            $ddeliveryUI->logMessage($e);
                        }
                    }
                }
            }
            return $HTTP_RAW_POST_DATA;

    }

    public function action_create(){
        if (!isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = file_get_contents("php://input");
        /*
        $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
            ->values(array($HTTP_RAW_POST_DATA, "asdsd"))->execute();
        */

        $data = json_decode( $HTTP_RAW_POST_DATA );

        if( count( $data->fields_values ) ){

            foreach( $data->fields_values as $item ){
                if ( $item->name == 'ddelivery_id' && !empty( $item->value )){
                    $ddelivery_id = (int) $item->value;
                }
                if ( $item->name == 'ddelivery_insales' && !empty( $item->value )){
                    $user_id = (int)$item->value;
                }
            }

            if( $ddelivery_id && $user_id ){
                $insales_user = ORM::factory('InsalesUser', array('id' => $user_id));
                if($insales_user->loaded()){
                    if( $data->delivery_variant_id == $insales_user->delivery_variant_id ){
                        try{
                            $settings = json_decode($insales_user->settings );
                            $IntegratorShop = new IntegratorShop( $this->request, $settings );

                            $ddeliveryUI = new DDeliveryUI($IntegratorShop, true);
                            $ddeliveryUI->onCmsOrderFinish( $ddelivery_id, $data->number,
                                                            $data->fulfillment_status, $data->payment_gateway_id );
                            $order = $ddeliveryUI->initOrder($ddelivery_id);
                                    $order->firstName = $data->shipping_address->name;
                                    $order->toEmail = $data->client->email;
                                    $order->toPhone = $data->shipping_address->phone;

                            if( $order->type == \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER ){
                                   $toFlat = $this->findInArray( $data->shipping_address->fields_values,  $settings->address['flat'] );
                                   $order->toFlat = (($toFlat != '')?$toFlat:$order->toFlat);

                                   $toHousing = $this->findInArray( $data->shipping_address->fields_values,  $settings->address['corp'] );
                                   $order->toHousing = (($toHousing != '')?$toHousing:$order->toHousing);

                                   $toHouse =  $this->findInArray( $data->shipping_address->fields_values,  $settings->address['house'] );
                                   $order->toHouse =  (($toHouse != '')?$toHouse:$order->toHouse);

                                   $toStreet = $this->findInArray( $data->shipping_address->fields_values, $settings->address['street']);
                                   $order->toStreet = (($toStreet != '')?$toStreet:$order->toStreet);
                            }
                            $ddeliveryUI->saveFullOrder($order);
                        }
                        catch( \DDelivery\DDeliveryException $e ){

                            $ddeliveryUI->logMessage($e);
                            return;
                        }
                    }
                }

            }
        }

        return $HTTP_RAW_POST_DATA;

    }

    public function findInArray( $array, $value ){
        if( count($array) ){
            foreach( $array as $item ){
                if( ((int)$item->field_id) == ((int)$value)){
                    return $item->value;
                }
            }
        }
        return '';
    }

    public function action_get(){
        $query = DB::query(Database::SELECT, 'SELECT * FROM ordddd WHERE id =4467');

        //$query->param(':user', 'john');
        $query->as_object();
        $return = $query->execute();
        header('Content-Type: text/html; charset=utf-8');
        $data = json_decode( $return[0]->creater );
        $IntegratorShop = new IntegratorShop2( );
        $ddeliveryUI = new DDeliveryUI( $IntegratorShop, true );
        //$ddeliveryUI->initOrder($data);
        echo '<pre>';
            print_r(  $data );
        echo '</pre>';
        /*
        $query = DB::select('id')->from('ddelivery_orders')->
            where( 'id', '=', $user_id )->and_where('shop_refnum', '=', $data->number)->as_object()->execute();
        */
        $ddelivery_id = 0;
        $user_id  = 0;
        if( count( $data->fields_values ) )
        {
            foreach( $data->fields_values as $item )
            {
                if ( $item->name == 'ddelivery_id' && !empty( $item->value ))
                {
                    $ddelivery_id = (int) $item->value;
                }
                if ( $item->name == 'ddelivery_insales' && !empty( $item->value ))
                {
                    $user_id = (int)$item->value;
                }
            }
        }

        if( $ddelivery_id && $user_id )
        {

            $insales_user = ORM::factory('InsalesUser', array('id' => $user_id));
            if($insales_user->loaded())
            {

                if( $data->delivery_variant_id == $insales_user->delivery_variant_id  )
                {
                    echo 'xxx';
                    try{
                        $IntegratorShop = new IntegratorShop( $this->request, $user_id );
                        $ddeliveryUI = new DDeliveryUI( $IntegratorShop, true );


                        $query = DB::select('id')->from('ddelivery_orders')->
                            where( 'add_field1', '=', $user_id )->and_where('shop_refnum', '=', $data->number)->as_object()->execute();
                        echo 'xxxx';
                        if( count($query) ){

                            $orders = $ddeliveryUI->initOrder( $query[0]->id );
                            print_r($orders);
                            if( $IntegratorShop->isStatusToSendOrder( $data->fulfillment_status )){
                                if($orders[0]->type == \DDelivery\Sdk\DDeliverySDK::TYPE_SELF)
                                {
                                    //echo $ddeliveryUI->createSelfOrder($orders[0]);
                                }
                                elseif( $orders[0]->type ==  \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER )
                                {
                                    //echo $ddeliveryUI->createCourierOrder($orders[0]);
                                }
                            }
                        }
                    }catch (\DDelivery\DDeliveryException $e){
                        $ddeliveryUI->logMessage($e);
                        echo $e->getMessage();
                    }
                }
            }
        }


    }
}