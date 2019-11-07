var status_play = 1;
function balik_status(){
	if(status_play){
		status_play  = 0;
		$('#balik_status').val('Play hafalan');
	}
	else{
		status_play  = 1;
		$('#balik_status').val('Pause hafalan');
	}
		
}

function balik_halaman(){
	if(!status_play){
		setTimeout("balik_halaman()",500);
		return;
	}

	window.location = "?mod=quran.hafalan.show&autostart=1&page=" + document.getElementById('next_page').innerHTML;
}

$(document).ready( function (){
	jwplayer("player").setup({
		flashplayer : "includes/jwplayer/player.swf",
		skin : "includes/jwplayer/glow.zip",
		width:320,
		height:30,
		controlbar: "bottom",
		events: {
			onComplete:function(){
				var ulangi_aya ;
				var ulangi_semua ;
				// jika masih harus ulang aya
				if(sudah_ulang_aya < ulang_aya){
					sudah_ulang_aya++;
					ulangi_aya = 1;
					play();
				}
				else{
					// jika aya sudah mencapai akhir ulangi dari awal lagi
					if(j >= total_aya - 1){
						if(sudah_ulang_semua < ulang_semua){
							j = 0;
							sudah_ulang_semua++;
							sudah_ulang_aya=1;
							ulangi_semua = 1;
							play();
						}
					}
					// jika belum sampai ayat terakhir lanjut ke ayat berikut
					else{
						j++;
						sudah_ulang_aya=1;
						play();
					}
				}
				
				//alert('aya=' + j + '  sudah_ulang_aya =' + sudah_ulang_aya + '  sudah_ulang_semua =' + sudah_ulang_semua);
				//alert('ulangi_aya=' + ulangi_aya + '  ulangi_semua =' + ulangi_semua );
				
			}
		}
	});

	jwplayer("player").load({ file:arr_sound[j] });
	//jwplayer("player").load({ file:arr_sound[j] });
});

function play(){
	
	if(!status_play){
		setTimeout("play('" + j + "')",500);
		return;
	}
	
	var aya = document.getElementById("aya_"+j);
	var terjemah = document.getElementById("terjemah_"+j);
	
	if(aya){
		for(k=0;k<100;k++){
			var old_aya  = document.getElementById("aya_"+k);
			var old_terjemah  = document.getElementById("terjemah_"+k);

			if(old_aya){
				old_aya.className = "";
				if(old_terjemah) old_terjemah.className = "latin";
			}
			else{
				break;
			}
		}
		aya.className = "playing";
		if(terjemah) terjemah.className = "terjemah_playing";
		
		if(arr_sound[j]){
			jwplayer("player").load({ file:arr_sound[j] });
			jwplayer("player").play();
		}
		
	}
}

function ganti_terjemah(){
	var terjemah = document.getElementById('cmb_terjemah');
	if(terjemah){
		window.location = "?mod=quran.hafalan.show&ganti_terjemah=1&terjemah=" + terjemah.value;
	}
}

function ganti_sura(){
	var sura = document.getElementById('acp_sura');
	var aya_awal  = document.getElementById('txt_aya_awal');
	var aya_akhir  = document.getElementById('txt_aya_akhir');

	var ulang_aya  		= document.getElementById('txt_ulang_aya');
	var ulang_semua  	= document.getElementById('txt_ulang_semua');
	if(sura){
		var sura_no;
		for(var i = 1 ; i <=114; i++){
			if(arr_sura[i]==sura.value) sura_no = i;
		}
		window.location = "?mod=quran.hafalan.show&ganti_sura=1&sura_name=" + sura.value + "&aya_awal=" + aya_awal.value + "&aya_akhir=" + aya_akhir.value+ "&ulang_aya=" + ulang_aya.value+ "&ulang_semua=" + ulang_semua.value + "#kata_" + sura_no + "_" + aya_awal.value + "_1" ;
	}
}

$(document).ready(function(){

	$('#txt_aya').keypress( function(event){
		if (event.which == '13')
			ganti_sura();
	});
});
