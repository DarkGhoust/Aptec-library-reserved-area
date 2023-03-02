<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('database.class.php');

class Ajax_Helper{
	
	public $notAllowedDetails = [
		"afiliação institucional",
		"autorizo a captação e disponibilização da comunicação oral e e-poster no campus virtual.",
		"nome para cartão de identificação no congresso"
	];

    public static function init()
    {
        $class = __CLASS__;
        new $class;
    }

    function __construct()
    {
        add_action('wp_ajax_show_detailed_submission', array( $this, 'show_detailed_submission' ));
		$this->db = new Aptec_db();
    }


    function show_detailed_submission(){
		$Id = $_GET['id'];
        $tableRow = $this->db->get_submission_by_id( $Id );
        $result = unserialize($tableRow["resultsMcq"]);		
        $form 	= unserialize($tableRow["formMcq"]);
        echo "<table>";
        foreach ($result as $key => $value){
			if( !$this->is_allowed_title( $form[$key]['title']) ){
				continue;
			}
            $selected = $value['options'];
            if (!empty($selected)) {
                echo $this->generate_table_row( $form[$key]['title'], $form[$key]['settings']['options'][$selected[0]]['label'] );
            } 
        }
        echo "</table>";


        $result = unserialize( $tableRow["resultsFreetype"] ); 		
        $form 	= unserialize( $tableRow["formFreetype"] );
        
        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        
        echo "<table>";
        foreach ($result as $key => $value){
			if( !$this->is_allowed_title( $form[$key]['title']) ){
				continue;
			}
            if ($value['type'] == 'upload' && !empty( $value['id'] )){
                $fileHTML = '';
                foreach ($value['id'] as $fileId){
                    $file = $this->db->get_file_by_id($fileId);
                    $fileHTML = $fileHTML . "<p><a target='_blank' href='{$file['guid']}'>{$file['name']}</a></p>";
                }
                echo $this->generate_table_row($form[$key]['title'], $fileHTML );
            }
            if (!empty( $value['value'])) {
                echo $this->generate_table_row($form[$key]['title'], $value['value']);
            } 
        }
        echo "</table>";
        wp_die();
    }

    function get_title( $result, $form ){
        foreach ($result as $key => $value){
            if ( $form[$key]['title'] == "Título"){
                return $value['value'];
            }
        }
    }
	
	function is_allowed_title($title){
		$title = trim(strtolower($title));
		if (in_array($title, $this->notAllowedDetails)){
			return false;
		}	
		return true;
	}

    function generate_table_row($title, $value){
        return "<tr><th class='long-title'>{$title}</th><td>{$value}</td>";
    }

} // !class_exists