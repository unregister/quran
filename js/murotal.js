var j = 0 ;
var status_play = 1;
function balik_status(){
	if(status_play){
		status_play  = 0;
		$('#balik_status').val('Play Murotal');
	}
	else{
		status_play  = 1;
		$('#balik_status').val('Pause Murotal');
	}
		
}

function balik_halaman(){
	if(!status_play){
		setTimeout("balik_halaman()",500);
		return;
	}

	window.location = "?mod=quran.murotal.show&autostart=1&page=" + document.getElementById('next_page').innerHTML;
}

jwplayer("player").setup({
	flashplayer : "includes/jwplayer/player.swf",
	skin : "includes/jwplayer/glow.zip",
	width:320,
	height:30,
	controlbar: "bottom",
	events: {
		onComplete:function(){
				if(auto_resume) {
					j++;
					if(arr_sound[j])
						play();
					else{
						balik_halaman();
					}
				}
			}
		}
	});
jwplayer("player").load({ file:arr_sound[j] });

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
		
		jwplayer("player").load({ file:arr_sound[j] });
		jwplayer("player").play();
	}
}

function ganti_terjemah(){
	var terjemah = document.getElementById('cmb_terjemah');
	if(terjemah){
		window.location = "?mod=quran.murotal.show&ganti_terjemah=1&terjemah=" + terjemah.value;
	}
}

function ganti_sura(){
	var sura = document.getElementById('acp_sura');
	var aya  = document.getElementById('txt_aya');
	if(sura){
		var sura_no;
		for(var i = 1 ; i <=114; i++){
			if(arr_sura[i]==sura.value) sura_no = i;
		}
		if(!aya.value) aya.value = 1;
		window.location = "?mod=quran.murotal.show&ganti_sura=1&sura_name=" + sura.value + "&aya=" + aya.value + "#kata_" + sura_no + "_" + aya.value + "_1" ;
	}
}

function edit_terjemah_kata(sura,aya,event){
	$.post('ajax.php?mod=quran.murotal.form_terjemah_kata',
			{
				sura : sura,
				aya  : aya
			},
			function (response){
				$('#form_terjemah_kata').html(response);
				document.getElementById('form_terjemah_kata').style.top = (event.pageY + 25) + 'px'  ;
			}
	);
}

function save_terjemah_kata(){
	var arr_indonesia = new Array();
	for(var i =0; i< 1000; i++){
		if(! $('#txt_indonesia_'+ i) )
			break;
		else{
			arr_indonesia[i] = $('#txt_indonesia_'+ i).val() ;
		}
	}
	
	$.post('ajax.php?mod=quran.murotal.save_terjemah_kata',
		{
			sura : $('#hdn_edited_sura').val(),
			aya : $('#hdn_edited_aya').val(),
			arr_indonesia : arr_indonesia
		},
		function (response){
			window.location.reload();
		}
	);
}

function batal_save(){
	$('#form_terjemah_kata').html('');
	$('#form_terjemah').html('');
}

function edit_terjemah(sura,aya,event){
	$.post('ajax.php?mod=quran.murotal.form_terjemah',
			{
				sura : sura,
				aya  : aya
			},
			function (response){
				$('#form_terjemah').html(response);
				document.getElementById('form_terjemah').style.top = (event.pageY + 25) + 'px'  ;
			}
	);
}

function save_terjemah(){
	$.post('ajax.php?mod=quran.murotal.save_terjemah',
		{
			sura : $('#hdn_edited_sura').val(),
			aya : $('#hdn_edited_aya').val(),
			text : $('#txt_terjemah').val(),
		},
		function (response){
			//alert(response);
			window.location.reload();
		}
	);
}

$(document).ready(function(){

	$('#txt_aya').keypress( function(event){
		if (event.which == '13')
			ganti_sura();
	});
});
