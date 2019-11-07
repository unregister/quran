<?php
class pencarian{
	var $spasi = array(" ","  ","   ","    ","    ","    ");
	var $illegal_char = array("_","/" , "" ,".",">","<",")","(","QS","qs","terjemah","tafsir","surat","surah","Surat","Surah");


	function manage_session(){
		//echo_pre($_POST);
		//echo_pre($_SESSION);
		if($_REQUEST[cari]){
			if(($_POST[cmb_bahasa]!='arabic_simple' && $_SESSION[bahasa]=='arabic_simple')){
				$_SESSION[sura_aya] = '';
				$_SESSION[kalimat] 	= '';
			}
			elseif(($_POST[cmb_bahasa]=='arabic_simple' && $_SESSION[bahasa]!='arabic_simple')){
				$_SESSION[sura_aya] = '';
				$_SESSION[kalimat] 	= '';
			}
			else{
				$_SESSION[sura_aya] = $_REQUEST[txt_sura_aya];
				$_SESSION[kalimat] = $_REQUEST[txa_kalimat];
			}

			$_SESSION[bahasa] = $_REQUEST[cmb_bahasa];
			$_SESSION[page_cari] = 1;
		}
		
		if($_GET[page_cari]) $_SESSION[page_cari] = $_GET[page_cari];
		
		if(!$_SESSION[page_cari]) $_SESSION[page_cari] = 1;
		
		if(!$_SESSION[bahasa]) 
			$_SESSION[bahasa] = indonesia;
			
		$this->filter_session();
	}

	function frm_cari(){
		$arr_value_bahasa  = array("indonesia",'arabic_simple','english');
		$arr_title_bahasa  = array("Indonesia",'Arabic','English');
		
		return "
		<form method='post' name='frm_cari' action='?mod=quran.pencarian.show&cari=1'>
		<table align='center' class='frm_cari' border='0'>
			<tr>
				<td width='120px'>Bahasa</td> 
				<td width='10px'>:</td> 
				<td>
					".combobox($_SESSION[bahasa]," onchange='frm_cari.submit()' style='width:200px' ","cmb_bahasa",$arr_value_bahasa,$arr_title_bahasa)."
				</td>
			</tr>
			<tr>
				<td width='150px'>Kata Kunci Surat</td> 
				<td width='10px'>:</td> 
				<td>
					<input name='txt_sura_aya' style='width:300px' value=\"".
					$_SESSION[sura_aya]. "\">
				</td>
			</tr>
			<tr>
				<td width='150px' valign='top'>Kata Kunci Kalimat</td> 
				<td width='10px' valign='top'>:</td> 
				<td>
					<textarea id='txa_kalimat'  name='txa_kalimat' style='height:60px;min-width:300px;"
						.($_SESSION[bahasa]=='arabic_simple'?"direction:rtl;font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]}":'')
						."'>{$_SESSION[kalimat]}</textarea>
						<br>
					<input type='submit' value='OK' style='width:15%;height:50px;float:right'>
				</td>
			</tr>
		</table>
		</form>". ($_SESSION[bahasa]=='arabic_simple'?
			'
			<script type="text/javascript" src="includes/vk/keyboard.js?dummy=1" charset="UTF-8"></script>
			<link rel="stylesheet" type="text/css" href="includes/vk/keyboard.css?dummy=1">
			<script>
					var myInput = document.getElementById("txa_kalimat");
					if (!myInput.VKI_attached) VKI_attach(myInput);
			</script>
			':'');
		
	}

	function show(){
		$this->manage_session();
		$content .= $this->frm_cari();

		$aya_per_page=5;
		
		$this->c_where_sura($where_sura,$bobot_sura);
		$this->c_where_aya($where_aya,$bobot_aya);
		
		$where = " $where_sura AND $where_aya ";
		$bobot = " $bobot_sura + $bobot_aya ";
		//echo_pre($_SESSION);
		
		echo "<span style='visibility:hidden' id='next_page'>".($_SESSION[page_cari]+1)."</span>";
		
		$start_index  = ( $_SESSION[page_cari] -1 ) * $aya_per_page;
		$sql = "SELECT `index`,sura,aya, text as text_terjemah
			, 
				( SELECT name_arabic FROM sura WHERE quran_{$_SESSION[bahasa]}.`sura`=sura.`index` 
				) as sura_name_arabic
			, 
				( SELECT name_indonesia FROM sura WHERE quran_{$_SESSION[bahasa]}.`sura`=sura.`index` 
				) as sura_name_indonesia
			,
				( SELECT text FROM quran_arabic WHERE quran_{$_SESSION[bahasa]}.`index`=quran_arabic.`index` 
				) as text_arabic
			FROM quran_{$_SESSION[bahasa]} 
			WHERE $where
			ORDER BY $bobot, sura 
			LIMIT $start_index, $aya_per_page";

		$table = get_table($sql);
		
		//echo_pre($table);
		
		//return;
		$count = exec_scalar("SELECT COUNT(*) FROM quran_{$_SESSION[bahasa]} 
							WHERE $where ");
		if($count==0){
			$str_count = "<b style='color:brown;font-size:11px;font-family:verdana'>
				  Data tidak ditemukan
				  </b>";
		}
		else{
			$str_count .= "<b style='color:brown;font-size:11px;font-family:verdana'>
				  Data yang ditemukan : $count
				  </b>";
		}
		
		$content .= "
		<table width='100%' border='0'>
			<tr>
				<td colspan='2' align='center'>
				$str_count
				</td>
			</tr>
		";
		
		$text_arab = "";
		$text_terjemah="";
		foreach($table as $i => $row){
			$j++;
	
			$row[text_arabic] = $this->text_arabic($row[sura],$row[aya],$row[text_arabic]);
			
			if($_SESSION[bahasa]=='arabic_simple') 
				$row[text_arabic] = $this->tandai($row[text_terjemah]);
			else
				$row[text_arabic] = repair_ar($row[text_arabic]) ;
				
			
			$page 		= exec_scalar("SELECT page FROM quran_arabic WHERE sura='{$row[sura]}' AND aya={$row[aya]}");
			$text_arab 	= "<span id='aya_$j'> {$row[text_arabic]} </span>
							<a href='?mod=quran.murotal.show&page=$page&index={$row[index]}#kata_{$row[sura]}_{$row[aya]}_1' class='sura_no'
								onmousemove='move_petunjuk(\"Klik ini untuk melihat ayat lanjutan\",event)' 
								onmouseout='hide_petunjuk()'
								>
								<span class='kurung'>" . l2a(')') ."</span>".
								$row[sura_name_arabic] . ":" . l2a( $row[aya] )  
								."<span class='kurung'>" . l2a('(') ."</span>"
							.'</a >&nbsp;'; 
							
			$text_terjemah = "
							<a href='?mod=quran.murotal.show&page=$page&index={$row[index]}#kata_{$row[sura]}_1_1' class='sura_no'
								onmousemove='move_petunjuk(\"Klik ini untuk melihat ayat lanjutan\",event)' 
								onmouseout='hide_petunjuk()'
								>
								<span class='kurung'>" . l2a('(') ."</span>
									{$row[sura_name_indonesia]}:{$row[aya]}
								<span class='kurung'>" . l2a(')') ."</span><br>
							</a> 
							<span 
								onmousemove='move_petunjuk(\"Klik nomor surat untuk melihat ayat lanjutan\",event);nyala($j);' 
								onmouseout='hide_petunjuk();padam($j);'
								id='terjemah_$j' class='latin' >".$this->tandai($row[text_terjemah])." </span><br>
							";
			if($_SESSION[bahasa]=='arabic_simple'){
				$content .="
					<tr>
						<td 
							class='arab' style='font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]};border-bottom:dotted 1px #777777'>
							$text_arab 
						</td>
					</tr>";
				
			}
			else{
				$content .="
					<tr>
						<td class='terjemah' style='font-size:{$_SESSION[font_size_terjemah]};border-bottom:dotted 1px #777777'  >
							 $text_terjemah
						</td>
						<td 
							class='arab' style='font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]};border-bottom:dotted 1px #777777'>
							$text_arab 
						</td>
					</tr>";
			}		

		}
		$content .="
			</table>";

		include("mod/quran/pencarian.html");
		if($_REQUEST[autostart])
			echo "
			<script>play(0)</script>";
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
			</span>
			";
		}
		return $output;
	}
	
	function tandai($aya){
		$syarat_ayat = str_replace("'","\'", trim($_SESSION[kalimat]));
		$syarat_ayat = str_replace("@","",$syarat_ayat);
		
		// JIKA syarat_ayat hanya satu kata
		if($syarat_ayat==str_replace(" ","",$syarat_ayat))
			$arr_syarat_ayat[0] = $syarat_ayat;
		else
			$arr_syarat_ayat= explode(" ",$syarat_ayat);
		
		if($_SESSION[bahasa]=='indonesia'){
			foreach($arr_syarat_ayat as $key =>$value){
				if(!$value) continue;
				$kata_tunggal = $this->remove_prefix_sufix($value);
				if($kata_tunggal)
					$arr_kata_tunggal[] = $kata_tunggal;
			}
		}
		
		foreach($arr_syarat_ayat as $key => $value){
			if(!$value || $value=='-') continue;
			$panjangDitemukan = "0";
			$ditemukan = "";
			
			if($cs){
				//$objResponse->alert($value);
				$pos = strpos(strtolower($aya),strtolower($value));
				if($pos>-1 && strpos($aya,$value) === false )
					$ok = false;

				$posDitemukan = strpos($aya,$value);
			}
			else{
				$posDitemukan = strpos(strtolower($aya),strtolower($value));
			}

			$kata_tunggal = $this->remove_prefix_sufix($value);
			if(strpos($aya,$value)>-1){
				$arrPrinted[] = $kata_tunggal;
			}
			
			
			$panjangDitemukan = strlen($value);
			if($posDitemukan>-1){
				$ditemukan = substr($aya,$posDitemukan,$panjangDitemukan);
				$aya = str_replace($ditemukan,"<span style='color:DarkRed;font-weight:bold' >$ditemukan</span>",$aya);	
			}
		}
			
		if($arr_kata_tunggal&&count($arr_kata_tunggal)>0)
			foreach($arr_kata_tunggal as $key =>$value){
				if(!value) continue;
				$posDitemukan = strpos(strtolower($aya),strtolower($value));
				$panjangDitemukan = strlen($value);
				if(is_array($arrPrinted))
					$isPrinted=in_array($value,$arrPrinted);
				else
					$isPrinted = false;
				
				
		//		echo "$value $posDitemukan $isPrinted :"; 
				if($posDitemukan>-1&&!$isPrinted){
					$ditemukan = substr($aya,$posDitemukan,$panjangDitemukan);
					$aya = str_replace($ditemukan,"<span style='color:DarkRed;font-weight:bold' >$ditemukan</span>",$aya);				
				}
			}
		
		return $aya;
		
	}
	
	function mengandung($kata1,$kata2){
		if(strpos($kata1,$kata2 )>-1)
			return 1;
		else
			return 0; 
	}
	
	function c_where_sura(&$where, &$bobot){
		$arr_al = array("Al-","Al ","Ash ","Ash-","Az-","Az ","At-","Ath-","At ","An-","An ","Ar ","Asy ","As ","Adh ");

		$bobot = ' 0 ';
		$where = ' 1 ';
		if(!$_SESSION[sura_aya]) return;
		$syarat_sura = str_replace("'","\'",$_SESSION[sura_aya]);
		$syarat_sura = str_ireplace("surat","",$syarat_sura);
		$syarat_sura = str_ireplace("sampai"," - ",$syarat_sura);
		$syarat_sura = str_ireplace("dan",",",$syarat_sura);
		
		$sql_sura ;
		/* jika kata mengandung kata Al  */
		foreach($arr_al as $key_al => $al){
			// Cari posisi al
			$pos_al =strpos(strtolower($syarat_sura),strtolower($al));
			// menghilangkan Al
			if($pos_al===0){
				$syarat_sura = trim(substr($syarat_sura,strlen($al)-1,strlen($syarat_sura) - strlen($pos_al) ));
				break;
			}
			$al = "";
		}
		//echo $syarat_sura;
		/* MENCARI POSISI SYMBOL PEMISAH ANTARA SURA DAN AYAT
			MISAL :
				Al baqarah ayat 1-5  => ayat
				Al baqarah : 1-5  => :
				Al baqarah 1  => spasi
		*/
		$syarat_sura = str_ireplace("ayat",":",$syarat_sura);
		$symbol = ":";
		$pos_symbol = strpos(strtolower($syarat_sura),$symbol);
		if(!$pos_symbol){
			$symbol = " ";
			$pos_symbol = strpos(strtolower($syarat_sura),$symbol);
		}
		//-----------------------------------------------------------
		
		/* Mengambil nama sura_name saja */
		if($pos_symbol=='')
			$sura = $syarat_sura;
		else	
			$sura =  trim(substr($syarat_sura,0, $pos_symbol));
		
		// JIKA kata kunci mengandung AL maka $sura = AL + nama sura;
		if($al&&$sura)
			$sura = $al .$sura;
			
		/* Mengambil aya saja */
		$ayat = trim(substr($syarat_sura, $pos_symbol + strlen($symbol), strlen($syarat_sura) - strlen($pos_symbol) ));

		
		/*echo surat .":". $sura . "<br/>";
		echo ayat .":". $ayat."<br/>";
		-------------------- 
		misal : 
		2:1-3   
		Baqarah  1-4 
		Baqarah:1-4 
		Baqarah ayat 1-4 
		
		Al Baqarah --------------------------*/
		if($this->mengandung($ayat,"-")){
			$arr_ayat = explode("-",$ayat);
			$ayat_awal = $arr_ayat[0];
			$ayat_akhir = $arr_ayat[1];
			$where = "( instr(sura_name,'$sura') or (sura_name sounds like '$sura') or sura='$sura')
						AND (aya BETWEEN '$ayat_awal' AND  '$ayat_akhir')";
		}
		/*---------------------  misal : 2:1,5,7,8       --------------------------
		---------------------  misal : Baqarah:1,5,7,8       --------------------------*/
		elseif($pos_symbol){
			if($this->mengandung($ayat,','))
				$ayat =  "$ayat,";
			$arr_ayat = explode(",",$ayat);
			$where = " ( instr(sura_name,'$sura') or (sura_name sounds like '$sura') or sura='$sura') ";
			
			foreach($arr_ayat as $key =>$value){
				$where_ayat .= " aya ='$value' OR";
			}
						
			$where_ayat = substr($where_ayat,0,strlen($where_ayat) -2) ;
			$where .=  " AND ($where_ayat) ";
		}
		else
			$where = " ( instr(sura_name,'$sura') or (sura_name sounds like '$sura') or sura='$sura') ";
		
		$bobot_surat = "
		IF(sura_name='$sura',-8,0) +
		IF(
		REPLACE(REPLACE(REPLACE(REPLACE(LOWER(sura_name),'a',''),'u',''),'i',''),'\'','')
		=
		REPLACE(REPLACE(REPLACE(REPLACE(LOWER('$sura'),'a',''),'u',''),'i',''),'\'','')
		,-4,0) +
		IF(instr(sura_name,'$sura'),-2,0) + 
		IF((sura_name sounds like '$sura'),-1,0) 
		";

		// JIKA kata kunci tidak mengandung al
		if(!$sura){
			$sura = $syarat_sura;
			$bobot_surat = "
				IF(sura_name='$sura',-8,0) +
				IF(
				REPLACE(REPLACE(REPLACE(REPLACE(LOWER(sura_name),'a',''),'u',''),'i',''),'\'','')
				=
				REPLACE(REPLACE(REPLACE(REPLACE(LOWER('$sura'),'a',''),'u',''),'i',''),'\'','')
				,-4,0) +
				IF(instr(sura_name,'$sura'),-2,0) +
				IF((sura_name sounds like '$sura'),-1,0) 
				";
			return "(sura_name sounds like '$sura' OR sura='$sura' OR  instr(sura_name,'$sura') OR instr(sura_name,'". str_replace($arr_al,"", $syarat_sura)."'))";
		}
		
		$bobot = $bobot_surat;
		$min_bobot = exec_scalar("SELECT min($bobot) FROM quran_{$_SESSION[bahasa]} ");
		if($min_bobot<='-15')
			$where = " $where AND ($bobot)<='-15' ";
		//echo $bobot."<br/>";
		//echo $where;
	}

	function remove_empty($arr){
		foreach($arr as $key => $value){
			if($value)
				$new_arr[] = $value;
		}
		return $new_arr;
	}

	function remove_prefix($string){
		$prefix = array("se","memper","mem","meny","meng","men","me","be","per","di","ke","pem","peny","peng"," pe"," ter");
	
		foreach($prefix as $key => $value){
			if(substr($string,0,strlen($value))==$value){
				$hasil = substr($string,strlen($value));
				if(strlen($hasil)>3)
					return $hasil;
			}
		}
		return false;
	}

	function remove_sufix($string){
		$sufix = array("kanlah","ilah","imu","iku","inya","i","lah","kan","an","kah","nya","mu","ku");
	
		foreach($sufix as $key => $value){
			if(substr($string,0-strlen($value))==$value){
				$hasil = substr($string,0,0-strlen($value));
				if(strlen($hasil)>3)
					return $hasil;
			}
		}
		return false;
	}

	function remove_prefix_sufix($string){
		if($this->remove_sufix($string)){
			if($this->remove_prefix($string))
				return $this->remove_prefix($this->remove_sufix($string));
			else
				return $this->remove_sufix($string);
		}
		else{
			return $this->remove_prefix($string);
		}
	}
	
	function c_where_aya(&$where, &$bobot){
		
		$kata_hubung = array(" dan "," ke "," di "," yang "," dengan ");
		
		$syarat_ayat = str_replace("'","\'", trim($_SESSION[kalimat]));
		//echo $syarat_ayat ;
		/* CASE SENSITIVE menggunakan BINARY search*/
		if($this->mengandung($syarat_ayat,'@')){
			$binary = " BINARY ";
			$syarat_ayat = str_replace("@","",$syarat_ayat);
		}

		// JIKA syarat_ayat kosong
		if(!$syarat_ayat){
			$bobot = " -1 ";
			$bobot_ideal = ' -1 ';
		}
		// JIKA syarat_ayat hanya satu kata
		elseif($syarat_ayat==str_replace(" ","",$syarat_ayat)){
			$arr_syarat_ayat[0] = $syarat_ayat;
			$bobot = "( IF(
					(instr($binary text,' $syarat_ayat ')>0 or
					instr($binary text,' $syarat_ayat,')>0 or
					instr($binary text,',$syarat_ayat')>0 or 
					instr($binary text,'.$syarat_ayat ')>0) 
					,'-4','0') + 
					IF(instr($binary text,'$syarat_ayat')>0 
					,'-2','0') )";
			$bobot_ideal = " -2 ";
		}
		elseif($this->mengandung($syarat_ayat," ")){
			$syarat_ayat = str_replace($kata_hubung," ",$syarat_ayat);
			$syarat_ayat_tunggal = $syarat_ayat;
			/*echo $syarat_ayat; */
			$arr_syarat_ayat= explode(" ",$syarat_ayat);
			$bobot_ideal = -1 * count($arr_syarat_ayat) ;
		
			//print_arr($arr_syarat_ayat);
			$arr_syarat_ayat= $this->remove_empty($arr_syarat_ayat);
			for($i = 0 ;$i<count($arr_syarat_ayat);$i++){
				$value = $arr_syarat_ayat[$i];
				
				if(!$value) continue;
			
				/* 
				bobot 20 untuk tiap ada 2 kata yang berdekatan misal
				Ayat : Dengan nama Allah yang maha pengasih dan penyayang
				kata kunci : pengasih penyayang
				*/
				if($arr_syarat_ayat[$i+1])
				$bobot .= 
					"
					/* bobot kata berdekatan */
					IF( 
						instr($binary text,'{$arr_syarat_ayat[$i]}') 
						AND 
						instr($binary text,'{$arr_syarat_ayat[$i+1]}')
						AND
						instr(
						substring(text, instr( $binary text,'{$arr_syarat_ayat[$i]}'), length(text) - instr( $binary text,'{$arr_syarat_ayat[$i]}') )
									,'{$arr_syarat_ayat[$i+1]}') <> 0
						AND
						instr(
						substring(text, instr( $binary text,'{$arr_syarat_ayat[$i]}'), length(text) - instr( $binary text,'{$arr_syarat_ayat[$i]}') )
									,'{$arr_syarat_ayat[$i+1]}') < 20
									
						,
						- 30 + 
						instr(
						substring(text, instr( $binary text,'{$arr_syarat_ayat[$i]}'), length(text) - instr( $binary text,'{$arr_syarat_ayat[$i]}') )
									,'{$arr_syarat_ayat[$i+1]}')
						
						,0)
						
						
						
						+";
						
				/*tiap ada kata yang hampir bobot ditambah  2 misal
				Ayat : menciptakan langit dan bumi
				kata kunci : cipta
				$bobot .= " IF(instr($binary ayat,'$value')>0,-2,0) + ";

				/* 
				tiap ada kata yang persis sama bobot ditambah  4 misal
				Ayat : menciptakan langit dan bumi
				kata kunci : cipta*/
				$bobot .= " IF(
					(instr($binary text,' $value ')>0 or
					instr($binary text,' $value,')>0 or
					instr($binary text,',$value')>0 or 
					instr($binary text,'.$value ')>0) 
					,'-4','0') +";
				/* Jadi setiap ada kata yang persis sama akan mendapat bobot 6  */
			}
		
			/*echo $bobot; 
			
			/* 
			tiap ada yang kata dasarnya cocok ditambah  2 misal
			Ayat : menciptakan langit dan bumi
			kata kunci : penciptaan
			*/
			if($_SESSION[bahasa]=='indonesia'){
				foreach($arr_syarat_ayat as $key =>$value){
					if(!$value) continue;

					$kata_tunggal = $this->remove_prefix_sufix($value);
					/*echo "syarat_ayat_tunggal  $syarat_ayat_tunggal , $kata_tunggal,$value<br/>";  */
					
					if($kata_tunggal){
						$arr_kata_tunggal[] = $kata_tunggal;
						/*echo $kata_tunggal; */
						$bobot .= "
						/* bobot kata tunggal */
						IF(instr($binary text,'$kata_tunggal')>0 and not(instr(text,'$value')>0),'-2','0') 
						
						
						+";
					}
						/*menambahkan bobot surat*/
				}
			}
			
			$tmpsyarat_ayat = str_replace(" ","%",$syarat_ayat);

			$bobot = substr($bobot,0,strlen($bobot) -1) ;
			
		}
		
		$where = " ($bobot <= $bobot_ideal) " ;
	}
	
	function filter_session(){
		$_SESSION[sura_aya] = str_replace("\\","", $_SESSION[sura_aya]);
		$_SESSION[kalimat] = str_replace("\\","", $_SESSION[kalimat]);
	}
	
	
}

?>