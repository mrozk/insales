<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cabinet extends  Controller_Base{

    public function _extractPost(){
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
        $address = $this->request->post('address');
        $this->request->post('address', json_encode($address));

        return array( 'api' => $this->request->post('api'),
                   'rezhim' => $this->request->post('rezhim'),
                   'declared' => $this->request->post('declared'),
                   'width' => $this->request->post('width'),
                   'height' => $this->request->post('height'),
                   'length' => $this->request->post('length'),
                   'weight' => $this->request->post('weight'),
                   'status' => $this->request->post('status'),
                   'secondname' => $this->request->post('secondname'),
                   'firstname' => $this->request->post('firstname'),
                   'plan_width' => $this->request->post('plan_width'),
                   'plan_lenght' => $this->request->post('plan_lenght'),
                   'plan_height' => $this->request->post('plan_height'),
                   'plan_weight' => $this->request->post('plan_weight'),
                   'type' => $this->request->post('type'),
                   'pvz_companies' => $this->request->post('pvz_companies'),
                   'cur_companies' => $this->request->post('cur_companies'),
                   'from1' => $this->request->post('from1'),
                    'to1' => $this->request->post('to1'),
                    'val1' => $this->request->post('val1'),
                    'sum1' => $this->request->post('sum1'),
                    'from2' => $this->request->post('from2'),
                    'to2' => $this->request->post('to2'),
                    'val2' => $this->request->post('val2'),
                    'sum2' => $this->request->post('sum2'),
                    'from3' => $this->request->post('from3'),
                    'to3' => $this->request->post('to3'),
                    'val3' => $this->request->post('val3'),
                    'sum3' => $this->request->post('sum3'),
                    'okrugl' => $this->request->post('okrugl'),
                    'shag' => $this->request->post('shag'),
                    'zabor' => $this->request->post('zabor'),
                    'payment' => $this->request->post('payment'),
                    'address' => $this->request->post('address'),
                    'theme' => $this->request->post('theme'),
                    'form' => $this->request->post('form'),
                    'common_caption' => $this->request->post('common_caption'),
                    'self_caption' => $this->request->post('self_caption'),
                    'courier_caption' => $this->request->post('courier_caption'),
                    'common_description' => $this->request->post('common_description'),
                    'self_description' => $this->request->post('self_description'),
                    'courier_description' => $this->request->post('courier_description'),
                    'source_params' => $this->request->post('source_params'),

                    'params_width' => $this->request->post('params_width'),
                    'params_length' => $this->request->post('params_length'),
                    'params_height'  => $this->request->post('params_height')
        );


    }
    public function action_save(){
        $session = Session::instance();
        $insalesuser = (int)$session->get('insalesuser');
        if ( !empty( $insalesuser ) )
        {
            $insales_user = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));

            if($insales_user->loaded())
            {
                $settings = $this->_extractPost();
                $settings['insalesuser_id'] = $insales_user->id;
                $settings = json_encode( $settings );

                $query = DB::update( 'insalesusers')->set( array('settings' => $settings) )
                         ->where('insales_id','=', $insalesuser)->execute() ;
                $memcache = MemController::getMemcacheInstance();
                if( !empty( $insales_user->shop ) ){
                    $memcache->set( $insales_user->shop, $settings);
                }

                Notice::add( Notice::SUCCESS,'Успешно сохранено' );
                $this->redirect( URL::base( $this->request ) . 'cabinet/' );
            }

        }
        else
        {
            Notice::add( Notice::ERROR,'Доступ только из админпанели магазина insales' );
            $this->redirect( URL::base( $this->request ) . 'cabinet/' );
        }
    }


    public static  function getXmlField( $name ){
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

    public static  function getXmlInfoField( $name ){
        return $pulet = '<field>
                          <type>Field::TextField</type>
                          <for-buyer type="boolean">false</for-buyer>
                          <office-title>' . $name . '</office-title>
                          <obligatory type="boolean">false</obligatory>
                          <title>' . $name . '</title>
                          <destiny type="integer">3</destiny>
                          <for-buyer type="boolean">true</for-buyer>
                          <show-in-checkout type="boolean">true</show-in-checkout>
                          <show-in-result type="boolean">true</show-in-result>
                        </field>';
    }


    public function action_addway(){
        $session = Session::instance();
        $insalesuser = (int)$session->get('insalesuser');

        if ( $insalesuser )
        {
            $insales_user = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));
            $settings = json_decode($insales_user->settings);

            if ( $insales_user->loaded() )
            {

                $insales_api =  new InsalesApi( $insales_user->passwd, $insales_user->shop );

                self::preClean( $insales_api );

                $variantsSettings = explode(',', $insales_user->delivery_variant_id);

                if( count( $variantsSettings ) > 0 ){
                    foreach($variantsSettings as $item){
                        $insales_api->api('DELETE', '/admin/delivery_variants/' . $item . '.json');
                    }
                }

                if( $settings->type != '4' ){
                    if( $settings->common_caption == '' ){
                        $settings->common_caption = 'DDelivery - сервис доставки';
                    }
                    if( $settings->common_description == ''  ){
                        $settings->common_description = 'Доставка товаров во все населенные пункты России + пункты самовывоза в 150 городах';
                    }

                    $payload = self::getShippingMethod( $settings->common_caption, $settings->common_description );
                    $delivery_variants = $insales_api->api('POST', '/admin/delivery_variants.xml', $payload);
                    $delivery_variants = new SimpleXMLElement($delivery_variants);
                    $delivery = $delivery_variants->id ;

                }else{

                    if( $settings->self_caption == '' ){
                        $settings->self_caption = 'DDelivery - самовывоз';
                    }
                    if( $settings->self_description == ''  ){
                        $settings->self_description = 'Доставка товаров во все населенные пункты России + пункты самовывоза в 150 городах';
                    }

                    if( $settings->courier_caption == '' ){
                        $settings->courier_caption = 'DDelivery - курьерская доставка';
                    }
                    if( $settings->courier_description == ''  ){
                        $settings->courier_description = 'Доставка товаров во все населенные пункты России + пункты самовывоза в 150 городах';
                    }

                    $payload = self::getShippingMethod( $settings->self_caption, $settings->self_description );
                    $delivery_variants = $insales_api->api('POST', '/admin/delivery_variants.xml', $payload);

                    $delivery_variants = new SimpleXMLElement( $delivery_variants );

                    $payload = self::getShippingMethod( $settings->courier_caption, $settings->self_description );
                    $delivery_variants2 = $insales_api->api('POST', '/admin/delivery_variants.xml', $payload);
                    $delivery_variants2 = new SimpleXMLElement( $delivery_variants2 );

                    $delivery = ($delivery_variants->id . ', ' . $delivery_variants2->id) ;
                }
                // Добавляем поля для хранения id заказа ddelivery
                $field = self::isFieldExists($insales_api, 'ddelivery_id');
                if( $field === false ){
                    $payload = self::getXmlField( 'ddelivery_id' );
                    $data = $insales_api->api('POST', '/admin/fields.xml', $payload);
                    $data = new SimpleXMLElement( $data );
                }
                else{
                    $data = $field;
                }
                // Добавляем поля для хранения id ddelivery_insales
                $field = self::isFieldExists($insales_api, 'ddelivery_insales');
                if( $field === false ){
                    $payload = self::getXmlField( 'ddelivery_insales' );
                    $data2 =  $insales_api->api('POST', '/admin/fields.xml', $payload );
                    $data2 = new SimpleXMLElement( $data2 );
                }else{
                    $data2 = $field;
                }

                $field = self::isFieldExists($insales_api, 'Информация о доставке');
                if( $field === false ){
                    $payload = self::getXmlInfoField( 'Информация о доставке' );
                    $data3 =  $insales_api->api('POST', '/admin/fields.xml', $payload );
                    $data3 = new SimpleXMLElement( $data3 );
                }else{
                    $data3 = $field;
                }

                // $delivery = new SimpleXMLElement($delivery);
                // Добавляем Способ доставки
                $payload = self::getWidgetXml();
                $w = $insales_api->api('POST', '/admin/application_widgets.xml  ', $payload);
                // Добавляем JS
                $payload = self::getXmlJsToInsales( $insales_user->id, $data->id, $data2->id, $delivery, $data3->id);
                // json_decode( $insales_api->api('PUT', '/admin/delivery_variants/' . $delivery->id . '.json', $payload) );
                $insales_api->api('PUT', '/admin/delivery_variants/' . $delivery_variants->id . '.xml', $payload);
                // Добавляем JS
                // Подписываемся на хук на создание заказа
                $payload = self::getXmlCreateHook( $insales_user->id );
                $insales_api->api('POST', '/admin/webhooks.xml', $payload) ;
                // Подписываемся на хук на создание заказа

                // Подписываемся на хук на обновление заказа
                $payload = self::getXmlUpdateHook( $insales_user->id );
                $insales_api->api('POST', '/admin/webhooks.xml', $payload) ;
                // Подписываемся на хук на обновление заказа

                $insales_user->delivery_variant_id = $delivery;
                $insales_user->save();

                Notice::add( Notice::SUCCESS,'Способ доставки успешно добавлен' );
                $this->redirect( URL::base( $this->request ) . 'cabinet/' );
            }
        }
    }


    public static  function isFieldExists( $insales_api, $fname ){
        $data = json_decode( $insales_api->api('GET', '/admin/fields.json') );
        if( count($data) )
        {
            foreach( $data as $item )
            {
                if( ( $item->office_title == $fname )  )
                return $item;
            }
        }
        return false;
    }


    public static  function preClean( $insales_api ){
        $data = json_decode( $insales_api->api('GET', '/admin/webhooks.json') );
        if( count($data) ){
            foreach( $data as $item ){

                if( substr_count( $item->address, URL::base(TRUE, FALSE) ) ){
                    $insales_api->api('DELETE', '/admin/webhooks/' . $item->id . '.json');
                }
            }
        }

        $data = json_decode( $insales_api->api('GET', '/admin/application_widgets.json') );
        if( count($data) ){
            foreach( $data as $item ){
                $insales_api->api('DELETE', '/admin/application_widgets/' . $item->id . '.json');
            }
        }
    }

    public static  function getShippingMethod( $title, $description ){
        return $payload = '<?xml version="1.0" encoding="UTF-8"?>
                                <delivery-variant>
                                  <title>' . $title . '</title>
                                  <position type="integer">1</position>
                                  <url>' . URL::base(TRUE, FALSE) . 'hello/gus/</url>
                                  <description>' . $description . '</description>
                                  <type>DeliveryVariant::External</type>
                                  <delivery-locations type="array"/>
                                  <javascript></javascript>
                                  <price type="decimal">0</price>
                                  <add-payment-gateways>true</add-payment-gateways>
                                </delivery-variant>';
    }

    public static  function getXmlUpdateHook( $insalesuser )
    {
        return $payload = '<webhook>
                               <address>' . URL::base(TRUE, FALSE) . 'orders/update/?mag_id=' .
                               $insalesuser . '</address>
                               <topic>orders/update</topic>
                               <format type="integer">1</format>
                           </webhook>';
    }
    public static  function getXmlCreateHook( $insalesuser )
    {
        return $payload = '<webhook>
                               <address>' . URL::base(TRUE, FALSE) . 'orders/create/?mag_id=' .
                               $insalesuser . '</address>
                               <topic>orders/create</topic>
                               <format type="integer">1</format>
                           </webhook>';
    }
    public static  function getXmlJsToInsales( $insalesuser_id, $field_id, $field2_id, $deliveryID, $field3_id)
    {

        return $payload = '<?xml version="1.0" encoding="UTF-8"?>
                            <delivery-variant>
                              <id type="integer">' . $deliveryID . '</id>
                              <javascript>&lt;script type="text/javascript" src="' . URL::base(TRUE, FALSE) . 'html/js/ddelivery.js"&gt;&lt;/script&gt;
                                     &lt;script type="text/javascript"&gt;var ddelivery_insales={
                                     "delivery_id" : [ ' . $deliveryID . '],
                                     "field_id":' .  $field_id . ',
                                     "field2_id":' . $field2_id . ',"_id":' . $insalesuser_id . ',
                                     "field3_id":' . $field3_id . ',
                                     "url": "' . URL::base(TRUE, FALSE) . '"
                                       };
                                       &lt;/script&gt;
                                    &lt;script type="text/javascript" src="' . URL::base(TRUE, FALSE) . 'html/action.js"&gt;&lt;/script&gt;
                              </javascript>
                            </delivery-variant>';
    }

    public static  function getPaymentWays( $insales_api ){
        $options = array();
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/payment_gateways.json') );

        $options[0] = 'Выберитите поле';
        if( count( $payment_gateways ) )
        {
            foreach( $payment_gateways as $gateways )
            {
                $options[$gateways->id] = $gateways->title;
            }
        }
        return $options;
    }

    public function action_index(){
        $insales_id = (int)$this->request->query('insales_id');
        $shop = $this->request->query('shop');

        if( !$insales_id ){
            $session = Session::instance();
            $insalesuser = (int)$session->get('insalesuser');
        }

        if ( isset($insalesuser) && !empty( $insalesuser ) ){
            $usersettings = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));

            $insales_api =  new InsalesApi($usersettings->passwd, $usersettings->shop);

            $payment = self::getPaymentWays($insales_api);
            $fields = self::getFields( $insales_api);

            $characteristics = self::getOptionFields( $insales_api );

            $addr_fields = self::getAddressFields( $insales_api );

            $this->template->set('content', View::factory('panel')->set('usersettings', $usersettings )
                           ->set('addr_fields', $addr_fields)->set('message', $this->template->system_msg)
                           ->set('payment', $payment)->set('characteristics', $characteristics)->set('fields', $fields)
                           ->set('base_url', URL::base( $this->request )));

            }else{
                if( !empty( $insales_id ) && !empty( $shop ) ){
                    $this->_proccess_enter($insales_id, $shop);
                }
                else
                {
                    echo 'Вход осуществляется через личный кабинет insales.ru';
                    Notice::add( Notice::ERROR,'Доступ только из админпанели магазина insales' );
                }
        }
    }

    public static  function getAddressFields( $insales_api ){
        $options = array();
        /*
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/option_names.json') );
        */
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/fields.json') );
        //print_r($payment_gateways);
        $options[0] = 'Выберитите поле';
        if( count( $payment_gateways ) )
        {
            foreach( $payment_gateways as $gateways )
            {
                if($gateways->for_buyer && ($gateways->type !='Field::Phone')){
                    $options[$gateways->id] = $gateways->office_title;
                }
            }
        }

        return $options;
    }


    public static  function getOptionFields( $insales_api ){
        $options = array();
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/properties.json') );

        $options[0] = 'Выберитите поле';
        if( count( $payment_gateways ) )
        {
            foreach( $payment_gateways as $gateways )
            {
                $options[$gateways->id] = $gateways->title;
            }
        }

        return $options;
    }

    public static  function getFields( $insales_api ){
        $options = array();
        /*
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/option_names.json') );
        */
        $payment_gateways = json_decode( $insales_api->api('GET', '/admin/product_fields.json') );
        //print_r($payment_gateways);
        $options[0] = 'Выберитите поле';
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
        if( $insales_user->loaded() ){
            if( $insales_token == md5( $token . $insales_user->passwd ) ){

                if( $insales_user->shop != $session->get('insalesshop') ){
                    $insales_user->add_url = $insales_user->shop;
                    $insales_user->shop = $session->get('insalesshop');
                    $insales_user->save();
                }
                $session->set('insalesuser', $insales_id);
                $this->redirect( URL::base( $this->request ) . 'cabinet/' );
            }else{
                echo 'Invalid token';
            }
        }else{
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
        $session->set('insalesshop', $shop);

        $url = 'http://' . $shop . '/admin/applications/' . InsalesApi::$api_key . '/login?token=' . $token . '&login=' . $back_url;

        $this->redirect( $url );
    }



    public static  function getWidgetXml(){
        return $pulet = "<application-widget>
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
                  var ddelivery_id = 0;
                  while(size != 0){
                    if( fields[size - 1].name == 'ddelivery_id' ){
                        if(fields[size - 1].value != 0){
                            green_lite = 1;
                            ddelivery_id = fields[size - 1].value;
                        }
                    }

                    size--;
                  };
                  if( green_lite != 0 ){
                            // подключаем скрипт который передаёт нам данные через JSONP
                      var script = document.createElement('script');
                      script.src = '" . URL::base(TRUE, FALSE) . "sdk/orderinfo/?order=' + ddelivery_id;
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
    }


}