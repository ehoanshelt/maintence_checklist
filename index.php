<?php
/*
Plugin Name: Maintenance Checklist
Plugin URI: http://www.thewpvalet.com/plugins/Maintenance-Checklist
Description: To help keep your WordPress site running on it's best, you need to be sure to keep it maintained. Maintenance Checklist provides you a checklist and reminders of maintenance tasks to keep your WordPress website running at its peak.
Version: 1.0
Author: Eric Hoanshelt
Author URI: http://erichoanshelt.me
License: GPL2
*/

include('includes/functions.php');
include('includes/admin_dashboard.php');
include('includes/screen-meta-links.php');
 
//Runs that when plugin is activated
//Sets array to default options
function wpv_maintenance_activation(){
	if(!get_option('wpv_maintenance_tracking')){
		$wpv_maintenance_options = 	array(
			'backup' => array(
				'ID' => 'backup',
				'name' => 'Backup',
				'value' => 'false', 
				'increment' => 'd',
				'date_completed'=> date('Y-m-d')), 
			'spam-trash-comments' => array(
				'ID' => 'spam-trash-comments',
				'name' => 'Spam & Trash Comment Removal',
				'value' => 'false', 
				'increment' => 'm',
				'date_completed'=> date('Y-m-d')),
			'post-revisions' => array(
				'ID' => 'post-revisions',
				'name' => 'Post Revision Removal',
				'value' => 'false', 
				'increment' => 'm',
				'date_completed'=> date('Y-m-d')),
			'db-optimize' => array(
				'ID' => 'db-optimize',
				'name' => 'Database Optimization',
				'value' => 'false', 
				'increment' => 'm',
				'date_completed'=> date('Y-m-d')),
			'malware-scan' => array(
				'ID' => 'malware-scan',
				'name' => 'Malware Scan',
				'value' => 'false', 
				'increment' => 'm',
				'date_completed'=> date('Y-m-d')));
			
		update_option('wpv_maintenance_tracking', $wpv_maintenance_options);
	}
}


register_activation_hook(__FILE__, 'wpv_maintenance_activation');


function wpv_style_method() {
	wp_register_style('wpv-plugin-style',plugins_url('maintenance-checklist/style/maintenance-checklist-style.css'));  
	wp_enqueue_style('wpv-plugin-style');
}   
 
add_action('admin_init', 'wpv_style_method');

function add_screen_meta(){
	echo "<script type='text/javascript'>jQuery(document).ready(function(){
		jQuery('#screen-meta').append('<div id=\"maintenance-checklist\" class=\"hidden\">" . wpv_notify_admin() . "</div>');})</script>";
}

add_action('admin_head', 'add_screen_meta');

//Notification Handler for admin 
function wpv_notify_admin(){
	$maintenance_values = wpv_get_maintenance_values();
	$comma_separated = implode(",", $maintenance_values);
	$wpv_maintenance_message;
	
	if(wpv_check_for_false_values()){
	
		foreach($maintenance_values as $value){
			if($value['value'] == 'false'){
				$wpv_maintenance_message .='<p>' . $value['name'] . ' needs to be completed.</p>';
			}
		}
		
		$wpv_maintenance_message .= '<p>Once you completed the task, go to Settings > <a href="options-general.php?page=maintenance-checklist">Maintenance Checklist</a> and check it off!</p>';
	}else{
	$wpv_maintenance_message .= '<p>All maintenance tasks have been completed! Go to Settings > <a href="options-general.php?page=maintenance-checklist">Maintenance Checklist</a> and start feeling good!</p>';
}
	
	wpv_check_time_for_maintenance();
	
	return $wpv_maintenance_message;
}

//Simple example. Adds a screen meta link to example.com
//to the main Dashboard page.

add_screen_meta_link(
    'wpv-maintenance-checklist',  //Link ID. Should be unique. 
    'Maintenance CheckList',        //Link text.
    '#maintenance-checklist', //URL
    array('index.php','edit.php','edit-comments.php','link-manager.php', 'upload.php', 'themes.php','options-general.php', 'tools.php', 'users.php', 'plugins.php')            //Where to show the link.
);

?>