<?php
class login{

	function show(){
		echo "
		<form method='post' action='?mod=utility.login.cek' >
			<table style='border:solid 1px brown;font-family:verdana;font-size:11px;line-height:180%;padding:10px' align='center'>
				<tr>
					<td>Username </td>
					<td>:</td>
					<td><input name='txt_username' ></td>
				</tr>
				<tr>
					<td>Password </td>
					<td>:</td>
					<td><input type='password' name='txt_password' ></td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='submit' value='login'>
					</td>
				</tr>
				
			</table>
			
		</form>
		";
	}
	
	function cek(){
		if($_POST[txt_username]==UN && $_POST[txt_password]==PW){
			$_SESSION[grup]  	= 1;
			$_SESSION[username] = UN;
			echo "
			<script>
				function alihkan(){
					window.location = '?mod=quran.murotal.show';
				}
				setTimeout('alihkan()',1000);
			</script>
			";
			echo warning("Anda berhasil login, Halaman akan segera dialihkan,..");
		}
		else{
			echo "
			<script>
				alert('Gagal login');
			</script>
			";
			$this->show();
		}
	}
	
	function logout(){
		$_SESSION[grup] = 0;
		$_SESSION[username] = '';
			echo "
			<script>
				function alihkan(){
					window.location = '?mod=quran.murotal.show';
				}
				setTimeout('alihkan()',1000);
			</script>
			";
		echo warning("Anda telah logout, Halaman akan segera dialihkan,..");
		
	}
}

?>