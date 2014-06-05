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

    public function action_create()
    {
        if (!isset($HTTP_RAW_POST_DATA))
            $HTTP_RAW_POST_DATA = file_get_contents("php://input");

        $data = json_decode( $HTTP_RAW_POST_DATA );

        if( $data->delivery_variant_id == 221842 )
        {
            foreach( $data->fields_values as $item )
            {
                if ( $item->name == 'ddelivery_id')
                {
                    $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
                            ->values( array( $item->value, "asdsd") )->execute();
                }
            }
        }
        /*
        $query = DB::insert('ordddd', array( 'creater', 'orderer' ))
            ->values(array($HTTP_RAW_POST_DATA, "asdsd"))->execute();
        */
        return $HTTP_RAW_POST_DATA;

    }

    public function action_get()
    {
        $query = DB::select()->from('ordddd')->as_object()->execute();
        //print_r($query);

        $query = DB::query(Database::SELECT, 'SELECT * FROM ordddd WHERE id = 23');
        //$query->param(':user', 'john');
        $query->as_object();
        $return = $query->execute();
        //echo $return[0]->orderer;
        //print_r( $return[0] );
        $creator = json_decode( $return[0]->creater );
        /*(
        echo '<pre>';
            print_r($creator);
        echo '</pre>';
        */
        //echo $creator->delivery_variant_id;
        if( $creator->delivery_variant_id == 221842 )
        {
            foreach( $creator->fields_values as $item )
            {
                if ( $item->name == 'ddelivery_id')
                {
                    echo $item->value;
                }
            }
        }
        /*
        foreach( $return as $item )
        {
            $creator = json_decode( $item->creater );
            print_r($creator);
            echo '<hr />';

        }
        */

    }
}