<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Enter extends  Controller_Base{



    public function action_index()
    {
        $insales_id = (int)$this->request->query('insales_id');
        if( $insales_id )
        {
            $usr_ins = Model::factory('Enter')->get_insales($insales_id);
           // $this->template->set();
        }
        else
        {
            echo 'bad insales_id';
        }

        $this->template->set('content', View::factory('admin/dashboard')->set('usr_ins', $usr_ins));
    }
} 