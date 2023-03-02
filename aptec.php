<?php 


if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action( 'template_redirect', 'redirect_if_user_not_logged_in' );
function redirect_if_user_not_logged_in() {
	if( is_page('pagina-reservada-revisores') ){
		if ( !is_user_logged_in() ){
			wp_redirect('login-revisores');
			exit;
        }
        
        require_once('classes/revisor.class.php');
        $revisor = new Aptec_Revisor();
	}
}

require_once('classes/ajax.class.php');
$ajaxHelper = new Ajax_Helper();
