<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('page.class.php');

class Aptec_Revisor
{
    public static function init()
    {
        $class = __CLASS__;
        new $class;
    }

    function __construct()
    {
        $this->set_variables();
        $Page = new Aptec_Page( $this->area, $this->email, $this->name );
    }
    
    function set_variables(){
        $user_id = get_current_user_id(); 
        $this->name = get_user_meta( $user_id, 'first_name', true);    
        $this->email = wp_get_current_user()->user_email;
        $this->area = get_user_meta( $user_id, 'area', true);
    }
}