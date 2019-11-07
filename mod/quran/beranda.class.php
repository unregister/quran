<?php
class beranda{
	function test(){
		echo "
		<div id='pesan'>
			<div style='position:fixed;top:0px;left:0px;width:100%;height:100%;background-color:#dbdbdb;opacity:0.7;'>
				&nbsp;
			</div>
			<table style='width:100%;height:100%;position:fixed;top:0px;left:0px;'>
				<tr>
					<td align='center'>
						<div style='border:solid 1px darkgreen;width:700px;
							color:white;background-color:darkgreen;font-weight:bold;font-family:verdana;line-height:180%'>
							Mari kita beramal jariyah
						</div>
						
						<div style='text-align:justify;border:solid 1px darkgreen;width:700px;height:200px;line-height:140%;
							overflow:scroll;background-color:white;'>
	<pre style='font-size:12px;font-family:verdana;'>
	<span style='color:darkblue'><i>...., Dan tolong-menolonglah kamu dalam (mengerjakan) kebajikan dan takwa, dan jangan 
	tolong-menolong dalam berbuat dosa dan pelanggaran. Dan bertakwalah kamu kepada Allah, 
	sesungguhnya Allah amat berat siksa-Nya. ( Al Maidah : 2 )</i> </span>
	
    Software Al quran terjemah ini bersifat open source dan gratis, seluruhnya hasil 
    kerjasama umat islam. Sehingga anda bebas menggunakan, mengopy, dan menyebarluaskan.
    
	Alangkah indahnya jika kita menambah amal perbuatan kita dengan membantu saudara -
    saudara kita yang fakir, miskin, dan yatim. Atas dasar inilah kami berinisiatif untuk 
    membuka donasi bagi pengguna quranterjemah.com yang ingin membantu saudara kita.
	
    <b style='color:brown'> Untuk tahap awal Insya Allah dana yang terkumpul akan digunakan untuk mengadakan 
    TPQ gratis untuk saudara kita yang ingin belajar Al quran namun kurang mampu. Donasi 
    digunakan untuk membeli buku IQRO` , Al Quran, dan menggaji guru. </b>
	
	Silahkan salurkan dana anda ke :
	1. BANK BCA
	   No Rek    :  4090354898
	   Atas Nama :  Ahmad Soleh Haikal
	2. BANK BNI
	   No Rek    :  0201426350
	   Atas Nama : Ahmad Soleh Haekal

	 <a href='download/laporan keuangan quranterjemah.xls' >Download Laporan Keuangan Donasi</a></pre>
	<center>
	<input type='button' onclick='$(\"#pesan\").hide()' value='OK'>
	</center>
						</div>
					
					</td>
				</tr>
			</table>
	</div>
		";
	}
	
	function show(){
		$content .= "
				
				
				<center>
				<h3 style='color:darkblue;line-height:200%'>
					WWW.QURANTERJEMAH.COM<br>
					AL QURAN TERJEMAH PER KATA ONLINE TERCANGGIH
					
				</h3>
				</center>
				<b style='color:brown' > ATURAN PENGGUNAAN :</b> <br>
					- Quranterjemah.com adalah proyek open source. 
					Semua turunan dan / atau hasil kutipan situs ini <b>HARUS BEBAS DICOPY, DAN DISEBARLUASKAN </b> kepada siapapun tanpa batasan apapun. <br>
				<b style='color:brown' > FITUR - FITUR :</b> <br>
				<b> 1. PENCARIAN TERCANGGIH MULTI-BAHASA. </b><br>
					- Dapat menangani pencarian kalimat dalam bahasa Indonesia, Inggris, dan Arab. <br>
					- Kata kunci dapat berupa kata atau kalimat. <br>
					- Menggunakan metode perangkingan seperti google sehingga akan mencari ayat yang paling mendekati
					jika kata kunci yang sama persis tidak ditemukan <br>
					- Kata kunci surat sangat fleksibel, <br>
					<table style='font-size:13'>
					<tr>
						<td rowspan='4' valign='top'>misalnya : </td>
						<td>Al baqarah ayat 1-5</td>
					</tr>
					<tr>
						<td>   Yusuf ayat 2 dan 3</td>
					</tr>
					<tr>
						<td>   2:1-5 </td>
					</tr>
					<tr>
						<td>   2:1,2,3,4 </td>
					</tr>
					</table>
				<b> 2. MUROTAL TERCANGGIH MULTI-BAHASA. </b><br>
					- Terdapat fitur tulisan arab latin untuk membantu orang yang sedang belajar membaca alquran.<br>
					- Ukuran huruf arab dan terjemah dapat disesuaikan untuk kenyamanan saat membaca.<br>
					- Suara murotal Qari' dapat dipilih.<br>
					- Murotal dapat diatur agar otomatis lanjut ke ayat selanjutnya atau tidak. Sangat membantu 
					bagi yang sedang menghafal quran.<br>
				<b> 3. FITUR TERJEMAH PER KATA. </b><br>
					Ketika anda mendekatkan cursor ke sebuah ayat, maka akan muncul <b>terjemah per kata</b> dari ayat tersebut. <br>
				<b> 4. SUMBER TEKS ARAB, TERJEMAH, DAN MUROTAL. </b><br>
					Kami memakai database dan murotal yang dibuat oleh http://tanzil.net/wiki/Resources. Sama seperti yang dipakai aplikasi 
					murotal ZEKR yang sudah lama kita kenal baik di Windows, dan Linux. <br>
				<b> 5. SUMBER RINGKASAN TERJEMAH SHAHIH MUSLIM. </b><br>
					Teks ringkasan terjemah shahih muslim ini berasal dari software hadits web yang diolah ke dalam bentuk database 
					untuk memudahkan pencarian.<br>
					Link Sumber : <a href='http://opi.110mb.com/haditsweb/index.htm' target='_blank'> Hadits Web </a> 
					
		";
		include("mod/quran/standar.html");
	}
}
?>