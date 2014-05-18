<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Admin_Layout_Secure extends Controller_Layout_Secure {

    /**
     * Login page URL
     *
     * @var string
     */
    public $login_url = 'admin/auth/login';

    /**
     * Roles
     *
     * @var array
     */
    public $roles = array('login', 'admin');

} // End Admin Layout Secure Controller