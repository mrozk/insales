<?php defined('SYSPATH') or die('No direct script access.');

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/mrozk/IntegratorShop.php');

class Controller_Sdk extends Controller
{



    public function get_request_state( $name )
    {
        $session = Session::instance();
        $query = $this->request->query($name);
        if( !empty( $query ) )
        {
            $session->set( $name, $query );
            return $query;
        }
        else
        {
            return $session->get( $name );
        }
    }



    public function action_status()
    {
        $uid = (int)$this->get_request_state('insales_id');
        if( !$uid )
        {
            return;
        }
        $IntegratorShop = new IntegratorShop( $this->request, $uid );
        $ddeliveryUI = new DDeliveryUI($IntegratorShop);


        $orders = $ddeliveryUI->getUnfinishedOrders();
        $statusReport = array();
        if( count( $orders ) )
        {
            foreach ( $orders as $item)
            {
                $rep = $this->changeInsalesOrderStatus( $item, $ddeliveryUI );
                if( count( $rep ) )
                {
                    $statusReport[] = $rep;
                }
            }
        }
        return $statusReport;

    }

    public function action_test()
    {
            $session = Session::instance();
            $insalesuser = (int)$session->get('insales_id');
            if( !$insalesuser )
            {
                return;
            }
            $insales_user = ORM::factory('InsalesUser', array('id' => $insalesuser));

            if ( $insales_user->loaded() )
            {
                $insales_api =  new InsalesApi(  $insales_user->passwd,  $insales_user->shop );
                $pulet = '<field>
                                <active type="boolean">true</active>
                                <destiny type="integer">1</destiny>
                                <for-buyer type="boolean">true</for-buyer>
                                <obligatory type="boolean">false</obligatory>
                                <office-title>Улица</office-title>
                                <position type="integer">4</position>
                                <show-in-checkout type="boolean">true</show-in-checkout>
                                <show-in-result type="boolean">true</show-in-result>
                                <system-name>street</system-name>
                                <title>Улица</title>
                                <example></example>
                                <type>Field::TextField</type>
                           </field>';
                $result =  $insales_api->api('DELETE','/admin/fields/1711751.xml', $pulet);
                echo '<pre>';
                    print_r($result);
                echo '</pre>';
            }
    }

    public function setInsalesOrderStatus($cmsOrderID, $status, $clientID)
    {
        $insales_user = ORM::factory('InsalesUser', array('id' => $clientID));

        if ( $insales_user->loaded() )
        {

            $insales_api =  new InsalesApi( $insales_user->passwd, $insales_user->shop );

            $pulet = '<order>
                            <id type="integer">' . $cmsOrderID . '</id>
                            <fulfillment-status>' . $status . '</fulfillment-status>
                      </order>';
            //echo strlen($pulet);
            //echo $cmsOrderID;
            $result = json_decode( $insales_api->api('PUT','/admin/orders/' . $cmsOrderID . '.json', $pulet) );
            return $result->id;
        }
    }

    public function changeInsalesOrderStatus( $order, $ui )
    {
        if( $order )
        {
            if( $order->ddeliveryID == 0 )
            {
                return array();
            }
            $ddStatus = $ui->getDDOrderStatus($order->ddeliveryID);

            if( $ddStatus == 0 )
            {
                return array();
            }
            $order->ddStatus = $ddStatus;
            $order->localStatus = $this->getLocalStatusByDD( $order->ddStatus );
            $ui->saveFullOrder($order);
            $this->setInsalesOrderStatus($order->shopRefnum, $order->localStatus, $order->insalesuser_id);
            return array('cms_order_id' => $order->shopRefnum, 'ddStatus' => $order->ddStatus,
                'localStatus' => $order->localStatus );
        }
        else
        {
            return array();
        }
    }

    public function action_index()
    {
        try
        {
            $address = $this->get_request_state('address');
            $client_name = $this->get_request_state('client_name');
            $client_phone = $this->get_request_state('client_phone');

            $house = $this->get_request_state('house');
            $street = $this->get_request_state('street');
            $flat = $this->get_request_state('flat');
            $corp = $this->get_request_state('corp');

            $this->request->query('address',$address);
            $this->request->query('client_name',$client_name);
            $this->request->query('client_phone',$client_phone);

            $this->request->query('house',$house);
            $this->request->query('street',$street);
            $this->request->query('flat',$flat);
            $this->request->query('corp',$corp);

            $uid = (int)$this->get_request_state('insales_id');
            if( !$uid )
            {
                return;
            }
            $IntegratorShop = new IntegratorShop( $this->request, $uid );
            $ddeliveryUI = new DDeliveryUI($IntegratorShop);
            $order = $ddeliveryUI->getOrder();
            $order->insalesuser_id = $uid;

            $ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());
            //echo '</pre>';
        }
        catch( \DDelivery\DDeliveryException $e )
        {
            echo $e->getMessage();
            return;
        }

        /*
        $order->city = 151185;
        $session = Session::instance();
        $cart = $session->get('cart');
        print_r( $cart );
        */
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