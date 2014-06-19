<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Hello extends Controller
{

    public function action_gus()
    {
        echo 'jQuery(".loader").css("display","none")';
        return;
    }

}