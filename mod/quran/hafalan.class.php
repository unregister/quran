<?php
class hafalan{
	function manage_session(){

		if($_REQUEST[page]) 	$_SESSION[page] = $_REQUEST[page];
		if(!$_SESSION[page]) 	$_SESSION[page] = 1;

		if($_REQUEST[ganti_terjemah]) $_SESSION[terjemah] = $_GET[terjemah];

		if($_REQUEST[ganti_sura]&& $_GET[sura_name] ){
			$_SESSION[aya_awal]		= $_GET[aya_awal];
			$_SESSION[aya_akhir]	= $_GET[aya_akhir];

			$_SESSION[ulang_aya]	= $_GET[ulang_aya];
			$_SESSION[ulang_semua]	= $_GET[ulang_semua];
			
			$_SESSION[sura] 	= exec_scalar("SELECT `index` FROM sura WHERE name_indonesia='{$_GET[sura_name]}'  ");
		}
		
		if(!$_SESSION[aya_awal]) 	$_SESSION[aya_awal] 	= 1;
		if(!$_SESSION[aya_akhir]) 	$_SESSION[aya_akhir] 	= 3;
		
		if(!$_SESSION[ulang_aya]) 		$_SESSION[ulang_aya] 	= 1;
		if(!$_SESSION[ulang_semua]) 	$_SESSION[ulang_semua] 	= 3;

		if(!$_SESSION[sura]) $_SESSION[sura] =1;
		

	}
	
	function show(){
		$this->manage_session();
		$script .= "var total_aya; var j = 0 ; var ulang_aya={$_SESSION[ulang_aya]}; var ulang_semua={$_SESSION[ulang_semua]};";
		$script .= "var arr_sound = new Array(); ";
		$script .= "var sudah_ulang_aya=1; var sudah_ulang_semua=1; ";

		$sql = "SELECT qa.`index`
					, name_arabic as sura_name_arabic
					, name_indonesia as sura_name_indonesia
					, qa.sura,qa.aya
					, qa.text as text_arabic
					, replace(qt.text,'<br>','') as text_terjemah
				FROM quran_arabic qa
					LEFT JOIN sura s ON qa.sura =s.`index`
					LEFT JOIN quran_{$_SESSION[terjemah]} qt ON qa.`index` =qt.`index`
				WHERE qa.sura='{$_SESSION[sura]}' AND 
					(qa.aya BETWEEN '{$_SESSION[aya_awal]}' AND '{$_SESSION[aya_akhir]}' ) ";
		$table = get_table($sql);
		
		$j =-1;
		$content .= "
		<table width='100%' border='0'>";
		if($table[0][aya]==1){
			if($table[0][sura]!=1 && $table[0][sura]!=9){
				$title = repair_ar(bismillah());
			}
			else{
				$title = $table[0][sura_name_arabic];
			}
			if($j==-1) 
				$z=0;
			else
				$z=$j;
			$content .= "
				<tr>
					<td colspan='2' align='center'>
						<div class='bismillah_con' >
							<a onclick='j=$z;play()' >$title</a>
						</div>
					</td>
				</tr>
			";
		}
		
		$text_arab = "";
		$text_terjemah="";
		foreach($table as $i => $row){
			$j++;
			$script		.= " arr_sound[$j] = '".SOUND."/{$_SESSION[qari]}/"
							.set_len($row[sura],3,'kanan','0')
							.set_len($row[aya],3,'kanan','0')
							.".mp3?dummy=3';
							";
			
			$row[text_arabic] = $this->text_arabic($row[sura],$row[aya],$row[text_arabic]);
			
			$text_arab = "";
			$text_terjemah = "";

			
			if($row[index]==$_GET[index]){
				$style_tambahan = " style='background-color:#eeeefe;' ";
			}
			else
				$style_tambahan = "";
				
			$text_arab 	.= "<span $style_tambahan id='aya_$j'> ".repair_ar($row[text_arabic]) ." </span>";
			
			if($_SESSION[grup]==1)
				$text_arab .="
							<image style='cursor:pointer' src='images/edit.png' onclick='edit_terjemah_kata({$row[sura]},{$row[aya]},event)'>";
							
			$text_arab .="
							<a href='javascript:play($j)' class='sura_no'
								onmousemove='move_petunjuk(\"Klik ini untuk mendengarkan\",event)' 
								onmouseout='hide_petunjuk()'
								>
								<span class='kurung'>" . l2a(')') ."</span>".
								$row[sura_name_arabic] . ":" . l2a( $row[aya] )  
								."<span class='kurung'>" . l2a('(') ."</span>"
							.'</a >&nbsp;'; 
							
			$text_terjemah .= "
							<a href='javascript:play($j)' class='sura_no'
								onmousemove='move_petunjuk(\"Klik ini untuk mendengarkan\",event)' 
								onmouseout='hide_petunjuk()'
								>
								<span class='kurung'>" . l2a('(') ."</span>
									{$row[sura_name_indonesia]}:{$row[aya]}
								<span class='kurung'>" . l2a(')') ."</span><br>
							</a> 
							<span 
								onmousemove='move_petunjuk(\"Klik nomor surat untuk mendengarkan\",event)' 
								onmouseout='hide_petunjuk()'
								id='terjemah_$j' class='latin' 
								$style_tambahan >{$row[text_terjemah]} ".
									($_SESSION[grup]==1?"
									<image style='cursor:pointer' src='images/edit.png' onclick='edit_terjemah({$row[sura]},{$row[aya]},event)'>":"")."
							</span>
							";
			$content .= "
			<tr>
				<td class='terjemah' style='border-bottom:dotted 1px #888888;font-size:{$_SESSION[font_size_terjemah]}' >
					 $text_terjemah
				</td>
				<td 
					class='arab' style='border-bottom:dotted 1px #888888;font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]}'>
					$text_arab 
				</td>
			</tr>";
		}
		$content .="
		</table>";
		
		$script .= "total_aya=".count($table).";  ";

		
		include("mod/quran/hafalan.html");
	}
	
	function text_arabic($sura,$aya,$text){
		//mengambil data terjemah per kata
		$table_per_kata	= get_table("SELECT * FROM terjemah_kata WHERE sura='$sura' 
									AND aya='$aya'");
		// inisialisasi
		$arr_terjemah = array();
		foreach($table_per_kata as $j => $row_per_kata){
			$m = $j+1;
			$arr_terjemah[$m] 		= $row_per_kata[indonesia]; 
			$arr_arab_harokat[$m] 	= $row_per_kata[arab_harokat]; 
		}
		
		// explode aya text into word
		$arr_kata_arab	= explode(" ",$text);
		
		//menampilkan tiap kata beserta terjemahnya
		/*echo "
		<pre>
		$sura:$aya ";*/
		
		$m = 1;
		foreach($arr_kata_arab as $k => $kata_arab){
			$event_aya_word ="";
			//menampilkan terjemahan per kata
			//echo "$kata_arab {$arr_arab[$m]}<br>";
			if($kata_arab==$arr_arab_harokat[$m] ){
				$output .= "
				<span id='terjemah_{$sura}_{$aya}_{$m}' 
					style='visibility:hidden;position:absolute;width:0px;height:0px;' >
					{$arr_terjemah[$m]}
				</span>
				";
				$event_aya_word ="
				onmousemove='move_terjemah($sura,$aya,$m,event)' 
				onmouseout='hide_terjemah($sura,$aya,$m)'
				";
				
				$id_kata = "kata_{$sura}_{$aya}_{$m}";
				$m++;
			}
			//menampilkan kata arab
			$output .= "
			<span 
				id='$id_kata'
				class='aya_word'
				$event_aya_word  >
				".repair_ar($kata_arab)."
			</span>&nbsp;&nbsp;
			";
		}
		return $output;
	}
	
	
}

?>