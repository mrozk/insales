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
                //URL::base( $this->request )
                $payload = '<?xml version="1.0" encoding="UTF-8"?>
                            <delivery-variant>
                              <title>DDelivery</title>
                              <position type="integer">1</position>
                              <url>https://insales.ddelivery/hello/gus/</url>
                              <description>DDelivery</description>
                              <type>DeliveryVariant::External</type>
                              <delivery-locations type="array"/>
                              <javascript>&lt;script type="text/javascript" src="' . URL::base( $this->request ) . 'html/js/ddelivery.js"&gt;&lt;/script&gt;
                                    &lt;script type="text/javascript" src="' . URL::base( $this->request ) .'html/assets/jquery.the-modal.js"&gt;&lt;/script&gt;
                                    &lt;link rel="stylesheet" href="' . URL::base( $this->request ) . 'html/assets/the-modal.css" type="text/css" media="screen"/&gt;
                                     &lt;script type="text/javascript"&gt;var ddelivery_insales={"_id":' . $insalesuser . ', "url": "' . URL::base( $this->request ) . '" };&lt;/script&gt;
                                    &lt;script type="text/javascript" src="' . URL::base( $this->request ) . 'html/js/action.js"&gt;&lt;/script&gt;
                                &lt;div class="id_dd"&gt;&lt;/div&gt;
                              </javascript>
                            </delivery-variant>';

                $insales_api->api('POST', '/admin/delivery_variants.xml', $payload) ;


                Notice::add( Notice::SUCCESS,'Способ доставки успешно добавлен, необходимо активировать его в личном кабинете Insales' );
                $this->redirect( URL::base( $this->request ) . 'cabinet/' );
            }
        }
    }

    public function addJsToInsales()
    {

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