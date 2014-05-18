<?php defined('SYSPATH') or die('No direct script access.');

class Model_Install extends Model {

    public function add_user( $token, $shop, $insales_id, $password )
    {

        $query = DB::insert('users', array( 'token', 'shop', 'insales_id', 'passwd' ))
                 ->values(array($token, $shop, $insales_id, $password))->execute();
    }

    public static function insert($table = NULL, array $columns = NULL)
    {
        return new Database_Query_Builder_Insert($table, $columns);
    }
}
