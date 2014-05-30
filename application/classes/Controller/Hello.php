<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Hello extends Controller
{

    public function action_gus()
    {
        return;
    }
    public function action_index()
    {
        $insales_user = ORM::factory('InsalesUser', array('insales_id' => 136789));
        if ( $insales_user->loaded() )
        {
            //echo $insales_user->id;
            $insales_api =  new InsalesApi('ddelivery', $insales_user->passwd, $insales_user->shop );
            //URL::base( $this->request )

            $payload = '<?xml version="1.0" encoding="UTF-8"?>
                        <delivery-variant>
                          <title>DDelivery</title>
                          <position type="integer">1</position>
                          <url>https://insales.ddelivery/hello/gus/</url>
                          <description>DDelivery</description>
                          <type>DeliveryVariant::External</type>
                          <delivery-locations type="array"/>
                          <javascript>&lt;script type="text/javascript" src="http://insales.ddelivery.ru/html/js/ddelivery.js"&gt;&lt;/script&gt;
                                &lt;script type="text/javascript" src="http://insales.ddelivery.ru/html/assets/jquery.the-modal.js"&gt;&lt;/script&gt;
                                &lt;link rel="stylesheet" href="http://insales.ddelivery.ru/html/assets/the-modal.css" type="text/css" media="screen"/&gt;
                                &lt;script type="text/javascript" src="http://insales.ddelivery.ru/html/js/action.js"&gt;&lt;/script&gt;
                                &lt;script type="text/javascript"&gt;var ddelivery_insales_id=111;&lt;/script&gt;
                          </javascript>
                        </delivery-variant>';


            print_r( $insales_api->api('GET', '/admin/delivery_variants.xml', $payload) ) ;

        }
        /*
        foreach($usr_ins as $user)
        {
            $insales_api =  new InsalesApi('ddelivery', $user['passwd'], $user['shop'] );
        }
        */
        ///$this->response->body('hello, worasdasdld!' . $this->request->param('id'));
        //echo 'guss';
    }
}