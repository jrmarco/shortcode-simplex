<?php
/* 
Plugin Name: Shortcode Simplex
Version: 1.0
Description: This lightweight plugin let you create and manage Shortcode inside any post. It's really simple and fast with its minimal graphics. If you like my work <a href="https://dev.bigm.it" >buy me a coffee</a>
Author: Marco Grossi
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Shortcode Simplex is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Shortcode Simplex is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
A copy of the GNU General Public License is available here https://www.gnu.org/licenses/gpl-2.0.html
*/
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	define( 'SCSMG_VAR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'SCSMG_VAR_PLUGIN_NAME', "shortcode_simplex");
	
	global $plugin_version;
	$plugin_version = '1.0';
	
	/** Function->{VOID} : install/init function. Will check also if this is the first installation creating table sample data */
	function shortcode_simplex_install() {
		if(shortcode_simplex_first()) {
			shortcode_simplex_table();
			shortcode_simplex_install_data();	
		}
	}
	
	/** Function->{BOOL} : check if a table with the name PREFIX.'shortcode_simplex' is already inside the DB */
	function shortcode_simplex_first() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';
		$sql = "SHOW TABLES LIKE '$table_name';";
		$check = $wpdb->get_var($sql);
		if($check!=NULL) { return false; } else { return true; }
	}
	
	/** Function->{VOID} : create the new table PREFIX.'shortcode_simplex' */
	function shortcode_simplex_table() {
		global $wpdb;
		global $plugin_version;
	
		$table_name1 = $wpdb->prefix . 'shortcode_simplex';
		$table_name2 = $wpdb->prefix . 'shortcode_simplex_opt';	
		$charset_collate = $wpdb->get_charset_collate();
		/* Shortcodes table */
		$sql = "CREATE TABLE IF NOT EXISTS $table_name1 (
			id int(11) NOT NULL AUTO_INCREMENT,
			name varchar(50) NOT NULL,
			note text NOT NULL,
			code text NOT NULL,
			date varchar(11) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );	
		
		/* Shortcode options table */
		$sql = "CREATE TABLE IF NOT EXISTS $table_name2 (
			id int(11) NOT NULL AUTO_INCREMENT,
			opt_id text NOT NULL,
			opt_name text NOT NULL,
			opt_value text NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );				
	
		add_option( 'addb_version', $plugin_version );
	}

	/**Function->{VOID} : create some sample shortcode with a sample page as guideline */
	function shortcode_simplex_install_data() {
		require_once(SCSMG_VAR_PLUGIN_PATH.'shortcode_simplex_sample.php');
	}

	/** Function{STRING}->{INT} : input OPTION_NAME, return OPTION_VALUE from the table */	
	function scsmg_method_get_Sample_page() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex_opt';
		$page_id = $wpdb->get_var('SELECT opt_value FROM '.$table_name.' WHERE opt_name="sample_page"');
		return $page_id;
	}	
	
	/** Function->{VOID} : remove the PREFIX.'shortcode_simplex' table from the DB */
	function shortcode_simplex_remove() {
		global $wpdb;
	
		$table_name1 = $wpdb->prefix . 'shortcode_simplex';
		$table_name2 = $wpdb->prefix . 'shortcode_simplex_opt';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name1'") == $table_name1) {
			$sample_page_id = intval(scsmg_method_get_Sample_page()); 
			wp_delete_post( $sample_page_id, TRUE);	
			$sql = "DROP TABLE $table_name1,$table_name2;";
			$wpdb->query($sql);
		}
	}

	/** Function->{VOID} : include /admin/style.css stylesheet in the plugin admin page only */
	function shortcode_simplex_style($hook) {
    	if ( $hook == 'toplevel_page_shortcode_simplex' ) {
    		wp_enqueue_style( 'shortcode_simplex_style', plugins_url( 'admin/style.css', __FILE__ ) );
		}
	}		
	 
	/** Function->{VOID} : create a new item in the Wordpress admin main menu */
	function shortcode_simplex_menu(){
		add_menu_page( 'Shortcode Simplex', 'Shortcode Spx', 'manage_options', 'shortcode_simplex', 'shortcode_simplex_init' );
	}

	/** Function->{STRING} : read the $ATTS , $CONTENT & $TAG from the Wordpress content converting them into shortcodes user define */
	function shortcode_simplex_shortcode( $atts,$content,$tag ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';		
		$sql = "SELECT name,code FROM $table_name ORDER BY id ASC;";
		$data = $wpdb->get_results($sql);		
		
		$shortarray = array();
		foreach($data as $row) {
			$shortarray[$row->name] = $row->code;
		}
		
		if($content==NULL) { return stripslashes(html_entity_decode($shortarray[$tag])); }
			else {
				$open_tag = '<'.stripslashes(html_entity_decode($shortarray[$tag])).'>';
				$close_tag_full = '</'.stripslashes(html_entity_decode($shortarray[$tag])).'>';
				$close_tag = explode(" ", $close_tag_full);
				return $open_tag.''.do_shortcode($content).''.$close_tag[0];
			}
	}
	
	/** Function->{VOID} : read the shortcode list from DB creating hooks with Wordpress */
	function shortcode_simplex_load_shortcode() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'shortcode_simplex';		
		$sql = "SELECT name FROM $table_name;";
		$rows = $wpdb->get_results($sql);
		foreach($rows as $row) {
			$name = $row->name;
			add_shortcode( $name, 'shortcode_simplex_shortcode' );
		}	
	}

	/** Function->{VOID} : plugin init function, include method and content pages */	
	function shortcode_simplex_init() {
		require_once(SCSMG_VAR_PLUGIN_PATH.'shortcode_simplex_utility.php');		
		require_once(SCSMG_VAR_PLUGIN_PATH.'shortcode_simplex_main.php');
	}
	
	register_activation_hook( __FILE__, 'shortcode_simplex_install' );
	/* Deactivation_hook not implemented yet : register_deactivation_hook( __FILE__, '' ); */	
	register_uninstall_hook(__FILE__, 'shortcode_simplex_remove');		
		
	add_action( 'admin_menu', 'shortcode_simplex_menu');
	add_action( 'admin_enqueue_scripts', 'shortcode_simplex_style' );
	add_action( 'wp_enqueue_scripts', 'shortcode_simplex_load_shortcode' );	

?>