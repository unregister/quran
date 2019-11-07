<?php
$themes = 'simple';
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_password ='';

$mysql_database = 'quran';

DEFINE("SOUND","sound");
DEFINE("DEFAULT_MOD","quran.beranda.show");
DEFINE("UN","haekal");
DEFINE("PW","vita");

$link = mysql_connect($mysql_host,$mysql_user,$mysql_password);
mysql_set_charset("utf8",$link);
mysql_select_db($mysql_database); 

include("includes/fungsi.php");


?>