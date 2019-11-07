<?php
class page_generator{

	function proses(){
		$sql = "SELECT `index` as idx, sura, aya, LENGTH(text) as huruf FROM quran_arabic_simple  ";
		$table = get_table($sql);
		$huruf 	= 0;
		$page 	= 1;
		$limit  = 350;
		foreach($table as $i => $row){
			$huruf += $row[huruf] + 30;

			exec_sql("UPDATE quran_arabic SET page='$page'  WHERE `index` = '{$row[idx]}' ");
			
			if($huruf > $limit){
				$page++;
				$huruf=0;
			}
		}
		
		
	}
}

?>