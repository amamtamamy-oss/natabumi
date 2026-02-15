<?php
// SETTING DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$name = "db_banksampah"; // Ganti nama database jika beda

// KONFIGURASI BACKUP
$tables = '*';
$link = mysqli_connect($host, $user, $pass, $name);
mysqli_query($link, "SET NAMES 'utf8'");

// DAPATKAN TABEL
if($tables == '*'){
    $tables = array();
    $result = mysqli_query($link, 'SHOW TABLES');
    while($row = mysqli_fetch_row($result)){ $tables[] = $row[0]; }
} else {
    $tables = is_array($tables) ? $tables : explode(',',$tables);
}

// LOOP TABEL
$return = "";
foreach($tables as $table){
    $result = mysqli_query($link, 'SELECT * FROM '.$table);
    $num_fields = mysqli_num_fields($result);
    $return .= 'DROP TABLE IF EXISTS '.$table.';';
    $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE '.$table));
    $return.= "\n\n".$row2[1].";\n\n";
    for ($i = 0; $i < $num_fields; $i++){
        while($row = mysqli_fetch_row($result)){
            $return.= 'INSERT INTO '.$table.' VALUES(';
            for($j=0; $j < $num_fields; $j++){
                $row[$j] = addslashes($row[$j]);
                if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                if ($j < ($num_fields-1)) { $return.= ','; }
            }
            $return.= ");\n";
        }
    }
    $return.="\n\n\n";
}

// SAVE FILE
$nama_file = 'backup-db-'.$name.'-'.date("Y-m-d-H-i-s").'.sql';
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$nama_file."\""); 
echo $return; exit;
?>