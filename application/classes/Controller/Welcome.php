<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Base {

	public function action_index()
	{
        Notice::add( Notice::ERROR,'Доступ только из админпанели магазина insales' );
	}


} // End Welcome
