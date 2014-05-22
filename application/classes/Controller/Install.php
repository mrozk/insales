<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Install extends Controller {

    public $secret_key = '1a29563d2f955e2c34b19f738ea1f8a6';

    public function action_index()
    {
        $token = $this->request->query('token');
        $shop = $this->request->query('shop');
        $insales_id = $this->request->query('insales_id');
        $password = md5( $token . $this->secret_key );

        if( $token && $shop && $insales_id && $password )
        {
            $books = Model::factory('Install')->add_user( $token, $shop, $insales_id, $password );
        }
        else
        {
            $this->response->body('bad psdaram');
        }
    }

}
?>