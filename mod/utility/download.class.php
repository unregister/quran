<?php
class download {
	function show(){
//		echo 'MAAF LAGI DIUPDATE, TUNGGU YA...';
//		return;
		echo '
<pre>
<b>
I. LINK DOWNLOAD </b> 
<a target="_blank" href="download/xampp_small.rar">- Versi Small ukuran 124,9 MB (Fitur lengkap namun murotal sampai al baqarah) </a>
	
<b style="font-size:12px">
II. PETUNJUK INSTALLASI </b> 

1. Install <b>firefox</b> versi baru agar tampilan huruf arab sempurna.
2. Install <b>flash player</b> agar firefox bisa menjalankan mp3

3. Copy <b>xampp_small.rar </b> ke D:\
4. Install <b>winrar </b> jika di komputer anda belum ada
5. Klik kanan <b>xampp_small.rar</b>, pilih <b>extract here</b>
6. Jika anda ingin menambah murotal, tersedia dua qari\':
	- <a target="_blank" href="download/musyari.tar">Musyari Rasyid Al afasy</a>
	- <a target="_blank" href="download/ghamadi.tar">Ghamadi</a>
	extract file tersebut ke folder <b>D:\xampp\htdocs\sound</b>

<b style="font-size:12px">
III. PETUNJUK MENJALANKAN </b> 
1. Buka folder xampp hasil extract, jalankan <b>START QURAN.bat</b>
2. Jika ada peringatan dari firewall pilih "UNBLOCK", biasanya muncul 2 kali
3. Perhatikan tulisan yang keluar di command prompt
   - Jika ada tulisan "xampp is not started", berarti komputer anda terinfeksi virus yang merusak EXE
   - jika ada tulisan "xampp is started", tetapi program tidak jalan berarti firewall memblok program quran
   Matikan firewall lewat control panel

</pre>
		';
	}
	
}
?>