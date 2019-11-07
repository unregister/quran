<?php
class pencarian{
	var $spasi = array(" ","  ","   ","    ","    ","    ");
	var $illegal_char = array("_","/" , "" ,".",">","<",")","(","QS","qs","terjemah","tafsir","suratt","surath","suratt","surath");


	function manage_session(){
		if($_REQUEST[cari]){
			$_SESSION[kitab] = $_REQUEST[cmb_kitab];
			$_SESSION[surat] = $_REQUEST[txt_surat];
			$_SESSION[pasal] = $_REQUEST[txt_pasal];
			$_SESSION[no_ayat] = $_REQUEST[txt_no_ayat];
			$_SESSION[isi] = $_REQUEST[txa_isi];
			$_SESSION[page_cari] = 1;
		}
		
		//echo_pre($_SESSION);
		if($_SESSION[pasal] && !$_SESSION[surat]){
			$_SESSION[pasal] = '';
			echo warning("Surat harus diisi juga");
		}

		if($_SESSION[no_ayat] && !$_SESSION[pasal]){
			$_SESSION[no_ayat] = '';
			echo warning("Pasal harus diisi juga");
		}
		
		if($_GET[page_cari]) $_SESSION[page_cari] = $_GET[page_cari];
		
		if(!$_SESSION[page_cari]) $_SESSION[page_cari] = 1;
			
		$this->filter_session();
	}

	function frm_cari(){
		//echo_pre($_SESSION);
		$arr_kitab 	= array("Perjanjian Lama","Perjanjian Baru");

		$arr_surat 	= get_column("SELECT DISTINCT surat FROM bible ORDER BY id_bible ");
		
		$arr_ayat = array('002:079','005:017','005:072','005:073');
		shuffle($arr_ayat);
		
		$arr =  explode(":",$arr_ayat[0]);
		
		$row = get_row("SELECT 		
						name_arabic as sura_name_arabic
						, name_indonesia as sura_name_indonesia
						, qa.sura,qa.aya
						, qa.text as text_arabic
						, replace(qt.text,'<br>','') as text_terjemah
					FROM quran_arabic qa
						LEFT JOIN sura s ON qa.sura =s.`index`
						LEFT JOIN quran_{$_SESSION[terjemah]} qt ON qa.`index` =qt.`index`
					WHERE qa.sura='{$arr[0]}' AND qa.aya='{$arr[1]}' ");
		return "
	<!--	<script>
		var i = 0 ;
		jwplayer('player').setup({
			flashplayer : 'includes/jwplayer/player.swf',
			skin : 'includes/jwplayer/glow.zip',
			width:320,
			height:30,
			controlbar: 'bottom'
			});
		$(document).ready(function(){
			var path_sound = '".SOUND."/{$_SESSION[qari]}/{$arr[0]}{$arr[1]}.mp3';;
			jwplayer('player').load({ file: path_sound});
			".(!($_SESSION[kitab]||$_SESSION[bab]||$_SESSION[pasal]||$_SESSION[page_cari]>1||$_SESSION[isi])?
			"jwplayer('player').play();":"")."
			
		});
		</script>
		<div style='text-align:center;direction:rtl;font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]}'>
			<span > ".repair_ar($row[text_arabic]) ." </span>
			<span class='kurung'>" . l2a(')') ."</span>".
				$row[sura_name_arabic] . ":" . l2a( $row[aya] )  
			."<span class='kurung'>" . l2a('(') ."</span>
		</div>
		<div class='terjemah' style='text-align:center;font-size:{$_SESSION[font_size_terjemah]}' >
			{$row[text_terjemah]} <br>
			<span class='kurung'>" . l2a('(') ."</span>
				{$row[sura_name_indonesia]}:{$row[aya]}
			<span class='kurung'>" . l2a(')') ."</span><br>

		</div>
-->
		<form method='post' name='frm_cari' action='?mod=bible.pencarian.show&cari=1'>
		<table align='center' class='frm_cari' width='600px' border='0'>
			<tr>
				<td width='120px'>Kitab</td> 
				<td width='10px'>:</td> 
				<td>
					".combobox($_SESSION[kitab]," onchange='frm_cari.submit()' style='width:200px' ","cmb_kitab",$arr_kitab)."
				</td>
			</tr>
			<tr>
				<td width='150px'>Surat</td> 
				<td width='10px'>:</td> 
				<td>
					".combobox($_SESSION[surat]," ","txt_surat",$arr_surat)."
				</td>
			</tr>
			<tr>
				<td>Pasal</td><td>:</td>
				<td><input name='txt_pasal' size='3' value='{$_SESSION[pasal]}'></td>
			</tr>
			<tr>
				<td>Ayat</td><td>:</td>
				<td><input name='txt_no_ayat' size='3'  value='{$_SESSION[no_ayat]}'></td>
			</tr>
			<tr>
				<td width='150px' valign='top'>Kata Kunci Isi</td> 
				<td width='10px' valign='top'>:</td> 
				<td>
					<textarea name='txa_isi' style='width:85%;height:80px;float:left;'>{$_SESSION[isi]}</textarea>
					<input type='submit' value='OK' style='width:15%;height:80px;float:left'>
				</td>
			</tr>
		</table>
		</form>
		";
	}

	function show(){
		exec_sql("UPDATE bible SET surat=TRIM(surat)");
		$this->manage_session();
		$content .= $this->frm_cari();

		$isi_per_page=5;
		
		echo "<span style='visibility:hidden' id='next_page'>".($_SESSION[page_cari]+1)."</span>";
		
		$start_index  = ( $_SESSION[page_cari] -1 ) * $isi_per_page;
		
		if($_SESSION[kitab] || $_SESSION[surat] || $_SESSION[isi] || $_SESSION[pasal] ){
			if($_SESSION[pasal]){
				$syarat_pasal = " AND pasal = '{$_SESSION[pasal]}' ";
				if($_SESSION[no_ayat]){
					$syarat_no_ayat = " AND no_ayat = '{$_SESSION[no_ayat]}' ";
				}
			}
			else{
				$syarat_pasal = "";
				$syarat_no_ayat = "";
			}
			$sql = "SELECT * FROM bible
				WHERE 
					nama_kitab like '%{$_SESSION[kitab]}%'
					".($_SESSION[surat]?" AND MATCH(surat) AGAINST('".addslashes($_SESSION[surat])."') ":"")
					.($_SESSION[isi]?" AND MATCH(isi) AGAINST('".addslashes($_SESSION[isi])."') ":"")
				  . $syarat_pasal . $syarat_no_ayat ."
				  ORDER BY
				  ".($_SESSION[surat]?" MATCH(surat) AGAINST('".addslashes($_SESSION[surat])."') DESC ":"")
					.($_SESSION[surat]&&$_SESSION[isi]?",":"")
					.($_SESSION[isi]?"  MATCH(isi) AGAINST('".addslashes($_SESSION[isi])."') DESC ":"")
					.($_SESSION[surat]||$_SESSION[isi]?",":"")
					." id_bible
				LIMIT $start_index, $isi_per_page";
			$count = exec_scalar("SELECT COUNT(*) FROM
				bible
			WHERE 
				nama_kitab like '%{$_SESSION[kitab]}%'
				".($_SESSION[surat]?" AND MATCH(surat) AGAINST('".addslashes($_SESSION[surat])."') ":"")
				.($_SESSION[isi]?" AND MATCH(isi) AGAINST('".addslashes($_SESSION[isi])."') ":"")
				. $syarat_pasal. $syarat_no_ayat );
		}
		else{
			$sql = "SELECT * FROM bible
				ORDER BY id_bible
				LIMIT $start_index, $isi_per_page";
			$count = exec_scalar("SELECT COUNT(*) FROM bible");
		}
		
		$table = get_table($sql);
		
		//echo_pre($table);
		
		//return;
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
		<style>
			.ayat_bible{
				text-decoration:none;
				color:black;
			}
			.ayat_bible:hover{
				background-color:#dedeef;
				color:blue;
			}
		</style>
		<table width='100%' border='0'>
			<tr>
				<td colspan='2' align='center'>
				$str_count
				</td>
			</tr>
		";
		
		foreach($table as $i => $row){
			$text_terjemah .= "
			<div >
				".($table[$i][nama_kitab]!=$table[$i-1][nama_kitab]?
				"
				<div style='color:darkblue;font-weight:bold;font-size:120%;margin-top:20px;margin-bottom:5px'>
					 {$row[no_kitab]} {$row[nama_kitab]} </div>
				</div>
				 ":"")."
				 ".($table[$i][surat]!=$table[$i-1][surat]?
				"		 
				<div style='font-size:105%;color:darkgreen;margin-top:10px'><i>{$row[surat]} </i> </div>
				":"")."
			</div>
			<div style='color:black;margin-top:10px;padding-left:30px;'>
				<div style='color:brown'> ( Pasal : {$row[pasal]}  ||  Ayat : {$row[no_ayat]} ) </div>
				<a class='ayat_bible' href='?mod=bible.pencarian.show&cari=1&cmb_kitab={$row[kitab]}&txt_surat=".trim($row[surat])."&txt_pasal={$row[pasal]}&page_cari=".( roof($row[no_ayat]/$isi_per_page) )."'>
					&nbsp;	&nbsp;	&nbsp;	&nbsp; ".$this->tandai($row[isi])."
				</a>
			</div>
			";
		}
		
		$content .="
			<tr>
				<td class='terjemah' style='font-size:{$_SESSION[font_size_terjemah]}' >
					 $text_terjemah
				</td>
			</tr>
		</table>";

		include("mod/bible/pencarian.html");
	}
	
	function tandai($isi){
		$syarat_isi = addslashes(trim($_SESSION[isi]));
		
		// JIKA syarat_isi hanya satu kata
		if($syarat_isi==str_replace(" ","",$syarat_isi))
			$arr_syarat_isi[0] = $syarat_isi;
		else
			$arr_syarat_isi= explode(" ",$syarat_isi);
		
		if($_SESSION[bahasa]=='indonesia'){
			foreach($arr_syarat_isi as $key =>$value){
				if(!$value) continue;
				$kata_tunggal = $this->remove_prefix_sufix($value);
				if($kata_tunggal)
					$arr_kata_tunggal[] = $kata_tunggal;
			}
		}
		
		foreach($arr_syarat_isi as $key => $value){
			if(!$value) continue;
			$panjangDitemukan = "0";
			$ditemukan = "";
			
			if($cs){
				//$objResponse->alert($value);
				$pos = strpos(strtolower($isi),strtolower($value));
				if($pos>-1 && strpos($isi,$value) === false )
					$ok = false;

				$posDitemukan = strpos($isi,$value);
			}
			else{
				$posDitemukan = strpos(strtolower($isi),strtolower($value));
			}

			$kata_tunggal = $this->remove_prefix_sufix($value);
			if(strpos($isi,$value)>-1){
				$arrPrinted[] = $kata_tunggal;
			}
			
			
			$panjangDitemukan = strlen($value);
			if($posDitemukan>-1){
				$ditemukan = substr($isi,$posDitemukan,$panjangDitemukan);
				$isi = str_replace($ditemukan,"<span style='color:DarkRed;font-weight:bold' >$ditemukan</span>",$isi);	
			}
		}
			
		if($arr_kata_tunggal&&count($arr_kata_tunggal)>0)
			foreach($arr_kata_tunggal as $key =>$value){
				if(!value) continue;
				$posDitemukan = strpos(strtolower($isi),strtolower($value));
				$panjangDitemukan = strlen($value);
				if(is_array($arrPrinted))
					$isPrinted=in_array($value,$arrPrinted);
				else
					$isPrinted = false;
				
				
		//		echo "$value $posDitemukan $isPrinted :"; 
				if($posDitemukan>-1&&!$isPrinted){
					$ditemukan = substr($isi,$posDitemukan,$panjangDitemukan);
					$isi = str_replace($ditemukan,"<span style='color:DarkRed;font-weight:bold' >$ditemukan</span>",$isi);				
				}
			}
		
		return $isi;
		
	}
	
	function mengandung($kata1,$kata2){
		if(strpos($kata1,$kata2 )>-1)
			return 1;
		else
			return 0; 
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
	
	function filter_session(){
		$_SESSION[surat_isi] = str_replace("\\","", $_SESSION[surat_isi]);
		$_SESSION[isi] = str_replace("\\","", $_SESSION[isi]);
	}
	
	
}

?>
