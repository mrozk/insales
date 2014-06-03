<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Orders extends Controller
{
    public function action_index()
    {
        //echo 'ozk';
    }

    public function action_update()
    {
        file_put_contents( DOCROOT . 'mumu.txt',  serialize($_SERVER));
    }
    public function action_get()
    {
        print_r( unserialize( file_get_contents( DOCROOT . 'mumu.txt' ) )  );
    }
}