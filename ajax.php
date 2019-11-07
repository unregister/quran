<?php
/*-----------------------
NAMA FILE : /index.php
PEMBUAT   : Haekal
*/
session_start();
		
include("config.php");

if(!$_SESSION[font_size_terjemah]) 	$_SESSION[font_size_terjemah] = 12;

if(!$_SESSION[font_size_arab]) 		$_SESSION[font_size_arab] = 26;

if(!$_SESSION[font_family_arab]) 	$_SESSION[font_family_arab] = "KFGQPC_Naskh";

if($_SESSION[auto_resume]=='') 	$_SESSION[auto_resume] = 1;

if(!$_SESSION[terjemah]) $_SESSION[terjemah] = 'indonesia';

$dir = SOUND;
$handle = opendir($dir);
while (false !== ($file = readdir($handle))) { 
	if($file!='.' && $file != '..' && is_dir("$dir/$file"))	$arr_qari[] = $file;
}

sort($arr_qari);

if(!in_array($_SESSION[qari],$arr_qari) ) $_SESSION[qari] = $arr_qari[0];

if($_GET[fb_comment_id])
	$_GET[mod] = "quran.kritik.show";

if(!$_GET[mod]){
	$_GET[mod] = DEFAULT_MOD;
}
$arr_mod = explode(".",$_GET[mod]);

$mod =  $arr_mod[0];
$class_name =  $arr_mod[1];
$function_name =  $arr_mod[2]?$arr_mod[2]:$default_function;

//-------- CREATING CONTENT------------------
$file = "mod/$mod/index.php";
if(!file_exists($file)){
	$file = "mod/$mod/$class_name.class.php";
}

//echo $file;
if(file_exists($file)){
	include_once($file);
	$class = new $class_name();
	ob_start();
	$class->$function_name();
	$contents = ob_get_contents();
	ob_clean();
}
else
	$content=  "<div class='warning'>Modul {$_GET[mod]} tidak ditemukan</div>";
//-------------------------------------------
 
/*//------------- CREATING LOGGING-------------
if(!($_GET[mod]=='utility'&&$_GET['class']=='log')){
	ob_start();
	print_r($_SESSION);
	$session = ob_get_contents();
	ob_clean();
	
	ob_start();
	print_r($_REQUEST);
	$request =  ob_get_contents(); 
	ob_clean();
//	exec_sql("INSERT INTO log VALUES(NULL,'{$_SERVER[QUERY_STRING]}','{$_SERVER[REMOTE_ADDR]}','{$_SESSION[grup]}','{$_SESSION[username]}',now(),'$session','$request')");
}
//-------------------------------------------
*/
echo $contents;
?>
			
