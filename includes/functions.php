<?php
//retrives the option for display
function wpv_get_maintenance_values(){
	$return_variable = get_option('wpv_maintenance_tracking', 'No Data Entry');
	return 	$return_variable;
}

//updates options based on what is set to true
function wpv_update_maintenance_values($input_true){
	$maintenance_values = wpv_get_maintenance_values();
	
	foreach($input_true as $true_value){
		if($true_value != 'Save Changes'){
			$maintenance_values[$true_value]['value'] = "true";
			$maintenance_values[$true_value]['date_completed'] = date('Y-m-d');
		}
	}
	
	update_option('wpv_maintenance_tracking', $maintenance_values);
}

function wpv_check_for_false_values(){
	$value_to_check = wpv_get_maintenance_values();
	
	foreach($value_to_check as $value){
		$wpv_check_if_false_exist .= $value["value"] . ',';
	}
	
	if(strpos($wpv_check_if_false_exist, "false") !== false){
		return true;
	}else{
		return false;
	}
	
}

//Checks to see if maintenance has been done on time
function wpv_check_time_for_maintenance(){
	$maintenance_values = wpv_get_maintenance_values();
	
	while ($keys = current($maintenance_values)) {
	    $key_array[] = key($maintenance_values);
		next($maintenance_values);
	}
	
	foreach($key_array as $key){
		if($maintenance_values[$key]['value'] == "true"){
			$interval_stamp = $maintenance_values[$key]["increment"];
			$date1 = new DateTime($maintenance_values[$key]['date_completed']);
			$date2 = new DateTime(date('Y-m-d'));
		
			$interval = $date1->diff($date2);
			$diff = $interval->$interval_stamp;
			
			if($diff > 0){
				$maintenance_values[$key]['value'] = "false";
			}

		}
	}
	
	update_option('wpv_maintenance_tracking', $maintenance_values);
}

?>