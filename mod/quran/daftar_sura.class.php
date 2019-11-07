<?php

class daftar_sura{
	function header_table(){
		return "
		<table class='daftar_sura' width='20%' border='1' >
			<tr>
				<th > No</th>
				<th >Nama </th>
				<th >Ayat </th>
			</tr>			
		";
	}
	function show(){
		$sql = "SELECT `index`, name_arabic, name_indonesia, ayas
				,(SELECT page FROM quran_arabic qa WHERE qa.sura=s.`index` AND aya='1') as page
				FROM sura s ORDER BY `index` ";
		$table = get_table($sql);
		for($x=0;$x<4;$x++){
			$content .= $this->header_table();
			for($i=0;$i<30;$i++){
				$row 	  = $table[$i + $x*30];
				if(!$row[name_arabic]) break;
				$content .= "
				<tr onclick='window.location=\"?mod=quran.murotal.show&page={$row[page]}#kata_{$row[index]}_1_1\" ' >
					<td >".($i + $x*30 +1)."</td>
					<td style='line-height:200%' >{$row[name_indonesia]}</td>
					<td >{$row[ayas]}</td>
				</tr>
				";
			}
			
			$content .="
			</table>
			";
		}	
		include("mod/quran/daftar_sura.html");
		
	}

}
?>