<?php
if(is_admin()){
add_action('admin_menu', 'wpv_register_my_custom_submenu_page');

function wpv_register_my_custom_submenu_page() {
	add_options_page('Maintenance Checklist', 'Maintenance Checklist', 'manage_options', 'maintenance-checklist', 'wpv_maintenance_callback'); 
}

function wpv_maintenance_callback() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		//Gets entire array. Usable throughout function scope
		$maintenance_items = wpv_get_maintenance_values();
		
		if($_POST){
			//For 'Have You Completed...' Sections
			if(!empty($_POST['check-submit'])){
				$post_items = $_POST;
			
				wpv_update_maintenance_values($post_items);
			
				echo '<script type="text/javascript">window.location = "options-general.php?page=maintenance-checklist"</script>';
			}
			
			//For 'Add Task' Sections
			if(!empty($_POST['add-submit'])){
				$task_slug = preg_replace("/[^A-Za-z0-9]/", "-", $_POST['task_name']);
				$task_slug = rtrim($task_slug, "-");
				
				$maintenance_items[$task_slug] = array(
				'ID' => $task_slug,
				'name' => $_POST['task_name'],
				'value' => 'false', 
				'increment' => $_POST['task_increment'],
				'date_completed'=> date('Y-m-d'));

				
				update_option('wpv_maintenance_tracking', $maintenance_items);
				
			}
			
			//For 'Delete Task' Sections
				if(!empty($_POST['delete-submit'])){

					unset($maintenance_items[$_POST['ddl_task_name']]);
					update_option('wpv_maintenance_tracking', $maintenance_items);

				}
		}
	?>

	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Maintenance Checklist</h2>
	<div id="wpv_maintenance_checklist">
	<?php
	//Checks to see if everything is done. If not, show have you completed. If so, enable feel good engine
	if(wpv_check_for_false_values()){
		echo '<h3>Have you completed...</h3>';
		}else{
			
			$wpv_feel_good_responses = array('Looks like you are doing great! Did I tell you your hair looks amazing today?', 'Hey there! Your maintenance tasks are finished for now. Geez you\'re a wonderful person!', ' Your WordPress system is looking pretty good! Must take after its owner!', "Your site is in good shape. Hey...have you been going to the gym? Looking good!");
			
			$wpv_random_response = round(rand(0, count($wpv_feel_good_responses) - 1));
			echo '<h3 class="feelGoodMessage">'. $wpv_feel_good_responses[$wpv_random_response] . '</h3>';	
		}
		?>
	<form method='POST' action="">
		<?php
		//Create Checklist based on items in database array
		foreach($maintenance_items as $item){
			echo '<p><input type="checkbox" name="'. $item['ID'] .'" value="'. $item['ID'] . '" ';
			if($item['value'] == "true"){
				echo 'checked="checked" disabled="disabled"';
				}
			echo '/> '. stripslashes($item['name']) .'</p>';
		}
		?>
		<p class="submit">
			<input type="submit" name="check-submit" id="submit" class="button-primary" value="Save Changes"/>
		</p>	
	</form>
	<hr/>
	<form method="POST" action="">
		<h3>Add Task</h3>
		<p>Task Name: <input type="text" name="task_name"/></p>
		<p>Increment: 
			<select name="task_increment"/>
				<option value='d'>Daily</option>
				<option value='w'>Weekly</option>
				<option value='m'>Monthly</option>
			</select>		
		</p>
			<p class="submit">
				<input type="submit" name="add-submit" id="submit" class="button-primary" value="Add Task"/>
			</p>
	</form>
	<hr/>
	<form method="POST" action="">
		<h3>Delete Task</h3>
		<p>Task Name: 
			
			<select name="ddl_task_name"/>
			<?php
			$maintenance_values = wpv_get_maintenance_values();
			foreach($maintenance_values as $value){
				echo '<option value="'. $value['ID'] .'">' . stripslashes($value['name']) . '</option>';
			}
				?>
			</select>				
		</p>
			<p class="submit">
				<input type="submit" name="delete-submit" id="submit" class="button-primary" value="Delete Task"/>
			</p>
	</form>
	</div>
<?php
}
}
?>