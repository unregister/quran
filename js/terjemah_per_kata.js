function move_terjemah(sura,aya,no,event){
	var terjemah_con = document.getElementById('terjemah_con');
	terjemah_con.style.visibility = 'visible';
	
	//alert('terjemah_' + sura + '_' + aya + '_' + no);
	var terjemah = document.getElementById('terjemah_' + sura + '_' + aya + '_' + no);
	
	terjemah_con.innerHTML = terjemah.innerHTML;

	terjemah_con.style.left = (event.pageX - 50) + 'px';
	terjemah_con.style.top = (event.pageY + 25) + 'px'  ;

	var kata = document.getElementById('kata_'+ sura + '_' + aya + '_' + no);
	kata.style.color = 'red';
	kata.style.background ='#EDEDFF';
	//alert()
}

function hide_terjemah(sura,aya,no){
	document.getElementById('terjemah_con').style.visibility = 'hidden';
	
	var kata = document.getElementById('kata_'+ sura + '_' + aya + '_' + no);
	kata.style.color = 'black';
	kata.style.background ='transparent';
	
}

function move_petunjuk(petunjuk,event){
	var terjemah_con = document.getElementById('terjemah_con');
	terjemah_con.style.visibility = 'visible';
	
	terjemah_con.innerHTML = petunjuk;

	terjemah_con.style.left = (event.pageX - 50) + 'px';
	terjemah_con.style.top = (event.pageY + 25) + 'px'  ;

}

function hide_petunjuk(){
	document.getElementById('terjemah_con').style.visibility = 'hidden';
}

function nyala(j){
	var terjemah = document.getElementById('terjemah_'+j);
	var aya 	 = document.getElementById('aya_'+j);
	if(terjemah) terjemah.style.background ='#DDFFDD';
	if(aya) 	 aya.style.background ='#DDFFDD';
}	

function padam(j){
	var terjemah = document.getElementById('terjemah_'+j);
	var aya 	 = document.getElementById('aya_'+j);
	if(terjemah) terjemah.style.background ='transparent';
	if(aya) 	 aya.style.background ='transparent';
}	


