<?php
class murotal{
	function manage_session(){

		if($_REQUEST[page]) 	$_SESSION[page] = $_REQUEST[page];
		if(!$_SESSION[page]) 	$_SESSION[page] = 1;

		if($_REQUEST[ganti_terjemah]) $_SESSION[terjemah] = $_GET[terjemah];

		if($_REQUEST[ganti_sura]&& $_GET[sura_name] ){
			if(!$_GET[aya]) $_GET[aya] = 1;
			
			$_GET[sura] 		= exec_scalar("SELECT `index` FROM sura WHERE name_indonesia='{$_GET[sura_name]}'  ");
			$_SESSION[page] 	= exec_scalar("SELECT page FROM quran_arabic WHERE sura='{$_GET[sura]}' AND aya='{$_GET[aya]}'");
			$_GET[index]		= exec_scalar("SELECT `index` FROM quran_arabic WHERE sura='{$_GET[sura]}' AND aya='{$_GET[aya]}'");
		}

	}
	
	function show(){
		$this->manage_session();
		$script .="var auto_resume={$_SESSION[auto_resume]} ;var arr_sound = new Array(); ";
		if($_SESSION[grup]==1){
			echo "<div style='position:absolute;right:20px' id='form_terjemah_kata'></div>";
			echo "<div style='position:absolute;left:25px' id='form_terjemah'></div>";
		}	
		echo "<span style='visibility:hidden' id='next_page'>".($_SESSION[page]+1)."</span>";

		$arr_sura =  get_column("SELECT distinct sura FROM quran_arabic WHERE page='{$_SESSION[page]}' order by sura");
		$j = -1;
		foreach($arr_sura as $i => $sura){
			$sql = "SELECT qa.`index`
						, name_arabic as sura_name_arabic
						, name_indonesia as sura_name_indonesia
						, qa.sura,qa.aya
						, qa.text as text_arabic
						, replace(qt.text,'<br>','') as text_terjemah
					FROM quran_arabic qa
						LEFT JOIN sura s ON qa.sura =s.`index`
						LEFT JOIN quran_{$_SESSION[terjemah]} qt ON qa.`index` =qt.`index`
					WHERE page='{$_SESSION[page]}' AND qa.sura='$sura' ";
			$table = get_table($sql);
			
			
			$content .= "
			<table width='100%' border='0'>";
			if($table[0][aya]==1){
				if($sura!=1 && $sura!=9){
					$j++;
					$title = repair_ar(bismillah());
					$script		.= " arr_sound[$j] ='sound/{$_SESSION[qari]}/001001.mp3';
									";
				}
				else
					$title = $table[0][sura_name_arabic];
					
				if($j==-1) 
					$z=0;
				else
					$z=$j;
				$content .= "
					<tr>
						<td colspan='2' align='center'>
							<div class='bismillah_con' >
								<a id='aya_$z' onclick='j=$z;play()' >$title</a>
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
								<a onclick='j=$j;play()' class='sura_no'
									onmousemove='move_petunjuk(\"Klik ini untuk mendengarkan\",event)' 
									onmouseout='hide_petunjuk()'
									>
									<span class='kurung'>" . l2a(')') ."</span>".
									$row[sura_name_arabic] . ":" . l2a( $row[aya] )  
									."<span class='kurung'>" . l2a('(') ."</span>"
								.'</a >&nbsp;'; 
								
				$text_terjemah .= "
								<a onclick='j=$j;play()' class='sura_no'
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
		}
		include("mod/quran/murotal.html");
		if($_REQUEST[autostart])
			echo "
			<script>play()</script>";
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
	
	function form_terjemah_kata(){
		echo "
		<input type='hidden' id='hdn_edited_sura' value='{$_REQUEST[sura]}'>
		<input type='hidden' id='hdn_edited_aya' value='{$_REQUEST[aya]}'>
		<div style='background-color:#dddddd'>
		<table style='margin:10px;font-family:verdana;font-size:11px;border-collapse:collapse;background-color:white'
				width='600px' border='1'>
<!--			<tr  >
				<th colspan='4'>Perubahan Terjemah Per Kata</th>
			</tr >
-->			<tr>
				<th>No</th>
				<th>Indonesia</th>
				<th>Arab Simple</th>
				<th>Arab</th>
			</tr>
			";
			
		$sql = "SELECT * FROM terjemah_kata WHERE sura='{$_REQUEST[sura]}' AND aya='{$_REQUEST[aya]}' ";
		$table = get_table($sql);
		foreach($table as $i => $row){
			echo "
				<tr>
					<td align='center'>".($i+1)."</td>
					<td>
						<textarea id='txt_indonesia_$i' style='width:100%;height:100%' >".trim($row[indonesia])."</textarea>
					</td>
					<td class='aya_word' align='right' style='padding:5px;font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]}'>
						{$row[arab]}
					</td>
					<td class='aya_word' align='right' style='padding:5px;font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]}'>
						{$row[arab_harokat]}
					</td>
				</tr>
			";
		}
		
		echo"
		</table>
			<input type='button' onclick='save_terjemah_kata()' style='margin:10px' value='Save' >
			<input type='button' onclick='batal_save()' style='margin:10px' value='Batal' >
		</div>
		";
	}
	
	function save_terjemah_kata(){
		$sura 	= $_POST[sura];
		$aya 	= $_POST[aya];
		$arr_indonesia = $_POST[arr_indonesia];
		
		foreach($arr_indonesia as $i => $indonesia){
			$no = $i+1;
			exec_sql("UPDATE terjemah_kata SET indonesia='$indonesia' WHERE sura='$sura' AND aya='$aya' AND no='$no' ",1);
		}
		echo "berhasil";
	}
	
	function form_terjemah(){
		$terjemah = exec_scalar("SELECT text FROM quran_{$_SESSION[terjemah]} WHERE sura='{$_REQUEST[sura]}' AND aya='{$_REQUEST[aya]}' ");
		echo "
		<input type='hidden' id='hdn_edited_sura' value='{$_REQUEST[sura]}'>
		<input type='hidden' id='hdn_edited_aya' value='{$_REQUEST[aya]}'>
		<div style='background-color:#dddddd'>
		<table style='margin:10px;font-family:verdana;font-size:11px;border-collapse:collapse;background-color:white'
				width='600px' border='0'>
				<tr>
					<td width='50px'>Terjemah</td>
					<td width='5px'>:</td>
					<td>
						<textarea id='txt_terjemah' style='width:100%;height:100%'>$terjemah</textarea>
					</td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' onclick='save_terjemah()' value='Save'>
						<input type='button' onclick='batal_save()' value='Batal'>
					</td>
				</tr>
		</table>
		</div>
		";
	}
	
	function save_terjemah(){
		exec_sql("UPDATE quran_{$_SESSION[terjemah]} SET text='{$_POST[text]}'  
				WHERE sura='{$_POST[sura]}' AND aya='{$_POST[aya]}' ");
	}
	
}

?>