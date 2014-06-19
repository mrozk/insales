<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cabinet extends  Controller_Base{


    public function action_save()
    {
        $session = Session::instance();
        $insalesuser = (int)$session->get('insalesuser');
        if ( !empty( $insalesuser ) )
        {
            $insales_user = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));
            if($insales_user->loaded())
            {
                $this->request->post('insalesuser_id', $insales_user->id);
                $zabor = $this->request->post('zabor');
                if( empty( $zabor ) )
                {
                    $this->request->post('zabor', '');
                }
                $pvz_companies = $this->request->post('pvz_companies');
                $cur_companies = $this->request->post('cur_companies');
                if( is_array( $pvz_companies ) )
                {
                    $pvz_companies = implode( ',', $this->request->post('pvz_companies') );
                }
                else
                {
                    $pvz_companies = '';
                }

                if( is_array( $cur_companies ) )
                {
                    $cur_companies = implode( ',', $this->request->post('cur_companies') );
                }
                else
                {
                    $cur_companies = '';
                }
                $this->request->post('pvz_companies', $pvz_companies);
                $this->request->post('cur_companies', $cur_companies);

              //  print_r($pvz_companies);
               // print_r($cur_companies);

                //exit();

                $insales_user->usersetting->values( $this->request->post() );
                $insales_user->usersetting->save();
                Notice::add( Notice::SUCCESS,'Успешно сохранено' );
                $this->redirect( URL::base( $this->request ) . 'cabinet/' );
            }

        }
        else
        {
            Notice::add( Notice::ERROR,'Доступ только из админпанели магазина insales' );
        }
    }



    public function action_addway()
    {
        $session = Session::instance();
        $insalesuser = (int)$session->get('insalesuser');
        if ( $insalesuser )
        {
            $insales_user = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));
            if ( $insales_user->loaded() )
            {
                $insales_api =  new InsalesApi('ddelivery', $insales_user->passwd, $insales_user->shop );
                $this->preClean( $insales_api );

                // Добавляем поля для хранения id заказа ddelivery
                $payload = $this->getXmlField( 'ddelivery_id' );
                $data = json_decode( $insales_api->api('POST', '/admin/fields.json', $payload) );
                // Добавляем поля для хранения id заказа ddelivery

                // Добавляем поля для хранения id ddelivery_insales
                $payload = $this->getXmlField( 'ddelivery_insales' );
                $data2 = json_decode( $insales_api->api('POST', '/admin/fields.json', $payload) );
                // Добавляем поля для хранения id ddelivery_insales

                // Добавляем JS
                $payload = $this->getXmlJsToInsales( $insales_user->id, $data->id, $data2->id);
                $delivery = json_decode( $insales_api->api('POST', '/admin/delivery_variants.json', $payload) );
                // Добавляем JS

                // Подписываемся на хук на создание заказа
                $payload = $this->getXmlCreateHook( $insales_user->id );
                $insales_api->api('POST', '/admin/webhooks.xml', $payload) ;
                // Подписываемся на хук на создание заказа

                // Подписываемся на хук на обновление заказа
                $payload = $this->getXmlUpdateHook( $insales_user->id );
                $insales_api->api('POST', '/admin/webhooks.xml', $payload) ;
                // Подписываемся на хук на обновление заказа

                $insales_user->delivery_variant_id = $delivery->id;
                $insales_user->save();

                Notice::add( Notice::SUCCESS,'Способ доставки успешно добавлен' );
                $this->redirect( URL::base( $this->request ) . 'cabinet/' );
            }
        }
    }

    public function preClean( $insales_api )
    {
        $data = json_decode( $insales_api->api('GET', '/admin/webhooks.json') );
        if( count($data) )
        {
            foreach( $data as $item )
            {

                if( substr_count( $item->address, URL::base( $this->request ) ) )
                {
                   $insales_api->api('DELETE', '/admin/webhooks/' . $item->id . '.json');
                }
            }
        }

        $data = json_decode( $insales_api->api('GET', '/admin/fields.json') );
        if( count($data) )
        {
            foreach( $data as $item )
            {
                if( ( $item->office_title == 'ddelivery_id' ) || ( $item->office_title == 'ddelivery_insales' ) )
                    $insales_api->api('DELETE', '/admin/fields/' . $item->id . '.json');
            }
        }

        $data = json_decode( $insales_api->api('GET', '/admin/delivery_variants.json') );
        if( count($data) )
        {
            foreach( $data as $item )
            {
                if( $item->title == 'DDelivery' )
                    $insales_api->api('DELETE', '/admin/delivery_variants/' . $item->id . '.json');
            }
        }
    }


    public function getXmlField( $name )
    {
        return $pulet = '<field>
                          <type>Field::TextField</type>
                          <for-buyer type="boolean">false</for-buyer>
                          <office-title>' . $name . '</office-title>
                          <obligatory type="boolean">false</obligatory>
                          <title>' . $name . '</title>
                          <destiny type="integer">3</destiny>
                          <for-buyer type="boolean">true</for-buyer>
                          <show-in-checkout type="boolean">true</show-in-checkout>
                          <show-in-result type="boolean">false</show-in-result>
                        </field>';
    }
    public function getXmlUpdateHook( $insalesuser )
    {
        return $payload = '<webhook>
                               <address>' . URL::base( $this->request ) . 'orders/update/?mag_id=' .
                               $insalesuser . '</address>
                               <topic>orders/update</topic>
                               <format type="integer">1</format>
                           </webhook>';
    }
    public function getXmlCreateHook( $insalesuser )
    {
        return $payload = '<webhook>
                               <address>' . URL::base( $this->request ) . 'orders/create/?mag_id=' .
                               $insalesuser . '</address>
                               <topic>orders/create</topic>
                               <format type="integer">1</format>
                           </webhook>';
    }
    public function getXmlJsToInsales( $insalesuser_id, $field_id, $field2_id )
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
                                    &lt;script type="text/javascript" src="' . URL::base( $this->request ) .'html/assets/jquery.the-modal.js"&gt;&lt;/script&gt;
                                    &lt;link rel="stylesheet" href="' . URL::base( $this->request ) . 'html/assets/the-modal.css" type="text/css" media="screen"/&gt;
                                     &lt;script type="text/javascript"&gt;var ddelivery_insales={"field_id":' . $field_id . ', "field2_id":' . $field2_id . ',"_id":' . $insalesuser_id . ', "url": "' . URL::base( $this->request ) . '" };&lt;/script&gt;
                                    &lt;script type="text/javascript" src="' . URL::base( $this->request ) . 'html/js/action.js"&gt;&lt;/script&gt;
                                &lt;div class="id_dd"&gt;&lt;/div&gt;
                              </javascript>
                              <price type="decimal">0</price>
                              <add-payment-gateways>true</add-payment-gateways>
                            </delivery-variant>';
    }

    public function getPaymentWays( $passwd, $shop )
    {
        $options = array();
        $insales_api =  new InsalesApi('ddelivery', $passwd, $shop );
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/payment_gateways.json') );
        //print_r($payment_gateways);
        if( count( $payment_gateways ) )
        {
            foreach( $payment_gateways as $gateways )
            {
                $options[$gateways->id] = $gateways->title;
            }
        }
        return $options;
    }

    public function getStatuses( $passwd, $shop )
    {
        // $insales_api =  new InsalesApi('ddelivery', $passwd, $shop );
        // $status = json_decode( $insales_api->api('GET', '/admin/payment_gateways.json') );

    }
    public function action_index()
    {
        $insales_id = (int)$this->request->query('insales_id');
        $shop = $this->request->query('shop');

        $session = Session::instance();
        $insalesuser = (int)$session->get('insalesuser');

        if ( !empty( $insalesuser ) )
        {
            echo $insalesuser;
            $usersettings = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));
            $payment = $this->getPaymentWays( $usersettings->passwd, $usersettings->shop );
            $fields = $this->getFields( $usersettings->passwd, $usersettings->shop );
            $this->template->set('content', View::factory('panel')->set('usersettings', $usersettings )
                           ->set('payment', $payment)->set('fields', $fields));
        }
        else
        {
            if( !empty( $insales_id ) && !empty( $shop ) )
            {
                $this->_proccess_enter($insales_id, $shop);
            }
            else
            {
                echo 'Вход осуществляется через личный кабинет insales.ru';
                Notice::add( Notice::ERROR,'Доступ только из админпанели магазина insales' );
            }
        }
    }
    public function getFields( $passwd, $shop )
    {
        $options = array();
        $insales_api =  new InsalesApi('ddelivery', $passwd, $shop );
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/option_names.json') );
        //print_r($payment_gateways);
        if( count( $payment_gateways ) )
        {
            foreach( $payment_gateways as $gateways )
            {
                $options[$gateways->id] = $gateways->title;
            }
        }
        return $options;
    }
    public function action_autologin()
    {

        $insales_token = $this->request->query('token');

        $session = Session::instance();
        $token = $session->get('ddelivery_token');
        $insales_id = $session->get('token_insales_id');


        $insales_user = ORM::factory('InsalesUser', array('insales_id' => $insales_id));
        if( $insales_user->loaded() )
        {
            if( $insales_token == md5( $token . $insales_user->passwd ) )
            {
                $session->set('insalesuser', $insales_id);
                $this->redirect( URL::base( $this->request ) . 'cabinet/' );
            }
            else
            {
                echo 'Invalid token';
            }
        }
        else
        {
            echo 'shop no found';
        }

    }

    private function _proccess_enter( $insales_id, $shop )
    {
        $back_url = URL::base( $this->request ) . 'cabinet/autologin/';
        $token = md5( time() . $insales_id );

        $session = Session::instance();
        $session->set('ddelivery_token', $token);
        $session->set('token_insales_id', $insales_id);
        $url = 'http://' . $shop . '/admin/applications/ddelivery/login?token=' . $token . '&login=' . $back_url;
        $this->redirect( $url );
    }

} 