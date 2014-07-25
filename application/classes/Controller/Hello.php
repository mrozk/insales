<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Hello extends Controller
{

    public function initSessionToken(){
        $token = md5( microtime() . mt_rand(1,20) );
        $session = Session::instance();
        $session->set( 'card_' . $token, 'init' );
        return $token;
    }
    public function action_gus()
    {
        header('Content-Type: text/javascript; charset=UTF-8');
        if( isset($_SERVER["HTTP_REFERER"]) ){
            $parse = parse_url( $_SERVER["HTTP_REFERER"] );
            if(isset( $parse['host'] )){
                $memcache = new Memcache;
                $memcache->connect('localhost', 11211) or die ("Could not connect to Memcache");
                $settings = $memcache->get($parse['host']);
                if( empty ( $settings ) ){
                    $query = DB::select( 'settings', 'shop')->from('ddelivery_insales')->
                                 where( 'shop', '=', $parse['host'] )->as_object()->execute();
                    if( isset( $query[0]->shop ) && !empty( $query[0]->shop ) ){
                        $memcache->set( $query[0]->shop, $query[0]->settings );
                        $settings = $query[0]->settings;
                    }
                }
                if( !empty($settings) ){
                    $token = md5( microtime() . mt_rand(1,20) );
                    $memcache->set( 'card_' . $token, 1 , 0, 600 );
                }

                echo 'jQuery(".loader").css("display","none");updatePriceAndSend("' . $token . '");';
                return;
            }
        }
    }
}