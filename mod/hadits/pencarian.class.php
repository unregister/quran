<?php
class pencarian{
	var $spasi = array(" ","  ","   ","    ","    ","    ");
	var $illegal_char = array("_","/" , "" ,".",">","<",")","(","QS","qs","terjemah","tafsir","babt","babh","babt","babh");


	function manage_session(){
		if($_REQUEST[cari]){
			$_SESSION[kitab] = $_REQUEST[cmb_kitab];
			$_SESSION[bab] = $_REQUEST[txt_bab];
			$_SESSION[isi] = $_REQUEST[txa_isi];
			$_SESSION[page_cari] = 1;
		}
		
		if($_GET[page_cari]) $_SESSION[page_cari] = $_GET[page_cari];
		
		if(!$_SESSION[page_cari]) $_SESSION[page_cari] = 1;
			
		$this->filter_session();
	}

	function frm_cari(){
		
		$arr_title_kitab 	= get_column("SELECT DISTINCT CONCAT(no_kitab,'. ',nama_kitab) FROM hr_muslim ORDER BY no_kitab ");
		$arr_value_kitab 	= get_column("SELECT DISTINCT nama_kitab FROM hr_muslim ORDER BY no_kitab ");

		$arr_bab 	= get_column("SELECT DISTINCT bab FROM hr_muslim ORDER BY no_kitab,  bab ");
		
		return "
		<form method='post' name='frm_cari' action='?mod=hadits.pencarian.show&cari=1'>
		<table align='center' class='frm_cari' width='600px' border='0'>
			<tr>
				<td width='120px'>Kitab</td> 
				<td width='10px'>:</td> 
				<td>
					".combobox($_SESSION[kitab]," onchange='frm_cari.submit()' style='width:400px' ","cmb_kitab",$arr_value_kitab,$arr_title_kitab)."
				</td>
			</tr>
			<tr>
				<td width='150px'>Bab / Tema</td> 
				<td width='10px'>:</td> 
				<td>
					".autocomplete($_SESSION[bab]," size='60' ","txt_bab",$arr_bab)."
				</td>
			</tr>
			<tr>
				<td width='150px' valign='top'>Kata Kunci Isi</td> 
				<td width='10px' valign='top'>:</td> 
				<td>
					<textarea name='txa_isi' style='width:85%;height:80px;float:left;"
						.($_SESSION[bahasa]=='arabic_simple'?"direction:rtl;font-size:{$_SESSION[font_size_arab]};font-family:{$_SESSION[font_family_arab]}":'')
						."'>{$_SESSION[isi]}</textarea>
					<input type='submit' value='OK' style='width:15%;height:80px;float:left'>
				</td>
			</tr>
		</table>
		</form>
		";
	}

	function show(){
		$this->manage_session();
		$content .= $this->frm_cari();

		$isi_per_page=5;
		
		echo "<span style='visibility:hidden' id='next_page'>".($_SESSION[page_cari]+1)."</span>";
		
		$start_index  = ( $_SESSION[page_cari] -1 ) * $isi_per_page;
		if($_SESSION[kitab] || $_SESSION[bab] || $_SESSION[isi]){
			$sql = "SELECT * FROM hr_muslim
				WHERE 
					nama_kitab like '%{$_SESSION[kitab]}%'
					".($_SESSION[bab]?" AND MATCH(bab) AGAINST('".addslashes($_SESSION[bab])."') ":"")
					.($_SESSION[isi]?" AND MATCH(isi) AGAINST('".addslashes($_SESSION[isi])."') ":"")
				  ."
				LIMIT $start_index, $isi_per_page";
			$count = exec_scalar("SELECT COUNT(*) FROM
				hr_muslim
			WHERE 
				nama_kitab like '%{$_SESSION[kitab]}%'
				".($_SESSION[bab]?" AND MATCH(bab) AGAINST('".addslashes($_SESSION[bab])."') ":"")
				.($_SESSION[isi]?" AND MATCH(isi) AGAINST('".addslashes($_SESSION[isi])."') ":"")
				);
		}
		else{
			$sql = "SELECT * FROM hr_muslim
				LIMIT $start_index, $isi_per_page";
			$count = exec_scalar("SELECT COUNT(*) FROM hr_muslim");
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
					 {$row[no_kitab]}. Kitab {$row[nama_kitab]} </div>
				</div>
				 ":"")."
				 ".($table[$i][bab]!=$table[$i-1][bab]?
				"		 
				<div style='font-size:105%;color:darkgreen;margin-top:10px'><i>Bab {$row[bab]} </i> </div>
				":"")."
			</div>
			<div style='color:black;margin-top:10px;padding-left:30px;'>
				<div style='color:brown'> ( HR.MUSLIM No:{$row[no_hr]} ) </div>
				&nbsp;	&nbsp;	&nbsp;	&nbsp; ".$this->tandai($row[isi])."
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

		include("mod/hadits/pencarian.html");
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
		$_SESSION[bab_isi] = str_replace("\\","", $_SESSION[bab_isi]);
		$_SESSION[isi] = str_replace("\\","", $_SESSION[isi]);
	}
	
	
}

?>
