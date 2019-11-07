<?php
function get_captcha(){
	$dir = "./images";
	$handle = opendir($dir);
	while (false !== ($file = readdir($handle))) { 
		if($file!='.'&&$file!='..'&&get_ext($file)=='gif'){
			$arr_file[] =  $file;
		}
	}
	
	shuffle($arr_file);
	//echo $_SESSION[captcha]."<br>";
	while($_SESSION[captcha]==$arr_file[0]){
		shuffle($arr_file);
	}
	
	$imgname =  $arr_file[0];
	$_SESSION[captcha] = $arr_file[0];
	
	
	$im = @imagecreatefromgif("images/$imgname");
	//echo $imgname;
	header("Content-type:image/gif");
	imagegif($im);
}
session_start();
include("../utility.php");
get_captcha();
?>