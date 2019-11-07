<?php
$sql = "SELECT * FROM symbol";
$table = get_table($sql);
foreach($table as $i => $row){
	$latin = $row[latin];
	$arab  = $row[arab]; 
	$arr_konversi[$latin] = $arab;
}
DEFINE(ALIF1, $arr_konversi[alif1]);
DEFINE(ALIF2, $arr_konversi[alif2]);

DEFINE(TASJID1, $arr_konversi[tasjid1]);
DEFINE(TASJID2, $arr_konversi[tasjid2]);

DEFINE(KASROH_TANWIN1, $arr_konversi[kasroh_tanwin1]);
DEFINE(KASROH_TANWIN2, $arr_konversi[kasroh_tanwin2]);

DEFINE(KATA_ALLAH1, $arr_konversi[kata_Allah1]);
DEFINE(KATA_ALLAH2, $arr_konversi[kata_Allah2]);

DEFINE(LILLAHI1, $arr_konversi[lillahi1]);
DEFINE(LILLAHI2, $arr_konversi[lillahi2]);

DEFINE(AA1, $arr_konversi[aa1]);
DEFINE(AA2, $arr_konversi[aa2]);

DEFINE(SAHABATKU1, $arr_konversi[sahabatku1]);
DEFINE(SAHABATKU2, $arr_konversi[sahabatku2]);

DEFINE(YA1, $arr_konversi[ya1]);
DEFINE(YA2, $arr_konversi[ya2]);

DEFINE(YA_PANJANG1, $arr_konversi[ya_panjang1]);
DEFINE(YA_PANJANG2, $arr_konversi[ya_panjang2]);

DEFINE(AL_A1, $arr_konversi[al_a1]);
DEFINE(AL_A2, $arr_konversi[al_a2]);

function repair_ar($aya){
	$aya = str_replace("
"," ",$aya);
	$aya = str_replace(ALIF1,ALIF2,$aya);
	$aya = str_replace(SAHABATKU1,SAHABATKU2,$aya);
	$aya = str_replace(TASJID1,TASJID2,$aya);
	$aya = str_replace(KASROH_TANWIN1,KASROH_TANWIN2,$aya);
	$aya = str_replace(KATA_ALLAH1,KATA_ALLAH2,$aya);
	$aya = str_replace(LILLAHI1,LILLAHI2,$aya);
	$aya = str_replace(AA1,AA2,$aya);
	$aya = str_replace(YA1,YA2,$aya);
	$aya = str_replace(YA2." ",YA1." ",$aya);
	$aya = str_replace(YA_PANJANG1." ",YA_PANJANG2." ",$aya);

	$aya = str_replace(AL_A1,AL_A2,$aya);

	return $aya;
}

function l2a($number){
	global $arr_konversi;
	$number = "$number";
	for($i=0;$i<=strlen($number);$i++){
		$latin = $number[$i];
		//echo $latin;
		$hasil .= $arr_konversi[$latin];
		//echo $arr_konversi[$latin];
	}
	return $hasil;
}
function bismillah(){
	return exec_scalar("SELECT text FROM quran_arabic WHERE sura=1 AND aya=1");
}

function allow_edit($modul=''){
	if($modul=='anggota'){
		if($_SESSION[grup]=='1'||$_SESSION[grup]=='2'||$_SESSION[grup]=='3'||$_SESSION[grup]=='4')
			return true;
		else
			return false;
	}
	if($modul=='koleksi'||$modul=='laporan'){
		if($_SESSION[grup]=='1'||$_SESSION[grup]=='2'||$_SESSION[grup]=='3')
			return true;
		else
			return false;
	}
	elseif($modul=='peminjaman'||$modul=='pengembalian'){
		if($_SESSION[grup]=='1'||$_SESSION[grup]=='2'||$_SESSION[grup]=='4')
			return true;
		else
			return false;
	}
	elseif($modul=='kunjungan'){
		if($_SESSION[grup]=='5')
			return true;
		else
			return false;
	}
	elseif($modul=='pengaturan'){
		if($_SESSION['grup']=="1"||$_SESSION['grup']=="2"||$_SESSION['grup']=="3")
			return true;
		else
			return false;
	}
	else{
		if($_SESSION['grup']=="1"||$_SESSION['grup']=="2")
			return true;
		else
			return false;
	}
}

function del_from_arr(&$arr, $del){
	foreach($arr as $i => $val){
		if($val!=$del) $arr_new[$i] = $val;
	}
	$arr =  $arr_new;
}

function selected($get){
	if(strpos($_GET[mod],$get)>-1)
		echo ' id="selected" '; 
}

function edit(){
	return "<iframe src='includes/phpimageeditor/?imagesrc=../../{$_SESSION[path_image]}' 
						style='border:none;width:100%;height:500px;'>
		</iframe>";
}
function capture(){
	return "<iframe src='includes/croflash/croflash.swf' 
						style='float:left;border:none;width:600px;height:500px;margin: -20px 100px 30px 100px'>
		</iframe>";
}

function warning($str){
	return "<div class='warning' id='warning'>$str</div>";
}

function get_date($format){
	return exec_scalar("SELECT DATE_FORMAT(now(),'$format')");
}
function set_len($str,$len,$rata,$char=" "){
	$panjang = strlen($str);
	if($len<strlen($str)){
		return substr($str,0,$len);
	}
	elseif($rata=='kanan'){
		for($i=0;$i<$len-$panjang;$i++){
			$str = $char.$str;
		}
	}
	elseif($rata=='kiri'){
		for($i=0;$i<$len-$panjang;$i++){
			$str = $str.$char;
		}
	}
	return $str;
}
function ttk($number){
	$hasil = number_format($number, 2, '.', ',');
	$hasil = str_replace('.00','',$hasil);
	return $hasil;
}

function split_tanggal($tanggal){
	$arr = explode('/',$tanggal);
	for($i=0;$i<3;$i++){
		if($arr[$i]=='')$arr[$i] = '%'; 
	}
	return $arr;
}

function split_jam($jam){
	$arr = explode(':',$jam);
	for($i=0;$i<3;$i++){
		if($arr[$i]=='')$arr[$i] = '%'; 
	}
	return $arr;
}

function str_to_time($str){
	$arr = explode(' ',$str);
	
	$tanggal = $arr[0];
	$jam = $arr[1];
	
	$arr_tanggal = explode('/',$tanggal);
	
	return "{$arr_tanggal[2]}-{$arr_tanggal[1]}-{$arr_tanggal[0]} $jam";
}

//FORMAT HARUS tanggal/bulan/tahun
function str_to_date($str){
	$arr = explode('/',$str);
	return "{$arr[2]}-{$arr[1]}-{$arr[0]}";
}

function date_to_str($str){
	$arr = explode('-',$str);
	return "{$arr[2]}/{$arr[1]}/" . substr($arr[0],-2);
}

function echo_pre($str){
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}

function set_length($str,$n){
	$len = strlen($str);
	for($i=1;$i<$n-len;$i++){
		$str = "0$str";
	}
	
	return $str;
}

function roof($double){
	$int = floor($double);
	if($int<$double)
		$int +=1;
	
	return $int;
}

function upper_first_case($str){
	$arr_str = explode(' ',$str);
	foreach($arr_str as $i => $value){
		if($value!=' '){
			$hasil .= ' '. strtoupper( $value[0] ) . strtolower( substr($value,1) ); 
		}
	}
	return $hasil;
}

function current_url(){
	return $_SERVER[PHP_SELF] . "?" . $_SERVER[QUERY_STRING];
}
function get_ext($file_name){
	$arr = explode('.',$file_name);
	return $arr[count($arr) -1];
}

function merahi($str,$syarat){
	
	if($syarat==""){
		return $str;
	}
	else{
		$arr_syarat= explode(" ",$syarat);
		foreach($arr_syarat as $key =>$value){
			$str = str_ireplace($value,"<b>$value</b>",$str);
		}
	}
	return $str;
	
}
?>
