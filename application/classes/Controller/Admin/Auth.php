<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Auth extends Controller_Base
{
    /**
     * @var string
     */
    public $success_url = '/admin';
    /**
     * User Login Action
     */



    public function action_login()
    {
        // init errors array
        $errors = array();
        $this->template->set('content', View::factory('admin/login') );
    }
}