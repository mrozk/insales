<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Dashboard extends Controller_Admin_Layout_Secure {

    /**
     * Control Panel Dashboard Action
     */
    public function action_index()
    {
        // Set content template
        $this->template->set('content', View::factory('admin/dashboard'));
    }

} // End Admin Dashboard