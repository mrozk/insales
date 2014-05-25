<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cabinet extends  Controller_Base{

    public function saveSettings()
    {

    }
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

    public function action_index()
    {
        $insales_id = (int)$this->request->query('insales_id');
        $shop = $this->request->query('shop');

        $session = Session::instance();
        $insalesuser = (int)$session->get('insalesuser');

        if ( !empty( $insalesuser ) )
        {
            $usersettings = ORM::factory('InsalesUser', array('insales_id' => $insalesuser));
            $this->template->set('content', View::factory('panel')->set('usersettings', $usersettings ));
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