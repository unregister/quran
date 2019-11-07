<?php
class pengaturan{

	function show(){
		// AMBIL $arr_qari dari index.php;
		global $arr_qari;
		//echo_pre($_SESSION);
		$url = $_SERVER[REQUEST_URI];
		echo"
			<style>
				#pengaturan_con * {
					font-size:12px;font-family:verdana
				}
			</style>
			<script type='text/javascript' >
				$(document).ready(function(){
					hide_pengaturan();
					}
				);
				
				function hide_pengaturan(){
						$('#pengaturan_con').hide();
				}
				function show_pengaturan(){
						$('#pengaturan_con').show();
				}
			</script>
			<div id='pengaturan_con' style='position:absolute;top:140px;left:50px;background-color:#FEEFEE;border:solid 1px brown;'>
				<div style='color:white;background-color:brown;text-align:center;padding:3px'  >
					PENGATURAN
				</div>
			<form method='post' action='?mod=utility.pengaturan.save'>
				<input type='hidden' value='$url' name='url'>
			";
		//-------- BACA FOLDER QARI' --------------
		
		$arr_font_size_terjemah 	= array(10,11,12,13,14,15,16,17,18,19);
		$arr_font_size_arab			= array(12,14,16,18,20,22,24,26,28,30,32,34,36,38,40);
		$arr_font_family_arab			= array("PDMS_IslamicFont","Traditional Arabic","Arial","Verdana");
		//echo $_SESSION[font_size_arab];
		echo "
				<table border='0'>
					<tr>
						<td rowspan='3'>
							<img src='images/pengaturan.png' >
						</td>
					</tr>
					<tr>
						<td > Font Arab</td>
						<td>:</td>
						<td width='200px'>".combobox($_SESSION[font_size_arab]," ","cmb_font_size_arab",$arr_font_size_arab)
							."px &nbsp;<!--".combobox($_SESSION[font_family_arab]," ","cmb_font_family_arab",$arr_font_family_arab)." -->
						</td>
						<td>Terjemah</td>
						<td>:</td>
						<td>
							".combobox($_SESSION[font_size_terjemah]," ","cmb_font_size_terjemah",$arr_font_size_terjemah) ."
						</td>

					</tr>
					<tr>
						<td>
							Qari' 
						</td>
						<td>:</td>
						<td>
							".combobox($_SESSION[qari]," ","cmb_qari",$arr_qari)."
						</td>
						<td>
							Otomatis Lanjut
						</td>
						<td>:</td>
						<td>
							".combobox($_SESSION[auto_resume]," ","cmb_auto_resume",array(1,0) ,array('Ya','Tidak') ) ."
						</td>
						<td>
							<input type='submit' value='OK'>
							<input type='button' value='Batal' onclick='hide_pengaturan()'>
						</td>
					</tr>
				</table>
			</form>
			</div>";
	}

	function save(){
		$_SESSION[font_size_terjemah] = $_POST[cmb_font_size_terjemah];
		$_SESSION[font_size_arab] = $_POST[cmb_font_size_arab];
		$_SESSION[font_family_arab] = 'PDMS_IslamicFont'; //$_POST[cmb_font_family_arab];
		$_SESSION[qari]   = $_POST[cmb_qari];
		$_SESSION[auto_resume]   = $_POST[cmb_auto_resume];
		echo "<script> window.location = '{$_POST[url]}' </script> ";
	}
	
}

?>
