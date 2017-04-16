<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	/* Static var : alphanumeric char only list */
	define( 'SCSMG_VAR_CHAR_FILTER',"/[^A-Za-z0-9 àáâãèéêìíîñòóôõùúûýćĉĝĥĩĵŕśŝŵŷź]/");
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'shortcode_simplex';
	$charset_collate = $wpdb->get_charset_collate();
	$alert_style = 'display: none;';
	$message = '';
	$sample_page_id = scsmg_method_get_Sample_page();
	$sample_page_link = esc_url( get_page_link( $sample_page_id ) );
	
	/** handle delete request from the form */
	if( !empty($_POST) && isset($_POST['erase_id']) && isset($_POST['secure']) ) {
		$id = intval(preg_replace('/[^0-9]/', '', $_POST['erase_id']));
		if ( check_admin_referer( 'remove_one_'.$id, 'callback' ) ) {
			scsmg_method_delete_shortcode($id);
		}
	}
	
	/** handle delete of all shortcodes recorded  */
	if( !empty($_POST) && isset($_POST['purge']) ) {
		if ( check_admin_referer( 'remove_all', 'callback' ) ) {
		 	scsmg_method_delete_all_shortcode(); 
		}
	}
	
	/** handle new record and update record from the form */
	if( !empty($_POST) && isset($_POST['commit']) ) {
		$id = intval(preg_replace('/[^0-9]/', '', $_POST['id_shortcode']));
		if($id==0) { $nonce_name = 'create_new'; } else { $nonce_name = 'edit_old'; }
		$nonce_name .= '_'.$id;
		if ( check_admin_referer( $nonce_name, 'callback' ) ) {
			/* $_POST['name'] as no addslashes() because accept only alphanumeric chars */ 
			$name = sanitize_text_field(strtolower(preg_replace('/[^\w-]/', '',$_POST['shortcode'])));
			/* $_POST['note'] accept only alphanumeric, accented characters and _ and - 
			 * $_POST['code'] have addslashes() inside the scsmg_method_save_shortcode function */
			$note = sanitize_text_field(preg_replace(SCSMG_VAR_CHAR_FILTER, '', $_POST['note']));
			$code = sanitize_text_field(htmlentities($_POST['code']));
			
			$message = scsmg_method_save_shortcode($id,$name,$note,$code);
			$alert_style = "display: visible;";
		}
	}	
	
?> 
		
<script language="JavaScript">
function shortcode_simplex_showDiv() {
	if(jQuery("#new_shortcode").is(":visible")) { jQuery("#new_shortcode").hide(); } else { jQuery("#new_shortcode").show(); }
}	
</script>		
		
<h1>Shortcode Simplex - shortcode manager</h1>
<div class='scsimplex_container'>
	<h3><span id="visibility" style="padding:2px 4px 2px 4px; background-color: white; color: navy;"><a onclick="shortcode_simplex_showDiv()">+</a></span> Manage shortcode:</h3>
	<div class="scsimplex_announce" style="<?php echo $alert_style; ?>"><?php echo $message; ?></div>
	<?php 
		$obj = array('id_shortcode' => 0, 'name' => '', 'note' => '', 'code' => '', 'show' => 'none');
		
		if( isset($_GET['action']) && isset($_GET['id']) ) {
			$obj = scsmg_method_populate_form_if_exist($_GET['id'],$_GET['action']);
		}
		
		$nonce_action = 'NULL';
		if($obj['id_shortcode']==0) { $nonce_action = 'create_new'; } else { $nonce_action = 'edit_old'; }
		$nonce_action .= '_'.$obj['id_shortcode'];
		$nonce_url_action = wp_nonce_url( $_SERVER['PHP_SELF'].'?page='.SCSMG_VAR_PLUGIN_NAME, $nonce_action, 'call' );
	?>
	
	<div id="new_shortcode" style="display: <?php echo $obj['show']; ?>">
		<div class='scsimplex_add_div'><br>
			<form id="form_code" style='margin-left: 20px;' method="post" action="<?php echo $nonce_url_action; ?>">
				<?php wp_nonce_field($nonce_action,'callback'); ?>
				<input type="hidden" name="id_shortcode" value="<?php echo $obj['id_shortcode']; ?>" >
				<label for="shortcode">Shortcode name - accept only alphanumeric, <b>*required</b> (max 50 characters)</label><br>
				<input class="scsimplex_field" name="shortcode" type="text" size="70" width="90px" maxlength="50" value="<?php echo stripslashes($obj['name']); ?>" required <?php if($obj['id_shortcode']!=0) { echo "readonly"; } ?>/> <br>
				<label for="note">Shortcode note</label><br>
				<input class="scsimplex_field" name="note" type="text" size="80" width="120px" value="<?php echo stripslashes($obj['note']); ?>" /> <br>
				<label for="code">Code - <b>*required</b> </label><br>
				<textarea class="scsimplex_field" name="code" style="color:#2B6D12;font:bold;" cols="80" rows="10" required><?php echo stripslashes(html_entity_decode($obj['code'])); ?></textarea><br>
				<input class="scsimplex_field" name="commit" type="submit" value="Save shortcode" /><br><br>
			</form>
			<script>try { jQuery("form_code").validate(); } catch(err){} </script>
		</div>
	</div><br>
	<div class='scsimplex_main_div'>
		<p align="right" style="font-size: x-small; margin-right: 20px;" >Check out the Sample page : <b><a href="<?php echo $sample_page_link; ?>" target="_blank">ShortCode Simplex sample</a></b></p>
		<h3 style='text-align: left; margin-left:20px;'>Active shortcode : <a href="<?php echo SCSMG_VAR_URL_WP.'&action=edit&id=-1'; ?>">[Create new]</a></h3>
		<div style='width:100%;'>
				<?php
					if(isset($_GET['offset'])) { $offset = $_GET['offset']; } else { $offset = 0; } 
					scsmg_method_echo_recorded_shortcode($offset);
				?>
		</div>
	</div>
	<br>
	<div class="scsimplex_foot">
		This is free software under the terms of the <a href="https://www.gnu.org/licenses/gpl-2.0.html" target="_blank" >General Public License</a> - it is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY
	</div>	
</div>