<?php defined('SYSPATH') or die('No direct script access.');

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/mrozk/IntegratorShop.php');
include_once( APPPATH . 'classes/Sdk/mrozk/IntegratorShop2.php');

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
    public function getXmlJsToInsales( $insalesuser_id, $field_id, $field2_id)
    {
        return $payload = '<?xml version="1.0" encoding="UTF-8"?>
                            <delivery-variant>
                              <title>DDelivery</title>
                              <position type="integer">1</position>
                              <url>' . URL::base( $this->request ) . 'hello/gus/</url>
                              <description>DDelivery</description>
                              <type>DeliveryVariant::External</type>
                              <delivery-locations type="array"/>
                              <javascript>&lt;script type="text/javascript" src="' . URL::base( $this->request ) . 'html/js/ddelivery.js"&gt;&lt;/script&gt;

                                     &lt;script type="text/javascript"&gt;var ddelivery_insales={"field_id":' . $field_id . ',
                                     "field2_id":' . $field2_id . ',"_id":' . $insalesuser_id . ',
                                     "url": "' . URL::base( $this->request ) . '"
                                       };&lt;/script&gt;
                                    &lt;script type="text/javascript" src="' . URL::base( $this->request ) . 'html/action.js"&gt;&lt;/script&gt;
                                    &lt;div class="id_dd"&gt;&lt;/div&gt;
                              </javascript>
                              <price type="decimal">0</price>
                              <add-payment-gateways>true</add-payment-gateways>
                            </delivery-variant>';
    }
    public function action_orderinfo(){
        $order = $this->request->query('order');
        try{
            $IntegratorShop = new IntegratorShop2();
            $ddeliveryUI = new DDeliveryUI($IntegratorShop,true);
            $orders = $ddeliveryUI->getOrderByCmsID($order);
            if( count($orders) ){
                $answer = (($orders->ddeliveryID == 0)?'Заявка на DDelivery не отправлена':'Номер заявки на DDelivery - ' . $orders->ddeliveryID );
                echo "set_data({'ID заказа -" . $orders->shopRefnum . "':'" . $answer . "'});";
            }
        }catch (\DDeliveryException $e){
            echo $e->getMessage();
        }
    }
    public function action_test()
    {
            $session = Session::instance();
            $insalesuser = (int)$session->get('insalesuser');
            echo $insalesuser;

            if( !$insalesuser )
            {
                return;
            }

            $insales_user = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));

            if ( $insales_user->loaded() )
            {

                $insales_api =  new InsalesApi(  $insales_user->passwd,  $insales_user->shop );
               // print_r( $insales_api->api('GET','/admin/delivery_variants.xml') );
                $pulet = "<application-widget>
<code>
  &lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot;&gt;
  &lt;head&gt;
    &lt;meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=utf-8&quot;/&gt;
    &lt;style&gt;
      table#statuses {
        border-collapse: collapse;
        border-right: 1px solid black;
        border-left: 1px solid black;
      }
      table#statuses td, table#statuses th {
        border: 1px solid black;
      }
    &lt;/style&gt;
  &lt;/head&gt;
  &lt;body&gt;

    &lt;table id='statuses' style='border: 1px solid black;'&gt;

    &lt;/table&gt;

    &lt;script&gt;
      var data = {};
      // функция которая вызывается во внешнем js файле и устанавливает значение переменной data
      function set_data(input_object) {
        data = input_object;
      }
      var table = document.getElementById('statuses');

      // устанавливаем номер заказа, используя id из переменной window.order_info
      var order_number_field = document.getElementById('order_number');
      // order_number_field.innerHTML = window.order_info.id;
      fields = window.order_info.fields_values;
      size = fields.length;
      var i = 0;
      var green_lite = 0;
      while(size != 0){
        if( fields[size - 1].name == 'ddelivery_id' ){
            if(fields[size - 1].value != 0){
                green_lite = 1;
            }
        }

        size--;
      };
      if( green_lite != 0 ){
                // подключаем скрипт который передаёт нам данные через JSONP
          var script = document.createElement('script');
          script.src = '" . URL::base( $this->request ) . "sdk/orderinfo/?order=' + window.order_info.id;
          document.documentElement.appendChild(script);

          // после отработки внешнего скрипта, заполняем таблицу пришедшими данными
          script.onload = function() {
              for (var key in data) {
                  var new_tr = document.createElement('tr');
                  new_tr.innerHTML= '&lt;td&gt;'+ key +'&lt;/td&gt;&lt;td&gt;'+ data[key] +'&lt;/td&gt;';
              table.appendChild(new_tr);
            }
          }
      }
    &lt;/script&gt;
  &lt;/body&gt;
  &lt;/html&gt;
</code>
<height>200</height>
</application-widget>";
                /*
                console.log(window.order_info.fields_values);
                for( var i = 0; i< window.order_info.fields_values.length; i++ ){
                console.log(window.order_info.fields_values[i]);
      }*/
                 $result =  $insales_api->api('POST','/admin/application_widgets.xml', $pulet);
                // $result =  $insales_api->api('GET','/admin/application_widgets.xml', $pulet);
                //  $result =  $insales_api->api('DELETE','/admin/application_widgets/7006.xml', $pulet);
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
            /*
            $house = $this->get_request_state('house');
            $street = $this->get_request_state('street');
            $flat = $this->get_request_state('flat');
            $corp = $this->get_request_state('corp');
            */


            $uid = (int)$this->get_request_state('insales_id');
            if( !$uid )
            {
                return;
            }

            if( $this->request->query('insales_id')){
                $session = Session::instance();
                $session->set('client_name', '');
                $session->set('client_phone', '');
                $session->set('shipping_address', '');

            }
            $client_name = $this->get_request_state('client_name');
            $client_phone = $this->get_request_state('client_phone');
            $shipping_address = $this->get_request_state('shipping_address');

            $this->request->query('shipping_address', $shipping_address);
            $this->request->query('client_name',$client_name);
            $this->request->query('client_phone',$client_phone);
            /*
            $this->request->query('house',$house);
            $this->request->query('street',$street);
            $this->request->query('flat',$flat);
            $this->request->query('corp',$corp);
            */

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
        catch ( \Exception $e ){
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