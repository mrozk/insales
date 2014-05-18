<?php defined('SYSPATH') or die('No direct script access.');

include('layout/default.php');
class Controller_Enter extends  Controller_Layout_Default {

    public function action_index()
    {
        $this->template->set('content', View::factory('admin/dashboard'));
    }
} 