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

    /*
    public function getXmlJsToInsales( $insalesuser_id, $field_id, $field2_id)
    {
        return $payload = '<?xml version="1.0" encoding="UTF-8"?>
                            <delivery-variant>
                              <title>DDelivery</title>
                              <position type="integer">1</position>
                              <url>' . URL::base( $this->request ) . 'hello/gus/' . $insalesuser_id . '/</url>
                              <description>DDelivery</description>
                              <type>DeliveryVariant::External</type>
                              <delivery-locations type="array"/>
                              <javascript>&lt;script type="text/javascript" src="' . URL::base( $this->request ) . 'html/js/ddelivery.js"&gt;&lt;/script&gt;

                                     &lt;script type="text/javascript"&gt;var ddelivery_insales={"field_id":' . $field_id . ',
                                     "field2_id":' . $field2_id . ',"_id":' . $insalesuser_id . ',
                                     "url": "' . URL::base( $this->request ) . '"
                                       };&lt;/script&gt;
                                    &lt;script type="text/javascript" src="' . URL::base( $this->request ) . 'html/action.js"&gt;&lt;/script&gt;
                              </javascript>
                              <price type="decimal">0</price>
                              <add-payment-gateways>true</add-payment-gateways>
                            </delivery-variant>';
    }
    */

    public function action_orderinfo(){
        $order = (int)$this->request->query('order');

        try{
            $IntegratorShop = new IntegratorShop2();
            $ddeliveryUI = new DDeliveryUI($IntegratorShop,true);

            $order = $ddeliveryUI->initOrder($order);

            if( $order->localId ){
                $errorMsg = '';
                try{
                    if( $order->type == \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER ){
                        $ddeliveryUI->checkOrderCourierValues( $order );
                    }else{
                        $ddeliveryUI->checkOrderSelfValues( $order );
                    }
                }catch (\DDelivery\DDeliveryException $e){
                    //$errorMsg = $e->getMessage(); $errorMsg .= $ddeliveryUI->formatPhone($order->toPhone);
                    $errorMsg = $e->getMessage();
                }

                //
                $answer = (($order->ddeliveryID == 0)?'Заявка на DDelivery не отправлена':'Номер заявки на DDelivery - ' . $order->ddeliveryID );
                echo "set_data({'" . (($errorMsg == '')?'':'<span style="color: #ff0000">Ошибка ввода информациии ' . $errorMsg . '</span> <br /> ') . ' ' . "ID заказа -" . $order->shopRefnum . "':'" . $answer . "'});";
            }
        }catch (\DDeliveryException $e){
            $ddeliveryUI->logMessage($e);
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
            $result = json_decode( $insales_api->api('PUT','/admin/orders/' . $cmsOrderID . '.json', $pulet) );
            return $result->id;
        }
    }

    public function changeInsalesOrderStatus( $order, $ui ){
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
    public function action_settings(){
        $client = (int)$this->request->query('client');
        $query = DB::select( 'id', 'width', 'height', 'length', 'weight')->from('usersettings')->
                      where( 'insalesuser_id', '=', $client )->as_object()->execute();

        echo 'inSettings(';
        if( count($query) ){
            echo json_encode( array('result' => 'success', 'request' => $query[0]) );
        }else{
            echo json_encode( array('result' => 'error') );
        }
        echo ');';
    }




    public function getOptionValue( $option_list, $needle ){
        if( count($option_list) )
        {
            foreach( $option_list as $item )
            {
                if( $needle == $item->product_field_id  )
                {
                    return $item->value;
                }
            }
        }
        return null;

    }

    public function getCaracteristicValue( $option_list, $needle ){
        if( count($option_list) )
        {
            foreach( $option_list as $item )
            {
                if( $needle == $item->property_id  )
                {
                    return $item->title;
                }
            }
        }
        return null;

    }

    // Нулячие значения заменяем дефолтными
    public function getDefault( $value, $default )
    {
        return ((empty($value))?$default:$value);
    }

    public function getItemsFromInsales( $ids, $settings, $url ){
        $idsArray = array();
        $quantArray = array();
        $skuArray = array();
        $result_products = array();

        $ids = explode( ',', $ids );

        if( count( $ids ) ){
            foreach( $ids as $oneItem ){
                if( !empty($oneItem) ){
                    $tempStr = explode('(_)', $oneItem);
                    $idsArray[] = (int)$tempStr[0];
                    $quantArray[$tempStr[0]] = (int)$tempStr[1];

                }
            }
            $prod_detail = file_get_contents( $url . '/products_by_id/' . implode(',', $idsArray) . '.json');
            $items = json_decode( $prod_detail );
            if( count( $items->products) ){

                for( $i = 0; $i < count( $items->products); $i++ ){

                    $item = array();

                    if( $settings->source_params != '1' ){
                        $item['width'] = $this->getOptionValue($items->products[$i]->product_field_values, $settings->width );
                        $item['height'] = $this->getOptionValue($items->products[$i]->product_field_values,
                            $settings->height);
                        $item['length'] = $this->getOptionValue($items->products[$i]->product_field_values,
                            $settings->length);
                    }else{
                        $item['width'] = $this->getCaracteristicValue($items->products[$i]->characteristics, $settings->params_width );
                        $item['height'] = $this->getCaracteristicValue($items->products[$i]->characteristics, $settings->params_height);
                        $item['length'] = $this->getCaracteristicValue($items->products[$i]->characteristics, $settings->params_length);

                    }

                    $item['weight'] = $items->products[$i]->variants[0]->weight;

                    $item['width'] =  (int) $this->getDefault($item['width'], $settings->plan_width);
                    $item['height'] = (int) $this->getDefault($item['height'], $settings->plan_height);
                    $item['length'] = (int) $this->getDefault($item['length'], $settings->plan_lenght);
                    $item['weight'] = (float) $this->getDefault($item['weight'], $settings->plan_weight);

                    if( !$item['width'] )
                        $item['width'] = $settings->plan_width;
                    if( !$item['height'] )
                        $item['height'] = $settings->plan_height;
                    if( !$item['length'] )
                        $item['length'] = $settings->plan_lenght;
                    if( !$item['weight'] )
                        $item['weight'] = $settings->plan_weight;

                    $item['id'] = $items->products[$i]->id;
                    $item['title'] = $items->products[$i]->title;
                    $item['price'] = $items->products[$i]->variants[0]->price;
                    $item['quantity'] = $quantArray[$item['id']];
                    $item['sku'] = $items->products[$i]->variants[0]->sku;
                    $result_products[] = $item;
                }
            }
        }
        return $result_products;
    }

    public function action_putcart(){
        header('Content-Type: text/javascript; charset=UTF-8');
        $token = $this->request->query('tokenBody');
        if( isset( $token ) && !empty($token) ){
            $has_token = MemController::instance()->get('token_' . $token);
            if( !empty($has_token) ){
                $token = true;
            }else{
                $token = false;
            }
        }else{
            $id = (int)$this->request->param('id');
            $productIdsString = $this->request->query('product_id');

            $token = md5( microtime() . mt_rand(1,20) . $id );
            $cart = array('cart' => $productIdsString, 'id' => $id);
            MemController::instance()->set('token_' . $token, json_encode($cart) );

        }
        $result = 'jsonCallback("' . $token . '");';
        echo $result;
        exit();
        //return;
        /*
        $token = $this->request->post('token');
        $cart = $this->request->query('data');
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211) or die ("Could not connect to Memcache");
        $has_token = $memcache->get( 'card_' . $token );
        if($has_token){
            $memcache->set( 'card_' . $token, $cart );
        }
        echo '{}';
        exit();
        */
    }

    public function action_index(){
        $token = $this->request->query('token');
        $has_token = MemController::instance()->get('token_' . $token);
        try{
            if(!empty($has_token)){
                $info = json_decode( $has_token, true );
                if( isset($info['id']) ){
                    $settings = MemController::initSettingsMemcache($info['id']);
                    if( !isset( $info['cart_full'] ) && isset($info['cart'])){
                        $info['cart_full'] = $this->getItemsFromInsales( $info['cart'], $settings, 'http://' . $settings->url);
                        MemController::instance()->set('token_' . $token, json_encode($info));
                    }

                    $IntegratorShop = new IntegratorShop( $this->request, $settings, $info['cart_full'] );
                    $ddeliveryUI = new DDeliveryUI( $IntegratorShop );
                    $order = $ddeliveryUI->getOrder();
                    $order->addField1 = $settings->id;
                    $ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());

                }else{
                    throw new Exception('Магазин не идентифицирован');
                }
            }else{
                throw new Exception('Пустой токен');
            }
        }catch (Exception $e){
            echo  $e->getMessage();
        }
        /*
         $token = $this->request->query('token');
         $items = $this->request->query('items');

         $has_token = MemController::getMemcacheInstance()->get( 'card_' . $token );

         if($has_token){
             $info = json_decode( $has_token, true );
             $settings = MemController::initSettingsMemcache($info['host']);
             $settingsToIntegrator = json_decode($settings);
             if( isset($items) && !empty( $items ) ){
                 $info['cart'] = $this->getItemsFromInsales($info['scheme'] . '://' . $info['host'], $items, $settingsToIntegrator);
                 MemController::getMemcacheInstance()->set( 'card_' . $token, json_encode( $info ), 0, 1200  );
             }
             try{
                 $IntegratorShop = new IntegratorShop( $this->request, $settingsToIntegrator, $info );
                 //echo $IntegratorShop->getApiKey();
                 $ddeliveryUI = new DDeliveryUI( $IntegratorShop );
                 $order = $ddeliveryUI->getOrder();
                 $order->addField1 = $settingsToIntegrator->insalesuser_id;
                 $ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());
             }
             catch( \DDelivery\DDeliveryException $e ){
                 echo $e->getMessage();
                 $ddeliveryUI->logMessage($e);
             }
         }else{
             echo 'Перезагрузите страницу браузера для продолжения';
         }
        */
    }


    public function action_test(){

        $insales_user = ORM::factory('InsalesUser', array('id' => 29));
        $settings = json_decode($insales_user->settings );

        //$IntegratorShop = new IntegratorShop( $this->request, $settings );
        //$IntegratorShop = new IntegratorShop2( );
        //$ddeliveryUI = new DDeliveryUI( $IntegratorShop,true );

        $insales_api =  new InsalesApi( $insales_user->passwd, $insales_user->shop );

        $xml = "<js-tag>
                    <type type=\"string\">JsTag::TextTag</type>
                    <content>
                        window.onload = function(){
                           CheckoutDelivery.find( 242743 ).setFieldsValues( [{fieldId: 1723622, value: 'xxxx' }] );
                           //console.log( CheckoutDelivery.find( 242743 ));
                           //console.log( CheckoutPaymentGateway );
                           //console.log( CheckoutDelivery.find( 242743 ) );
                           console.log(window.ORDER);
                           console.log(CheckoutDelivery.find( 242743 ));
                        }
                    </content>
                </js-tag>";


        $xml = "<js-tag>
                    <type type=\"string\">JsTag::FileTag</type>
                    <content>http://devinsales.ddelivery.ru/html/values.js</content>
                </js-tag>";

        //print_r( $insales_api->api('POST', '/admin/js_tags.xml', $xml) );
        //echo '<pre>';
        /*
       $order = $ddeliveryUI->initOrder( 1035 );
        echo '<pre>';
        //print_r($order);
        echo $ddeliveryUI->createSelfOrder($order);
        echo '<pre>';
        */
        //echo '</pre>';
        /*
        $insales_user = ORM::factory('InsalesUser', array('id' => 52));
        $insales_api =  new InsalesApi( $insales_user->passwd, $insales_user->shop );
        print_r($insales_api->api('DELETE', '/admin/delivery_variants/239921.xml') );
        print_r($insales_api->api('DELETE', '/admin/delivery_variants/239920.xml') );
        */
        /*
        $insales_user = ORM::factory('InsalesUser', array('id' => 29));

        if ( $insales_user->loaded() )
        {
            echo $insales_user->id;
        }
        */
        /*
        $IntegratorShop = new IntegratorShop2( );
        $ddeliveryUI = new DDeliveryUI( $IntegratorShop );
        echo '<pre>';
        print_r($ddeliveryUI->initOrder(360));
        echo '</pre>';

        echo 'xxx';
        */
        /*
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

            $result =  $insales_api->api('POST','/admin/application_widgets.xml', $pulet);
            // $result =  $insales_api->api('GET','/admin/application_widgets.xml', $pulet);
            //  $result =  $insales_api->api('DELETE','/admin/application_widgets/7006.xml', $pulet);
            echo '<pre>';
            print_r($result);
            echo '</pre>';
        } */
    }
}