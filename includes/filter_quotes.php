<?php
function filter_quotes(&$arr){
	foreach($arr as $i => $value){
		if(is_array($value)){
			filter_quotes($value);
		}
		else{
			$arr[$i]   = addslashes($value);
		}
	}
}

if(!get_magic_quotes_gpc()){
	filter_quotes($_REQUEST);
	filter_quotes($_POST);
	filter_quotes($_GET);
	
}

?>
