<?php
class ringkasan{

	function ubah($lama, $baru){
		exec_sql("UPDATE hadits.had_muslim 
					SET 
						Isi_Indonesia=REPLACE(Isi_Indonesia,'".addslashes($lama)."','".addslashes($baru)."')
				");
	}
	
	function ringkas(){

		$this->ubah('Shallallahu \'Alaihi Wasalam','saw.');
		$this->ubah('shallallahu \'alaihi wasallam','saw.');
		$this->ubah('merupakan','adalah');
		$this->ubah('Dan ia adalah atsar yang masyhur','Dan merupakan jejak(cerita) yang terkenal');
		
		$_GET[NoHdt] = 1;
		$this->form();
	}
	
	function form(){
		$row = get_row("SELECT * FROM hadits.had_muslim WHERE NoHdt={$_GET[NoHdt]}");
		echo "
		<link rel='stylesheet' type='text/css' href='themes/simple/css/page_border.css'>
		<link rel='stylesheet' type='text/css' href='themes/simple/css/main.css'>
		<link rel='stylesheet' type='text/css' href='themes/simple/css/menu.css'>
		<script src='includes/jquery-ui/js/jquery-1.4.4.min.js'></script>
		<script src='includes/jwplayer/jwplayer.js'></script>
		<script src='js/terjemah_per_kata.js'></script>

		<div class='content'>
			<form method='post' action='?mod=hadits.ringkasan.simpan&NoHdt={$_GET[NoHdt]}'>
			<input type='submit' value='simpan'><br>
			
			<textarea 
				 name='Isi_Indonesia'
				 style='line-height:200%;width:100%;height:250px;' >{$row[Isi_Indonesia]}</textarea>
			
			<br>
			<br>
			
			<textarea 
				 name='Isi_Arab'
				 style='direction:rtl;font-size:30;font-family:{$_SESSION[font_family_arab]};width:100%;height:250px'>{$row[Isi_Arab]}</textarea> 
			<br>
			</form>			
		</div>
		";
	
	}
	
	function simpan(){
		if($_POST[Isi_Indonesia]){
			$nr[Isi_Indonesia]	= $_POST[Isi_Indonesia];
			$nr[Isi_Arab]		= $_POST[Isi_Arab];
			update('hadits.had_muslim',$nr, "NoHdt='{$_GET[NoHdt]}' " );
			$this->form();
		}
		else{
			$this->form();
		}
		
	}
	

}
?>