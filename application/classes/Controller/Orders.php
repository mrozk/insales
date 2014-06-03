<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Orders extends Controller
{
    public function action_index()
    {
        echo Kohana::VERSION ;
    }

    public function action_update()
    {
        if (!isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = file_get_contents("php://input");
            $query = DB::insert('ordddd', array( 'orderer' ))
            ->values(array($HTTP_RAW_POST_DATA))->execute();
        return $HTTP_RAW_POST_DATA;

    }
    public function action_get()
    {
        $query = DB::select()->from('ordddd')->as_object()->execute();
        //print_r($query);

        $query = DB::query(Database::SELECT, 'SELECT * FROM ordddd ');
        //$query->param(':user', 'john');
        $query->as_object();
        $return = $query->execute();
        //echo $return[0]->orderer;

        foreach( $return as $item )
        {
           echo '<pre>';
           print_r(json_decode( $item->orderer ));
           echo '</pre>';
        }

    }
}