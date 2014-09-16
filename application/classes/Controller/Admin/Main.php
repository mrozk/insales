<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Main extends Controller_Admin_Layout{
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
        $id = (int)$this->request->query('id');
        $insales_user = ORM::factory('InsalesUser', array('id' => $id));
        if($insales_user->loaded()){
            $settings = $this->_extractPost();
            $settings['insalesuser_id'] = $insales_user->id;

            $settings = json_encode( $settings );

            $query = DB::update( 'insalesusers')->set( array('settings' => $settings) )
                ->where('id','=', $id)->execute() ;
            $memcache = MemController::getMemcacheInstance();
            if( !empty( $insales_user->shop ) ){
                $memcache->set( $insales_user->shop, $settings);
            }

            Notice::add( Notice::SUCCESS,'Успешно сохранено' );
            $this->redirect( URL::base( $this->request )  . 'admin/main/user/?id=' . $id );
        }else{
            Notice::add( Notice::ERROR, 'Ошибка сохранения' );
            $this->redirect( URL::base( $this->request ) . 'admin/main/user/?id=' . $id );
        }
    }


    public function action_addway(){
        $id = (int)$this->request->query('id');
        $insales_user = ORM::factory('InsalesUser', array('id' => $id));
        $settings = json_decode($insales_user->settings);
        if($insales_user->loaded()){
            $insales_api =  new InsalesApi( $insales_user->passwd, $insales_user->shop );

            Controller_Cabinet::preClean( $insales_api );

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

                $payload = Controller_Cabinet::getShippingMethod( $settings->common_caption, $settings->common_description );
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

                $payload = Controller_Cabinet::getShippingMethod( $settings->self_caption, $settings->self_description );
                $delivery_variants = $insales_api->api('POST', '/admin/delivery_variants.xml', $payload);

                $delivery_variants = new SimpleXMLElement( $delivery_variants );

                $payload = Controller_Cabinet::getShippingMethod( $settings->courier_caption, $settings->self_description );
                $delivery_variants2 = $insales_api->api('POST', '/admin/delivery_variants.xml', $payload);
                $delivery_variants2 = new SimpleXMLElement( $delivery_variants2 );

                $delivery = ($delivery_variants->id . ', ' . $delivery_variants2->id) ;
            }

            // Добавляем поля для хранения id заказа ddelivery
            $field = Controller_Cabinet::isFieldExists($insales_api, 'ddelivery_id');
            if( $field === false ){
                $payload = Controller_Cabinet::getXmlField( 'ddelivery_id' );
                $data = $insales_api->api('POST', '/admin/fields.xml', $payload);
                $data = new SimpleXMLElement( $data );
            }
            else{
                $data = $field;
            }
            // Добавляем поля для хранения id ddelivery_insales
            $field = Controller_Cabinet::isFieldExists($insales_api, 'ddelivery_insales');
            if( $field === false ){
                $payload = Controller_Cabinet::getXmlField( 'ddelivery_insales' );
                $data2 =  $insales_api->api('POST', '/admin/fields.xml', $payload );
                $data2 = new SimpleXMLElement( $data2 );
            }else{
                $data2 = $field;
            }
            // $delivery = new SimpleXMLElement($delivery);
            // Добавляем Способ доставки
            $payload =  Controller_Cabinet::getWidgetXml();
            $w = $insales_api->api('POST', '/admin/application_widgets.xml  ', $payload);
            // Добавляем JS
            $payload =  Controller_Cabinet::getXmlJsToInsales( $insales_user->id, $data->id, $data2->id, $delivery);
            // json_decode( $insales_api->api('PUT', '/admin/delivery_variants/' . $delivery->id . '.json', $payload) );
            $insales_api->api('PUT', '/admin/delivery_variants/' . $delivery_variants->id . '.xml', $payload);
            // Добавляем JS
            // Подписываемся на хук на создание заказа
            $payload = Controller_Cabinet::getXmlCreateHook( $insales_user->id );
            $insales_api->api('POST', '/admin/webhooks.xml', $payload) ;
            // Подписываемся на хук на создание заказа

            // Подписываемся на хук на обновление заказа
            $payload = Controller_Cabinet::getXmlUpdateHook( $insales_user->id );
            $insales_api->api('POST', '/admin/webhooks.xml', $payload) ;
            // Подписываемся на хук на обновление заказа

            $insales_user->delivery_variant_id = $delivery;
            $insales_user->save();

            Notice::add( Notice::SUCCESS,'Способ доставки успешно добавлен' );
            $this->redirect( URL::base( $this->request )  . 'admin/main/user/?id=' . $id );
        }
    }


    // Главная страница
    public function action_index(){

        $query = DB::select()->from('insalesusers')->as_object()->execute();
        $this->template->set('content', View::factory('admin/dashboard')->set('insalesusers',$query));
    }

    // Главная страница
    public function action_user(){
        $id = (int)$this->request->query('id');
        $usersettings = ORM::factory('InsalesUser', array('id' => $id));

        $insales_api =  new InsalesApi($usersettings->passwd, $usersettings->shop);

        $payment = Controller_Cabinet::getPaymentWays($insales_api);
        $fields = Controller_Cabinet::getFields( $insales_api);
        $characteristics = Controller_Cabinet::getOptionFields( $insales_api );
        $addr_fields = Controller_Cabinet::getAddressFields( $insales_api );
        $this->template->set('content', View::factory('panel')->set('id', $id )->set('usersettings', $usersettings )
                        ->set('addr_fields', $addr_fields)->set('message', $this->template->system_msg)
                        ->set('payment', $payment)->set('characteristics', $characteristics)->set('fields', $fields)
                        ->set('base_url', URL::base( $this->request )));
    }





} // End Main