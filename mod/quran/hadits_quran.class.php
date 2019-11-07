<?php
class hadits_quran{
	function fill(){
		exec_sql("DELETE FROM hadits_quran");
		$sql = "SELECT REPLACE(lower(Sumber),' ','') as Sumber, NoHdt, Nama_Surat, Ayat
				FROM hadits.kumpulan_quran
				GROUP BY Nama_Surat, Ayat 
				ORDER BY Urut_Q
				LIMIT 0,100";
		$table = get_table($sql);
		foreach($table as $i => $row){
			$ri = array();
			$ri[hr]		= $row[Sumber];
			$ri[no]		= $row[NoHdt];
			$ri[text] 	= addslashes(exec_scalar("SELECT Isi_Indonesia FROM hadits.had_{$row[Sumber]} WHERE NoHdt={$row[NoHdt]} "));
			$ri[sura]	= addslashes($row[Nama_Surat]);
			$ri[aya]	= $row[Ayat];
			insert("hadits_quran",$ri,1);
		}
		
	}
}

?>