<?php


function insert($table,$row,$echo =0 ){
	$columnName = "(";
	foreach($row as $key => $value){
		$key = str_replace("\'","",$key);
		$columnName .= "`$key`,"; 
	}
	$columnName = substr($columnName ,0,strlen($columnName ) -1) .  ")";

	$sql = "INSERT INTO ".$table.$columnName." VALUES(";
	foreach($row as $key => $value){
		//if(!get_magic_quotes_gpc()) $value = addslashes($value);
		$value = ($value=="NULL")?$value:"\"$value\"";
		$sql .= "$value,"; 
	}
	$sql = substr($sql,0,strlen($sql) -1) .  ")";
	mysql_query($sql);
	
	//echo $sql;
	$error = mysql_error();
	if($echo)
		echo "
		<pre>
		$sql
		$error
		</pre>";
		
	if($error) return false;
	else return true;

}

function update($table,$row,$syarat,$echo =0 ){
	
	$sql = "UPDATE $table SET ";
	foreach($row as $key => $value){
		$key = str_replace("\'","",$key);
	//	if(!get_magic_quotes_gpc())			$value = addslashes($value);

		$value = ($value=="NULL")?$value:"\"$value\"";
		$sql .= " `$key` = $value,"; 
	}
	$sql = substr($sql,0,strlen($sql) -1) . " WHERE $syarat ";
	
	//echo $sql;
	mysql_query($sql);
	//return $sql;
	$error = mysql_error();
	if($echo)
		echo "
		<pre>
		$sql
		$error
		</pre>";
	if($error) return false;
	else return true;
	
}

function remove($table,$syarat,$echo =0 ){
	$sql = "DELETE FROM $table WHERE $syarat";
	mysql_query($sql);
	$error = mysql_error();
	if($echo)
		echo "
		<pre>
		$sql
		$error
		</pre>";
	if($error) return false;
	else return true;
}

function exec_scalar($sql,$echo =0){
	$table = get_table($sql);
	if($echo) echo $sql;
	
	if(!empty($table))
		return $table[0][0];
	else
		return false;
}

function exec_sql($sql,$echo =0){
 	$res = mysql_query($sql);
	$error = mysql_error();
	if($echo) 
		echo "<pre>
				$sql
				$error
			</pre>";
	
	if($error) {
		echo "'$error'";
		return false;
	}
	return true;
}

function get_table($sql,$echo =0){
	if(strlen(strpos(strtoupper($sql),"SELECT"))==0){
		$sql = "SELECT * FROM ".$sql;
	}
	$res = mysql_query($sql);
	$table = array();
	$error = mysql_error();

	while($row = mysql_fetch_array($res)){
		$table[] = $row;
	}

	$error = mysql_error();
	//echo "<pre>$sql</pre><br>$error";
	//echo $error;
	if($echo||$error) 	echo "<pre>$sql</pre><br>$error";
	
	return $table;
}

function get_row($sql,$echo =0){
	$table = get_table($sql,$echo);
	$row = array();
	if(count($table)>0){
		$row=$table[0];
	}
	return $row;
}

function get_column($sql,$echo =0){
	$table = get_table($sql,$echo);
	
	$arr = array();
	if(count($table))
		foreach($table as $i => $row){
			$arr[] = $row[0];
		}
	
	return $arr;
}


function remove_order($sql){
	$order_position = strpos(strtoupper($sql), 'ORDER BY');
	if($order_position > 1){
		$order = substr($sql,$order_position );
		$sql = str_ireplace($order," ",$sql);
	}
	return $sql;
}

function sql_count($sql){
	//echo $sql;
	// MENCARI POSISI 'FROM'
	$len = strlen($sql);
	// LOOP DARI HURUF PAING BELAKANG
	for($i=$len-1; $i >0 ;$i-- ){
		// JIKA KETEMU ) maka $kurung++
		if($sql[$i]==')') $kurung++;
		if($sql[$i]=='(') $kurung--;
		
		
		if(strtoupper(substr($sql,$i,4))=='FROM'  && $kurung==0){
			$from_position = $i;
			break;
		}
	}
	
	if(!$from_position){
		echo " GAGAL MEMBUAT SELECT COUNT(*) SECARA OTOMATIS ";
		return ' SELECT 0 ';
	}	
	
	$table_name = substr($sql,$from_position + 4);
	$sql_count = remove_order("SELECT count(*) FROM $table_name");
	//echo_pre($sql_count);
	return $sql_count;	
}

?>