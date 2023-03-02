<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Aptec_db{

	public $revisors = [

		"cnmandreia@gmail.com" => [412, 420, 470, 507],

		"bernar.rodrigues1969@gmail.com" => [281, 282, 283, 398, 401, 424, 457, 465, 513],

		"calcafache@ipcb.pt" => [345, 478, 485, 483, 493, 498, 508, 510],

		"catarinaadeoliveira@gmail.com" => [62, 136, 223, 293, 343, 360, 446, 463, 482, 486, 515, 516],

		"cristiana.mlm@gmail.com" => [209, 357, 405, 472, 488],

		"ferreiradossantos.daniela@gmail.com" => [478, 485, 412, 420, 62, 136, 223, 293, 343, 360, 446, 209, 357, 405, 414, 345, 322, 460, 461, 278, 347, 353, 409, 413, 425, 267, 281, 282, 283, 398, 401, 424, 457],

		"ejfapereira@gmail.com" => [414, 468],

		"gui.rego@uhbw.nhs.uk" => [425, 483, 493, 498, 508, 510, 345],

		"joao.tiago25@gmail.com" => [491, 503, 506],

		"marcoantunes@hotmail.com" => [470, 507, 483, 493, 498, 508, 510, 463, 482, 486, 515, 516, 472, 488, 478, 485, 468, 491, 503, 506, 466, 480, 484, 511, 467, 489, 497, 501, 504, 465, 513],

		"nuno.varela@cardiocvp.net" => [322, 460, 461, 466, 480, 484, 511],

		"marco.10.pereira@gmail.com" => [209, 357, 405, 472, 488],

		"pedroamorim91@gmail.com" => [412, 420, 470, 507],

		"sandrinejorge102@gmail.com" => [322, 460, 461, 466, 480, 484, 511],

		"soniamatildemateus@gmail.com" => [278, 347, 353, 409, 413, 467, 489, 497, 501, 504],

		"susanamblanco@hotmail.com" => [267, 278, 347, 353, 409, 413, 467, 489, 497, 501, 504],

		"sigm74@hotmail.com" => [414, 468],

		"suse.caeiro@gmail.com" => [62, 136, 223, 293, 343, 360, 446, 463, 482, 486, 515, 516],

		"taniamamurca@hotmail.com" => [425, 267],

		"tiagosodafelicia@hotmail.com" => [491, 503, 506, 281, 282, 283, 398, 401, 424, 457, 465, 513]
	];

	function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;    
	}
			
	public function get_all_submissions( $id, $area, $email ){
		$result = $this->sql_get_all( $id );
		if ( $area == 'All' ){
			return $result;
		}
		return $this->select_by_work_id( $result, $email );
	}

	public function get_all_marks( $id, $area, $email ){
		$result = $this->sql_get_all( $id );
		if ( $area == 'All' ){
			return $result;
		}
		return $this->select_by_email( $result, $email);
	}

	public function get_submission_by_id( $submission_id ){
		$t1 	= $this->wpdb->prefix . 'fsq_data';
		$t2 	= $this->wpdb->prefix . 'fsq_form';
		$sql    = "SELECT *, form.`mcq` as formMcq, results.`mcq` as resultsMcq,
		form.`freetype` as formFreetype, results.`freetype` as resultsFreetype,
		form.`pinfo` as formPinfo, results.`pinfo` as resultsPinfo 
		FROM `{$t2}` as form, `{$t1}` as results WHERE form.id = results.form_id AND results.id = {$submission_id}";
		$result = $this->wpdb->get_results( $this->wpdb->prepare($sql), ARRAY_A );

		return $result[0];
	}

	public function get_file_by_id( $id ){
		$table 	= $this->wpdb->prefix . 'fsq_files';
		$sql    = "SELECT *	FROM `{$table}` WHERE id = {$id}";
		$result = $this->wpdb->get_results( $this->wpdb->prepare($sql), ARRAY_A );

		return $result[0];
	}

	function sql_get_all( $id ){
		$t1 	= $this->wpdb->prefix . 'fsq_data';
		$t2 	= $this->wpdb->prefix . 'fsq_form';
		$sql    = "SELECT *, 
		form.`mcq` as formMcq, results.`mcq` as resultsMcq,
		form.`freetype` as formFreetype, results.`freetype` as resultsFreetype,
		form.`pinfo` as formPinfo, results.`pinfo` as resultsPinfo 
		FROM `{$t2}` as form, `{$t1}` as results WHERE form.id = results.form_id AND form.id = {$id} ORDER BY results.id DESC";
		return $this->wpdb->get_results( $this->wpdb->prepare($sql), ARRAY_A );
	}


	function select_by_work_id( $table, $email ){
		$resultArray = [];
		foreach ($table as $tableRow){
			if ( !in_array( intval($tableRow['id']), $this->revisors[$email]) ) {
				continue;
			} 
			$resultArray[] = $tableRow;
		}	
		return $resultArray;
	}

	function select_by_email( $table, $email ){
		$resultArray = [];
		foreach ($table as $tableRow){
			if ( trim($tableRow['email']) != $email) {
				continue;
			} 
			$resultArray[] = $tableRow;
		}	
		return $resultArray;
	}

}