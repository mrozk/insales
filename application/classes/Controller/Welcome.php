<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Base {

	public function action_index()
	{
		exit('insales');
		///$this->response->body('hello, world!' . $this->request->param('id'));
	}


} // End Welcome
