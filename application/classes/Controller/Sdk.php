<?php

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/example/IntegratorShop.php');

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

    public function action_addwidget()
    {
        $uid = (int)$this->get_request_state('insales_id');
        $insales_user = ORM::factory('InsalesUser', array('insales_id' => 136789));
        if ( $insales_user->loaded() )
        {
            $insales_api =  new InsalesApi('ddelivery', $insales_user->passwd, $insales_user->shop );
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
    &lt;p&gt;Номер заказа: &lt;b&gt;&lt;span id='order_number'&gt;&lt;span&gt;&lt;/b&gt;&lt;/p&gt;
    &lt;table id='statuses' style='border: 1px solid black;'&gt;
      &lt;tr&gt;
        &lt;th&gt;Дата&lt;/th&gt;
        &lt;th&gt;Статус&lt;/th&gt;
      &lt;/tr&gt;
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
      order_number_field.innerHTML = window.order_info.id;

      // подключаем скрипт который передаёт нам данные через JSONP
      var script = document.createElement('script');
      script.src = 'http://www.insales.ru/assets/js/widget_example_jsonp_data.js';
      document.documentElement.appendChild(script);

      // после отработки внешнего скрипта, заполняем таблицу пришедшими данными
      script.onload = function() {
          for (var key in data) {
              var new_tr = document.createElement('tr');
              new_tr.innerHTML= '&lt;td&gt;'+ key +'&lt;/td&gt;&lt;td&gt;'+ data[key] +'&lt;/td&gt;';
          table.appendChild(new_tr);
        }
      }
    &lt;/script&gt;
  &lt;/body&gt;
  &lt;/html&gt;
</code>
<height>60</height>
</application-widget>";
            print_r( $insales_api->api('POST', '/admin/application_widgets.xml', $pulet) );
        }
    }
    public function action_addcreate()
    {
        $uid = (int)$this->get_request_state('insales_id');
        $insales_user = ORM::factory('InsalesUser', array('insales_id' => 136789));
        if ( $insales_user->loaded() )
        {
            $insales_api =  new InsalesApi('ddelivery', $insales_user->passwd, $insales_user->shop );
            $pulet = '<webhook>
                        <address>http://insales.ddelivery.ru/orders/create/?mag_id=136789</address>
                        <topic>orders/create</topic>
                        <format type="integer">1</format>
                     </webhook>';
            //print_r( $insales_api->api('DELETE', '/admin/webhooks/48624.xml', $pulet) );
            //print_r( $insales_api->api('GET', '/admin/webhooks.xml', $pulet) );
            //print_r( $insales_api->api('POST', '/admin/webhooks.xml', $pulet) );
        }
    }
    public function action_addupdate()
    {
        /*
        $uid = (int)$this->get_request_state('insales_id');

        $IntegratorShop = new IntegratorShop( $this->request, $uid );
        $ddeliveryUI = new DDeliveryUI($IntegratorShop);
        $ddeliveryUI->cleanCache();
        */

        $uid = (int)$this->get_request_state('insales_id');
        $insales_user = ORM::factory('InsalesUser', array('insales_id' => 136789));
        if ( $insales_user->loaded() )
        {
           $insales_api =  new InsalesApi('ddelivery', $insales_user->passwd, $insales_user->shop );
           $pulet = '<webhook>
                        <address>http://insales.ddelivery.ru/orders/create/?mag_id=136789</address>
                        <topic>orders/create</topic>
                        <format type="integer">1</format>
                     </webhook>';
            //print_r( $insales_api->api('DELETE', '/admin/webhooks/48624.xml', $pulet) );
            //print_r( $insales_api->api('GET', '/admin/webhooks.xml', $pulet) );
           print_r( $insales_api->api('POST', '/admin/webhooks.xml', $pulet) );
        }
        //echo $uid;
    }
    public function action_index()
    {
        try
        {
            $uid = (int)$this->get_request_state('insales_id');
            if( !$uid )
            {
                return;
            }
            $IntegratorShop = new IntegratorShop( $this->request, $uid );
            $ddeliveryUI = new DDeliveryUI($IntegratorShop);

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