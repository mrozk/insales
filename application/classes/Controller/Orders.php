<?php defined('SYSPATH') or die('No direct script access.');

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/example/IntegratorShop.php');

class Controller_Orders extends Controller
{
    public function action_index()
    {
        echo Kohana::VERSION ;
    }

    public function action_update()
    {
        if (!isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = file_get_contents("php://input");
            $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
                ->values( array( $HTTP_RAW_POST_DATA, "asdsd") )->execute();
            $data = json_decode( $HTTP_RAW_POST_DATA );
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
                        $IntegratorShop = new IntegratorShop( $this->request, $user_id );
                        $ddeliveryUI = new DDeliveryUI( $IntegratorShop, true );
                        $ddeliveryUI->onCmsChangeStatus( $data->order_lines[0]->order_id, $data->fulfillment_status );
                    }
                }
            }
            return $HTTP_RAW_POST_DATA;

    }

    public function action_create()
    {
        if (!isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = file_get_contents("php://input");

        $data = json_decode( $HTTP_RAW_POST_DATA );

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

            if( $ddelivery_id && $user_id )
            {
                $insales_user = ORM::factory('InsalesUser', array('id' => $user_id));
                if($insales_user->loaded())
                {
                    if( $data->delivery_variant_id == $insales_user->delivery_variant_id )
                    {
                        try
                        {
                            $IntegratorShop = new IntegratorShop( $this->request, $user_id );
                            $ddeliveryUI = new DDeliveryUI($IntegratorShop, true);
                            $ddeliveryUI->onCmsOrderFinish( $ddelivery_id, $data->order_lines[0]->order_id,
                                          $data->fulfillment_status, $data->payment_gateway_id );
                        }
                        catch( \DDelivery\DDeliveryException $e )
                        {
                            echo $e->getMessage();
                            return;
                        }
                    }
                }

            }
        }
        /*
        $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
            ->values( array( $HTTP_RAW_POST_DATA, "asdsd") )->execute();



        if( $data->delivery_variant_id == 221842 )
        {
            foreach( $data->fields_values as $item )
            {
                if ( $item->name == 'ddelivery_id' && !empty( $item->value ))
                {
                    try
                    {
                        $IntegratorShop = new IntegratorShop( $this->request, 136789 );
                        $ddeliveryUI = new DDeliveryUI($IntegratorShop, true);
                        $ddeliveryUI->onCmsOrderFinish( $item->value, $data->order_lines[0]->order_id,
                                                        $data->fulfillment_status, $data->payment_gateway_id );
                    }
                    catch( \DDelivery\DDeliveryException $e )
                    {
                        echo $e->getMessage();
                        return;
                    }
                    $mag_id = (int)$this->request->query('mag_id');
                    $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
                            ->values( array( $mag_id, "asdsd") )->execute();

                }
            }
        }
        */
        /*
        $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
            ->values(array($HTTP_RAW_POST_DATA, "asdsd"))->execute();
        */
        return $HTTP_RAW_POST_DATA;

    }

    public function action_get()
    {
        $query = DB::select()->from('ordddd')->as_object()->execute();
        //print_r($query);

        $query = DB::query(Database::SELECT, 'SELECT * FROM ordddd WHERE id = 77');
        //$query->param(':user', 'john');
        $query->as_object();
        $return = $query->execute();
        //echo $return[0]->orderer;
        //print_r( $return[0] );
        $data = json_decode( $return[0]->creater );

        echo '<pre>';
           //print_r($data);
        echo '</pre>';
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
                    $IntegratorShop = new IntegratorShop( $this->request, $user_id );
                    $ddeliveryUI = new DDeliveryUI( $IntegratorShop, true );
                    echo $ddeliveryUI->onCmsChangeStatus( $data->order_lines[0]->order_id, $data->fulfillment_status );
                }
            }
        }
        /*
        if( $ddelivery_id && $user_id )
        {
            $insales_user = ORM::factory('InsalesUser', array('id' => $user_id));
            if($insales_user->loaded())
            {
                if( $data->delivery_variant_id == $insales_user->delivery_variant_id )
                {
                    try
                    {
                        $IntegratorShop = new IntegratorShop( $this->request, $user_id );
                        $ddeliveryUI = new DDeliveryUI($IntegratorShop, true);
                        $ddeliveryUI->onCmsOrderFinish( $item->value, $data->order_lines[0]->order_id,
                            $data->fulfillment_status, $data->payment_gateway_id );
                    }
                    catch( \DDelivery\DDeliveryException $e )
                    {
                        echo $e->getMessage();
                        return;
                    }
                }
            }
        }
        */

        //echo $method_id . '<br />';
        /*
        echo $ddelivery_id;
        echo $query[0]->insalesuser_id;
        */
        /*
        //echo $creator->delivery_variant_id;
        if( $creator->delivery_variant_id == 221842 )
        {
            foreach( $creator->fields_values as $item )
            {
                if ( $item->name == 'ddelivery_id')
                {
                    echo $item->value;
                }
            }
        }
        */
        /*
        foreach( $return as $item )
        {
            $creator = json_decode( $item->creater );
            print_r($creator);
            echo '<hr />';

        }
        */

    }
}