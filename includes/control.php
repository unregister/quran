<?php
function radiobutton($selected, $event, $name, $arr_value,$arr_title,$width){
		foreach($arr_value as $i => $value){
			$hasil .= "<input $event type='radio' value='$value' name='$name' ".($selected==$value?'CHECKED':'')." /> 
					<input disabled style='color:black;background-color:transparent;width:{$width}px;border:none' value='{$arr_title[$i]}' /> ";
		}
		return $hasil;
}
	
function lookup($selected,$event,$name, $table, $value_column, $title_column=''){
	//echo "'$name'";
	if(!$title_column) $title_column = $value_column;
	
	if($value_column!=$title_column)
		$sql = "SELECT $value_column, $title_column FROM $table ORDER BY $title_column ";
	else
		$sql = "SELECT $value_column, $value_column as x FROM $table ORDER BY $title_column ";
	
	$table = get_table($sql);
	$option .="<option value='' ></option>";
	foreach($table as $i => $row){
		if(strtolower($row[0])==strtolower($selected))
			$option .="<option value='{$row[0]}' selected>{$row[1]}&nbsp;</option>";
		else
			$option .="<option value=\"{$row[0]}\">{$row[1]}&nbsp;</option>";
		
		$lookup ="<select $event id=\"$name\" name=\"$name\">
						$option
				   </select>";
	}
	return $lookup;
}

function combobox($selected,$event,$name,$arr_value=array(),$arr_title = array()){
	if(!is_array($arr_title) || !count($arr_title))
		$arr_title = $arr_value;
		
	$jmlValue=count($arr_value);
	$jmlTitle=count($arr_title);
	if($jmlValue!=$jmlTitle){
		$combobox="Invalid ComboBox";
	}else{
		$option .="<option value=\"\" ></option>";
		for($i=0;$i<=($jmlValue-1);$i++){
			$arr_value[$i]   = trim($arr_value[$i]);
			if(strtolower($arr_value[$i])==strtolower($selected)){
				$option .="<option value=\"$arr_value[$i]\" selected>$arr_title[$i]</option>";
			}else{
				$option .="<option value=\"$arr_value[$i]\">$arr_title[$i]</option>";
			}
		}
		$combobox="<select $event  id=\"$name\" name=\"$name\">
						$option
				   </select>";
	}
	
	return $combobox;
} // end combobox()


function autocomplete($text, $event, $name, $arr_autocomplete){
	foreach($arr_autocomplete as $i => $value){
		$value = addslashes(trim($value));
		$source .= " \"$value\",";
	}
	$source = substr($source,0,strlen($source)-1);
	
	return "
		
		<input id=\"$name\" name=\"$name\" $event value=\"$text\"/>
		<script type=\"text/javascript\">
		$(function(){
		//Autocomplete
			$(\"#$name\").autocomplete({
				source: [ $source ]
			});
		});
		</script>
	";
}

function maskedit($name, $mask, $value){
	$size = strlen($mask);
	return"
		<input type='text' id='$name' name='$name' size='$size'/>
		<script type='text/javascript'>
		jQuery(function($){
			   $('#$name').mask('$mask');
			   document.getElementById('$name').value='$value';	
			});
		
		</script>";
}
?>
