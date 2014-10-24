<?php defined('SYSPATH') OR die('No Direct Script Access');

use DDelivery\DDeliveryUI;

include_once( APPPATH . 'classes/Sdk/application/bootstrap.php');
include_once( APPPATH . 'classes/Sdk/mrozk/IntegratorShop.php');

class Controller_Hello extends Controller{

    public function initSessionToken(){
        $token = md5(microtime() . mt_rand(1, 20));
        $session = Session::instance();
        $session->set('card_' . $token, 'init');
        return $token;
    }

    public function action_gus(){
        $id = $this->request->param('id');
        $token = $this->request->query('token');

        $success = "false";
        $userInfo = array();

        if (isset($token) && !empty($token)) {

            $has_token = MemController::instance()->get('token_' . $token);
            $info = json_decode($has_token, true);
            if (isset($info['order_id']) && !empty($info['order_id'])) {
                $settings = MemController::initSettingsMemcache(  $info['id']);
                $IntegratorShop = new IntegratorShop($this->request, $settings, $info['cart_full']);
                $ddeliveryUI = new DDeliveryUI($IntegratorShop);
                $order = $ddeliveryUI->initOrder((int)$info['order_id']);
                $point = $order->getPoint();

                if (!empty($point)) {
                    $clientPrice = $ddeliveryUI->getClientPrice($point, $order);
                    $comment = $ddeliveryUI->getPointComment($order);
                    //$userInfo = $ddeliveryUI->paymentPriceEnable('');
                    $filter = $ddeliveryUI->getAvailablePaymentVariants($order);

                    $userInfo = array('price' => $clientPrice, 'comment' => $comment, 'method' => $info['way_id'],
                        'order_id' => $info['order_id'], 'filter' => $filter);
                    $success = "true";
                }
            }
        }
        $resultJson = json_encode(array('success' => $success, 'userInfo' => $userInfo));
        echo 'jsonCallback3(' . $resultJson . ')';
        return;
        //echo $this->request()->param('id');

        /*
        header('Content-Type: text/javascript; charset=UTF-8');
        $result = 'jQuery(".loader").css("display","none");';
        if( isset($_SERVER["HTTP_REFERER"]) ){
            $parse = parse_url( $_SERVER["HTTP_REFERER"] );
            if(isset( $parse['host'] )){
                $settings = MemController::initSettingsMemcache( $parse['host'] );
                if( !empty($settings) ){
                    $token = md5( microtime() . mt_rand(1,20) );
                    $memcache = MemController::getMemcacheInstance();
                    $price = $_REQUEST['price'];
                    $info = array( "host" => $parse['host'], 'scheme' => $parse['scheme'],
                                   "price" => $price );
                    $memcache->set( 'card_' . $token, json_encode( $info ), 0, 1200 );
                    $result .= 'updatePriceAndSend("' . $token . '");';
                }else{
                    $result .= 'updatePriceAndSend(null);';
                }
                $result .= 'enableDDButton();';
                echo $result;
                return;
            }
        }
        */
    }

    public function action_test(){
        $insales_user = ORM::factory('InsalesUser', array('id' => 29));
        // echo $insales_user->shop;
        $insales_api =  new InsalesApi( $insales_user->passwd, $insales_user->shop );
        $xml = Controller_Cabinet::getWidgetXml();

        $xml = json_decode( $insales_api->api('GET', '/admin/application_widgets.json',$xml) );
        print_r($xml);

    }

}