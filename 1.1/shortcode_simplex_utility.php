<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

	/* Static var : Plugin Admin url */
	define( 'SCSMG_VAR_URL_WP',"//".$_SERVER['HTTP_HOST']."/wp-admin/admin.php?page=".SCSMG_VAR_PLUGIN_NAME);
	/* Static var : Max result per page */
	define( 'SCSMG_VAR_MAX_RES',10);

	/** Function{INT}->{ArrayASSOC} : input shortcode ID, return an associative array (single row) of a shortcodes from the table */
	function scsmg_method_get_Shortcode_byId($key) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';
		$search = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$table_name.' WHERE id=%d;',$key),ARRAY_A);
		return $search;
	}

	/** Function{STRING}->{ArrayASSOC} : input shortcode NAME, return an associative array (single row) of a  shortcodes from the table */	
	function scsmg_method_get_Shortcode_byName($key) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';
		$search = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$table_name.' WHERE name=%s;',$key),ARRAY_A);
		return $search;
	}
	
	/** Function->{INT} : return the count of all shortcode from the table */
	function scsmg_method_get_Shortcode_count() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';
		$count = $wpdb->get_var('SELECT COUNT(*) FROM '.$table_name);
		return $count;
	}	
	
	/** Function{INT,STRING}->{Array} : input shortcode ID, identifier of request the form, if not NULL an associative array with 
	 * the shortcode information is returned; in case of DELETE action a little form will be printed to request confirmation 
	 */
	function scsmg_method_populate_form_if_exist($id_raw,$action_raw) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';	
		
		$id = intval(sanitize_text_field($id_raw));
		$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id=%d",$id);
		$data = $wpdb->get_row($sql);
		$method = sanitize_text_field(preg_replace("/[^a-z]/", '', $action_raw));
	
		$obj = array('id_shortcode' => 0, 'name' => '', 'note' => '', 'code' => '', 'show' => 'none');		
	
		if($data!=NULL) {
			$obj = array('id_shortcode' => $data->id, 'name' => $data->name, 'note' => $data->note, 'code' => $data->code);
		}


		$local_delete_single = sprintf(esc_html__('Would you like to permanently delete shortcode #%1$s - namely %2$s ?','shortcode-simplex'),$obj['id_shortcode'],$obj['name']);
		$local_delete_all = esc_html__('Would you like to permanently delete ALL the shortcodes?','shortcode-simplex');
		$local_abort = esc_html__('Abort','shortcode-simplex');
		$local_delete_btn = esc_html__('Delete permanently','shortcode-simplex');
		$local_not_found = esc_html__('Shortcode not found! Sorry','shortcode-simplex');

		switch($method) {
			case 'edit' : { $obj['show'] = 'visible'; }; break;
			case 'delete' : {
				$obj['show'] = 'none';
				$element = scsmg_method_get_Shortcode_byId($obj['id_shortcode']);
				$nonce_action = 'NULL';
				if($element!=NULL) {
					$nonce_action = 'remove_one_'.$obj['id_shortcode'];
					$nonce_url_action = wp_nonce_url( $_SERVER['PHP_SELF'].'?page='.SCSMG_VAR_PLUGIN_NAME, $nonce_action, 'call' );
					
					echo('<h2 class="scsimplex_delete">'.$local_delete_single.' - <a href="'.admin_url().'admin.php?page='.SCSMG_VAR_PLUGIN_NAME.'">'.$local_abort.'</a>');
					echo('<form method="post" action="'.$nonce_url_action.'"><input type="hidden" name="erase_id" value="'.$obj['id_shortcode'].'" >');
					wp_nonce_field($nonce_action,'callback');
					echo('<input type="submit" name="secure" value="'.$local_delete_btn.'" ></form></h2><br>');					
				} else { echo('<h3 class="scsimplex_delete">'.$local_not_found.'</h3>'); }				
			} ; break;
			case 'clean' :  {
				$obj['show'] = 'none';
				$nonce_action = 'remove_all';
				$nonce_url_action = wp_nonce_url( $_SERVER['PHP_SELF'].'?page='.SCSMG_VAR_PLUGIN_NAME, $nonce_action, 'call' );
									
				echo('<h2 class="scsimplex_delete">'.$local_delete_all.' - <a href="'.admin_url().'admin.php?page='.SCSMG_VAR_PLUGIN_NAME.'">'.$local_abort.'</a>');
				echo('<form method="post" action="'.$nonce_url_action.'">');
				wp_nonce_field($nonce_action,'callback');
				echo('<input type="submit" name="purge" value="'.$local_delete_btn.'" ></form></h2><br>');									
			} ; break; 
		}
		return $obj;		
	}	
	
	/** Function{INT}->{MIXED} : input pagination index, print the shortcodes list plus the pagination hook */
	function scsmg_method_echo_recorded_shortcode($index_raw) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';
		$index = intval(preg_replace('/[^0-9]/', '', $index_raw));
		
		$shortcode_sum = scsmg_method_get_Shortcode_count();
		$max_index = ceil($shortcode_sum/SCSMG_VAR_MAX_RES); 
		if($index<0) { $index=0; }
		if($index>$max_index) { $index = $max_index-1; }
				
		$sql = $wpdb->prepare("SELECT * FROM $table_name ORDER BY id ASC LIMIT %d,".SCSMG_VAR_MAX_RES.";",$index*SCSMG_VAR_MAX_RES);
		$data = $wpdb->get_results($sql);
				
		$local_description = esc_html__('Notes','shortcode-simplex');
		$local_btn_delete_all = esc_html__('Delete ALL','shortcode-simplex');
		$local_btn_edit = esc_html__('Edit','shortcode-simplex');
		$local_btn_delete = esc_html__('Delete','shortcode-simplex');
				
		$table_head = '<table><tr class="scsimplex_row"><td class="scsimplex_row_id"><b>ID</b></td>'
			.'<td class="scsimplex_row_name"><b>Shortcode</b></td><td class="scsimplex_row_note"><b>'.$local_description.'</b></td>'
			.'<td align="center" ><a href="'.SCSMG_VAR_URL_WP.'&action=clean&id=ALL">'.$local_btn_delete_all.'</a></td></tr>';
		echo $table_head;				
		foreach($data as $row) {
			$structured_row = '<tr class="scsimplex_row"><td class="scsimplex_row_id">'.$row->id.'</td><td class="scsimplex_row_name">['.$row->name.']</td>'
			.'<td class="scsimplex_row_note">'.$row->note.'</td>'
			.'<td><a href="'.SCSMG_VAR_URL_WP.'&action=edit&id='.$row->id.'">'.$local_btn_edit.'</a>&nbsp;&nbsp;&nbsp;'
			.'<a href="'.SCSMG_VAR_URL_WP.'&action=delete&id='.$row->id.'">'.$local_btn_delete.'</a></td></tr>';
			echo $structured_row;	
		}
		echo("</table>");
		scsmg_method_shortcode_pagination($index);
	}
	
	/** Function{INT}->{Array} : input pagination index, print pagination hook to navigate through pages */
	function scsmg_method_shortcode_pagination($index) {
		$shortcode_sum = scsmg_method_get_Shortcode_count();
		$max_index = ceil($shortcode_sum/SCSMG_VAR_MAX_RES); 

		$prev = 0; $post = $index; $prev_vis = 'visible'; $post_vis = 'visible';
		if(($index-1)>0) { $prev=$index-1; }
		if(($index+1)<$max_index) { $post=$index+1; }
		if($index==($max_index-1)) $post_vis = "none";
		if($index<=0) $prev_vis = "none";
		$local_page = esc_html__('Page','shortcode-simplex');

		$local_page = esc_html__('Page','shortcode-simplex');
		$local_prev = esc_html__('Previous','shortcode-simplex');
		$local_next = esc_html__('Next','shortcode-simplex');

		/** This echo is for pagination checks only : echo "Previous $prev <-- Page [$index] --> Next $post - Last page $max_index - Elements $shortcode_sum<BR>"; */
		echo '<div align="center"><a style="display:'.$prev_vis.'" href="'.SCSMG_VAR_URL_WP.'&offset='.$prev.'">'.$local_prev.'</a> '.$local_page.' #'.($index+1)
			.' <a style="display:'.$post_vis.'" href="'.SCSMG_VAR_URL_WP.'&offset='.$post.'">'.$local_next.'</a></div>';		
	}
	
	/** Function{INT}->{VOID} : input shortcode ID, delete shortcode record */
	function scsmg_method_delete_shortcode($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';
		
		$obj = scsmg_method_get_Shortcode_byId($id);
		if($obj!=NULL) { $sql = $wpdb->delete( $table_name, array( 'id' => $id ), array( '%d' ) ); }
	}
	
	/** Function{INT}->{VOID} : delete ALL shortcode record */
	function scsmg_method_delete_all_shortcode() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';

		$sql = $wpdb->query("TRUNCATE $table_name;");
	}	
	
	/**
	 * Function{INT,STRING,STRING,STRING}->{Array} : input shortcode ID,NAME,NOTE,CODE , save a new record into the table or
	 * update if already present
	 */
	function scsmg_method_save_shortcode($id,$name,$note,$raw_code) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';
				
		/* In case of get_magic_quotes_gpc() OR wp_magic_quotes WP function other slashes ar not added. In case 
		 * noone of the above function is active, slashes are added */		
		if(get_magic_quotes_gpc() || function_exists('wp_magic_quotes')) {
			$code = $raw_code;
		} else {
			$code = addslashes($raw_code);			
		}
		$date = date("j M Y");		
		$obj_new = scsmg_method_get_Shortcode_byName($name);
		if($obj_new!=NULL && $id!=0) {
			$sql = $wpdb->update( $table_name, array( 'name' => $obj_new['name'],'code'=> $code, 'note' => $note, 'date' => $date ), array( 'id' => $id) , array( '%s','%s','%s','%s' ),array( '%d' )  ); 
			return '<h3 class="scsimplex_commok">Shortcode ['.$name.'] ID#'.$id.' has been edited</h3>';			
		} else {
			if($obj_new==NULL) {
			$sql = $wpdb->insert( $table_name, array( 'name' => $name,'code'=> $code, 'note' => $note, 'date' => $date ), array( '%s','%s','%s','%s' ) );
				return '<h3 class="scsimplex_commok">Shortcode ['.$name.'] has been created</h3>';		
			} else { return "<h3 class='scsimplex_alert'>This shortcode [$name] already exist. <a href='javascript:history.back()'>Go back</a></h3>"; }
		}		
	}

?>