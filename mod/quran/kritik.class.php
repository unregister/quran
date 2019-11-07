<?php
class kritik{
	function show(){
		echo '
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) {return;}
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/id_ID/all.js#xfbml=1&appId=112871238821264";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, "script", "facebook-jssdk"));</script>
		
		<center >
				<b style="font-family:verdana;color:#666666;line-height:300%">Kirimkan saran, kritik, atau pertanyaan anda disini</b>
		</center>
		<table align="center">
		<tr><td align="center">
		<div class="fb-comments" data-href="quranterjemah.com" data-num-posts="20" data-width="900"></div>
		</td>
		</tr>
		</table>
		';
		return;
		
		
		echo "
		<center style='font-family:verdana;font-size:12px'>
		<b>SARAN & KRITIK</b>
		<br><br>
		</center>
		
		<form method='post' action='?mod=quran.kritik.kirim'> 
			<table border='0' style='font-size:12px;font-family:verdana' cellpadding='0' cellspacing='0' align='center'>
				<tr>
					<td width='100px'>Email anda </td><td> : <input name='txt_email' size='25'> * untuk membalas </td>
				</tr>
				<tr>
					<td>Jenis</td>
					<td> : ".combobox("Saran","","cmb_jenis",array("Kesalahan pengetikan","Error/Bug","Saran") )."  </td>
				</tr>
				<tr>
					<td  valign='top'>Pesan</td>
					<td> : 
						<textarea name='txa_isi' style='vertical-align:top' cols='50' rows='10' > </textarea>
					</td>
				</tr>
				<tr>
					<td>
						Jawab
					</td>
					<td valign='top'>
						<img src='includes/captcha/captcha.php' align='left'>
						<input name='txt_jawaban' size='3' style='font-size:35'>
						<input type='submit' style='font-size:35' value='OK'>
					</td>
				</tr>
			</table>
			
		</form>
		
		";
	}
	
	function kirim(){
		//echo_pre($_SESSION);
		//echo_pre($_POST);
		if($_SESSION[captcha]==$_POST[txt_jawaban].".gif"){
			if(trim($_POST[cmb_jenis])&&trim($_POST[txa_isi])){
				$tujuan = "haekal.ahmad@gmail.com";
				mail($tujuan, $_POST[cmb_jenis], "dari: {$_POST[txt_email]} \n ". $_POST[txa_isi] ,"From: kritik@quranterjemah.com ", "-f kritik@quranterjemah.com ");
				
				echo "
				<script>
					alert('Terima kasih atas saran / kritiknya');
					window.location ='index.php';
				</script>
				
				";
			}
			else 
				$this->show();

		}
		else 
			$this->show();
	}
}
?>