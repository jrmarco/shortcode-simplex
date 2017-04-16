<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'shortcode_simplex';
	$table_name2 = $wpdb->prefix . 'shortcode_simplex_opt';
	
	$today = date("j M Y");
	/** Create some sample of shortcode, in array */
	$example = array('name'=>'scriptjquery' ,'note'=>addslashes('This shortcode insert javascript, jQuery code inside the page'),'code'=>addslashes('<script language="JavaScript">function tableshow() { if(jQuery("#example_table").is(":visible")) { jQuery("#example_table").hide(); } else { jQuery("#example_table").show(); } }</script>'),'date'=>$today);
	$example1 = array('name'=>'first' ,'note'=>addslashes('This shortcode print a bold version of Hello World!'),'code'=>addslashes('<b>Hello world!</b><br>'),'date'=>$today);
	$example2 = array('name'=>'second','note'=>addslashes('This shortcode create a colored DIV'),'code'=>addslashes('<div align="center" style="background-color:navy;color:white;">Look at me</div><br>'),'date'=>$today);
	$example3 = array('name'=>'third' ,'note'=>addslashes('This shortcode create an unordered list'),'code'=>addslashes('<ul><li>1st element</li><li>2nd element</li><li>3rd element</li></ul>'),'date'=>$today);
	$example4 = array('name'=>'fourth' ,'note'=>addslashes('This shortcode create a button that show/hide the sample table'),'code'=>addslashes('<input type="button" value="Click me" onclick="tableshow()" /><br>'),'date'=>$today);
	$example5 = array('name'=>'fifth' ,'note'=>addslashes('This shortcode create a table'),'code'=>addslashes('<div id="example_table"><table><th>Table Head</th><tr><td>1st column / first row</td><td>2nd column / first row</td></tr><tr><td colspan="2">This TD take 2 cols</td></tr></table></div>'),'date'=>$today);
	
	/** Write shortcode inside the PREFIX.'shortcode_simplex' table */
	$sql  = $wpdb->insert( $table_name, array( 'name' => $example['name'],'code'=> $example['code'], 'note' => $example['note'], 'date' => $example['date'] ), array( '%s','%s','%s','%s' ) );
	$sql1 = $wpdb->insert( $table_name, array( 'name' => $example1['name'],'code'=> $example1['code'], 'note' => $example1['note'], 'date' => $example1['date'] ), array( '%s','%s','%s','%s' ) );
	$sql2 = $wpdb->insert( $table_name, array( 'name' => $example2['name'],'code'=> $example2['code'], 'note' => $example2['note'], 'date' => $example2['date'] ), array( '%s','%s','%s','%s' ) );
	$sql3 = $wpdb->insert( $table_name, array( 'name' => $example3['name'],'code'=> $example3['code'], 'note' => $example3['note'], 'date' => $example3['date'] ), array( '%s','%s','%s','%s' ) );
	$sql4 = $wpdb->insert( $table_name, array( 'name' => $example4['name'],'code'=> $example4['code'], 'note' => $example4['note'], 'date' => $example4['date'] ), array( '%s','%s','%s','%s' ) );
	$sql5 = $wpdb->insert( $table_name, array( 'name' => $example5['name'],'code'=> $example5['code'], 'note' => $example5['note'], 'date' => $example5['date'] ), array( '%s','%s','%s','%s' ) );
			
	/** Package the content of the sample Page and write it into Wordpress */	
	$sample_content = '<div align="justify" style="font-size:small;">this page is a little collection of some potential example to give you an idea about the possible uses of shortcode.'
	.' Here we use six Shortcode already defined ( you can check them inside the Shortcode Simplex admin page ). Check what they do:<br>'
	.'[ scriptjquery ] -> insert a < script > tag with a jQuery function to hide/show the table<br>[scriptjquery]<br>'
	.'[ first ] -> print a bold version of Hello World!<br>[first]<br>'
	.'[ second ] -> create a blue div<br>[second]<br>'
	.'[ third ]-> create an unordered list<br>[third]<br>'
	.'[ fourth ]-> create a button that call the jQuery function to hide/show the table<br>[fourth]<br>'
	.'[ fifth ]-> create a table<br>[fifth]<br>'
	.'You can easily expand this list on the <a href="'.admin_url().'admin.php?page='.SCSMG_VAR_PLUGIN_NAME.'">plugin admin page</a><br><br>'
	.'<p align="center">If you like my work <a href="https://dev.bigm.it" target="_blank">buy me a coffee</a></p></div>';
	$sample_page = array('post_title'=>'ShortCode Simplex sample','post_content'  => $sample_content,'post_type' => 'page','post_status'=>'publish','post_author'=> 1,'post_category' => array(1));
	/* Collect the created page ID to store and print it later */
	$sample_post_id = wp_insert_post($sample_page);
	$wpdb->insert( $table_name2, array( 'opt_id' => 'NULL','opt_name'=> 'sample_page', 'opt_value' => $sample_post_id), array( '%s' ) );
?>