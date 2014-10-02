<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 7/29/14
 * Time: 9:25 AM
 */

class MemController{

    private static $memcacheInstance = null;

    private function __construct(){}

    public static function instance(){
        if( self::$memcacheInstance == null ){
            self::$memcacheInstance = new Memcache;
            self::$memcacheInstance->connect('localhost', 11211) or die ("Could not connect to Memcache");
        }
        return self::$memcacheInstance;
    }

    public function __destruct(){
        if( self::$memcacheInstance != null ){
            self::$memcacheInstance->close();
        }
    }

    public static function initSettingsMemcache( $id ){
        $memcache = self::instance();
        $settings = $memcache->get('insales_' . $id);
        if( empty( $settings ) ){
            $query = DB::select( 'settings', 'shop', 'id')->from('insalesusers')->where( 'id', '=', $id )
                     ->as_object()->execute();
            if( isset( $query[0]->id )){
                $settings = json_decode($query[0]->settings);
                $settings->url = $query[0]->shop;
                $settings->id = $query[0]->id;
                $settings = json_encode($settings);
                $memcache->set( 'insales_' . $id, $settings );
            }else{
                throw new Exception('Не найден магазин');
            }
        }
        return json_decode($settings);
        /*
        $memcache = self::getMemcacheInstance();

        if( !empty( $url ) ){
            $settings = $memcache->get($url);

            if( !$settings ){

                $query = DB::select( 'settings', 'shop', 'id')->from('insalesusers')->
                             where( 'shop', '=', $url )->or_where('add_url', '=', $url)->as_object()->execute();

                if( isset( $query[0]->shop ) && !empty( $query[0]->shop ) ){
                    $settings = json_decode( $query[0]->settings );

                    $settings->insalesuser_id = $query[0]->id;

                    $memcache->set( $url, json_encode( $settings ) );
                    //print_r($settings);
                }else{
                    $settings = false;
                }
            }
            return $settings;
        }
        return false;
        */
    }
}