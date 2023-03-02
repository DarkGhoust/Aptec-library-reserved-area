<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('database.class.php');

class Aptec_Page{

    public static function init()
    {
        $class = __CLASS__;
        new $class;
    }

    function __construct( $area, $email, $name )
    {
        add_shortcode( 'revisor-works', array($this, 'show_all_works'));
        add_shortcode( 'revisor-marks', array($this, 'show_all_marks'));
        $this->db = new Aptec_db();
        $this->area = $area;
		$this->email = $email;
		$this->name = $name;
    }

    function show_all_works($atts){
        $formId = $atts['id'];
        $data = $this->db->get_all_submissions( $formId, $this->area, $this->email );
// 		echo "<pre>";
// 		print_r ($data); 
// 		echo "</pre>";

        ob_start();	
        ?>
		
		<table class="productivity-table" id="works">
			<div style="max-width: 980px;
    					margin: 15px auto;
						">
			  <input type="checkbox" id="reviewed" class="toggle-reviewed">
			  <label for="reviewed">Ocultar trabalhos revistos</label>
			</div>
		<thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Título</th>
                <th>Total: <?=count($data);?></th>
            </tr>
		</thead><?
        
        foreach ($data as $tableRow) {
            $this->show_short_works($tableRow);
        }
        ?>
            </table>
            <div id="work-content"></div>
        <?php
        require_once( dirname(__DIR__, 1) . '/js/works.php');
        return ob_get_clean();
    }

    function show_all_marks($atts){
        $formId = $atts['id'];
        $data = $this->db->get_all_marks( $formId, $this->area, $this->email );
		
		$data = $this->calculate_total_points( $data );
		
// 		echo "<pre>";
// 		print_r ($data); 
// 		echo "</pre>";
// 		return true;
        ob_start();	
        ?><table class="productivity-table" id="marks">
			<thead>
				<tr>
					<th>ID de trabalho</th>
					<th>Pontuação</th>
					<th>Título</th>
					<th>Nome de revisor</th>
					<th>Número de submissões: <?=count($data);?></th>
					<th>Pontuação total</th>
				</tr>
			</thead><?
        
        foreach ($data as $tableRow) {
            $this->show_short_marks($tableRow);
        }
        ?>
            </table>
            <div id="mark-content"></div>
        <?php
        require_once( dirname(__DIR__, 1) . '/js/marks.php');
		require_once( dirname(__DIR__, 1) . '/js/hide_evaluated.php');
        return ob_get_clean();
    }

    function show_short_works($tableRow){

        $result = unserialize( $tableRow["resultsFreetype"] ); 		
        $form 	= unserialize( $tableRow["formFreetype"] );    
		setlocale(LC_ALL, 'fr_FR');

        $date = date_create( $tableRow['date'] );
        $title = $this->get_title( $result, $form );
        printf("<tr data-id='%s'>
            <td>%s</td>
            <td width='200'>%s</td>
            <td>%s</td>
            <td width='100'><button class='open-work' data-id='%s'>Ver</button></td>
        </tr>", $tableRow['id'], $tableRow['id'], date_format($date, 'Y-m-d'), $title, $tableRow['id']);
    }

    function show_short_marks($tableRow){
		
        $result = unserialize( $tableRow["resultsFreetype"] ); 		
        $form 	= unserialize( $tableRow["formFreetype"] );   
	
		$totalScore = 0;
		$tableRow['total_score'] < 10 ? $totalScore = '0'. $tableRow['total_score'] : $totalScore = $tableRow['total_score'];
		
        $date = date_create( $tableRow['date'] );
        $title = $this->get_title( $result, $form );
        printf("<tr data-revisor='%s'>
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
			<td>%s</td>
            <td><button class='open-mark' data-id='%s'>Ver</button></td>
			<td>%s (%s&percnt;)</td>
        </tr>", $tableRow['f_name'], $tableRow['work_id'], intval($tableRow['score']), $title, $tableRow['f_name'], $tableRow['id'], $totalScore, intval($tableRow['total_score']*100/60));
    }


    function get_title( $result, $form ){
        foreach ($result as $key => $value){
            if ( $form[$key]['title'] == "Título"){
                return $value['value'];
            }
        }
    }

    function generate_table_row($title, $value){
        return "<tr><th class='long-title'>{$title}</th><td>{$value}</td>";
    }
	
	function calculate_total_points( $table ){
		$table = $this->push_workId_to_top_table( $table );
		
		$key_values = array_column($table, 'work_id'); 
		array_multisort($key_values, SORT_ASC, $table);
		
		$points = 0;
		$workID = 0;
		$rowsToChange = [];
		foreach ($table as $tableRowID => $tableRow){
			if ($workID != $tableRow['work_id'] ){
				foreach ($rowsToChange as $row){
					$table[$row]['total_score'] = $points;
				}
				unset ($rowsToChange);
				
				$points = intval( $tableRow['score'] );
				$workID = $tableRow['work_id'];
				$rowsToChange[] = $tableRowID;
			}
			else{
				$points = $points + intval( $tableRow['score'] );
				array_push( $rowsToChange, $tableRowID );
			}
		}
		foreach ($rowsToChange as $row){
			$table[$row]['total_score'] = $points;
		}
		
		return $table;
	}
	
	function push_workId_to_top_table( $table ){
		foreach ($table as $tableRowID => $tableRow){
			$result = unserialize( $tableRow["resultsFreetype"] ); 		
			$form 	= unserialize( $tableRow["formFreetype"] ); 

			foreach ($result as $key => $value){
				if( trim($form[$key]['title']) == "Id de trabalho"){
					$table[$tableRowID]['work_id'] = $value['value'];
				}
			}
		}
		return $table;
	}

} // !class_exists