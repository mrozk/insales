<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Base extends Controller_Template
{

    /**
     * Auto loading configs groups
     *
     * @var array
     */
    public $config_groups = array(
        'blog',
    );

    /**
     * Auto loaded configs
     *     Format:
     *         array (group => params)
     *
     * @var array
     */
    public $config = array();

    public $template = "layout";

    public function before()
    {
        parent::before();

        $this->template->content = '';
    }
}