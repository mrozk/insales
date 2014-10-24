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
        $ddelivery_insales = (int)$this->request->query('ddelivery_insales');
        try{
            //$IntegratorShop = new IntegratorShop2();
            $settings = MemController::initSettingsMemcache( $ddelivery_insales);

            $IntegratorShop = new IntegratorShop($this->request, $settings);
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
                    $errorMsg = $e->getMessage();
                }
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

        if ( $insales_user->loaded() ){

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


    public function action_getprice(){
        header('Content-Type: text/javascript; charset=UTF-8');
        $token = $this->request->query('tokenBody');

        $success = "false";
        $userInfo = array();

        if( isset( $token ) && !empty($token) ){

            $has_token = MemController::instance()->get('token_' . $token);
            //print_r($has_token);
            $info = json_decode( $has_token, true );
            if( isset( $info['order_id'] ) && !empty($info['order_id']) ){
                $settings = MemController::initSettingsMemcache($info['id']);
                $IntegratorShop = new IntegratorShop( $this->request, $settings, $info['cart_full'] );
                $ddeliveryUI = new DDeliveryUI( $IntegratorShop );
                $order = $ddeliveryUI->initOrder((int)$info['order_id']);
                $point = $order->getPoint();

                if( !empty( $point ) ){
                    $clientPrice = $ddeliveryUI->getClientPrice($point, $order);
                    $comment =  $ddeliveryUI->getPointComment($order);
                    //$userInfo = $ddeliveryUI->paymentPriceEnable('');
                    $filter = $ddeliveryUI->getAvailablePaymentVariants($order);

                    $userInfo = array('filter' => $filter,'price'=>$clientPrice);
                    $success = "true";
                }
            }
        }
        $response = array( 'success' => $success, 'userInfo' => $userInfo);
        $result = 'jsonCallback2(' . json_encode($response) . ');';
        echo $result;
        exit();
    }
    public function action_putcart(){
        header('Content-Type: text/javascript; charset=UTF-8');
        $token = $this->request->query('tokenBody');
        if( isset( $token ) && !empty($token) ){
            $has_token = MemController::instance()->get('token_' . $token);
            if( !empty($has_token) ){
                $token = 1;
            }else{
                $token = 0;
            }
        }else{
            $id = (int)$this->request->param('id');
            $productIdsString = $this->request->query('product_id');

            $token = md5( microtime() . mt_rand(1,20) . $id );
            $cart = array('cart' => $productIdsString, 'id' => $id);

            MemController::instance()->set('token_' . $token, json_encode($cart), null, 900 );

        }
        $result = 'jsonCallback("' . $token . '");';
        echo $result;
        exit();
    }

    public function action_index(){
        $token = $this->request->query('token');
        $has_token = MemController::instance()->get('token_' . $token);
        if(!empty($has_token)){
            $info = json_decode( $has_token, true );

            if( isset($info['id']) ){
                $settings = MemController::initSettingsMemcache($info['id']);
                if( empty($settings) ){
                    echo ('Ошибка инициализации');
                    return;
                }

                if( !isset( $info['cart_full'] ) && isset($info['cart'])){
                    $info['cart_full'] = $this->getItemsFromInsales( $info['cart'], $settings, 'http://' . $settings->url);
                    MemController::instance()->set('token_' . $token, json_encode($info), null, 900 );
                }
                $order_id = $this->request->post('order_id');
                $IntegratorShop = new IntegratorShop( $this->request, $settings, $info['cart_full'] );

                try{
                    $ddeliveryUI = new DDeliveryUI( $IntegratorShop );
                    $order = $ddeliveryUI->getOrder();
                    $order->addField1 = $settings->id;

                    $ddeliveryUI->render(isset($_REQUEST) ? $_REQUEST : array());

                    $order = $ddeliveryUI->getOrder();

                    if( isset($order->localId) && !empty($order->localId)  ){
                        $point = $order->getPoint();
                            if( !empty($point) ){
                                $info['order_id'] = (int)$order_id;
                                $info['way_id'] = $this->request->query('wayId');
                                MemController::instance()->set('token_' . $token, json_encode($info), null, 900 );
                            }
                    }

                }catch (\DDelivery\DDeliveryException $e){
                    $IntegratorShop->logMessage($e);
                }
            }else{
                echo ('Магазин не идентифицирован');
            }
        }else{
            // Пустой токен
            echo ('Для продолжения работы перезагрузите страницу');
        }
    }


    public function action_test(){
        $insales_user = ORM::factory('InsalesUser', array('id' => 29));
        echo $insales_user->shop;
        $insales_api =  new InsalesApi( $insales_user->passwd, $insales_user->shop );


        $variantsSettings = explode(',', $insales_user->delivery_variant_id);
        if( count( $variantsSettings ) > 0 ){
            foreach($variantsSettings as $item){
                $insales_api->api('DELETE', '/admin/delivery_variants/' . $item . '.json');
            }
        }

        Controller_Cabinet::getTopJsTag($insales_api, 29, 1723621, 1723622,
                                        1817097, 242743, 242744);
    }
}