<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Hello extends Controller
{

    public function action_index()
    {
        $this->response->body('hello, worasdasdld!' . $this->request->param('id'));
        //echo 'guss';
    }
}