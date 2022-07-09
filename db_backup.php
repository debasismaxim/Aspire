<?php
ini_set('memory_limit', '-1');
error_reporting(1);
//global $connection;
function db_connect() {
    static $connection;
	$username	= "aspire"; 
	$password	= "Aspire@234user"; 
	$dbname		= "aspire"; 
	$host		= "localhost";

    if(!isset($connection)) { 
        $connection = mysqli_connect($host,$username,$password,$dbname);
    }
    if($connection === false) {
        return mysqli_connect_error(); 
    }
    return $connection;
}

function db_query($query) {
    $connection = db_connect();
    $result = mysqli_query($connection,$query);
    return $result;
}
function testing() {
   
    return 'kkkkk';
}

function db_error() {
    $connection = db_connect();
    return mysqli_error($connection);
}

$connect = db_connect();
session_start();

//$username	= "root"; 
//$password	= ""; 
//$dbname		= "pumul"; 
//$host		= "localhost";

#Function to backup database to a zip file
function backup() 
{
  //$suffix = time();
  #Execute the command to create backup sql file
 // exec("mysqldump --user={$username} --password={$password} --quick --add-drop-table --add-locks --extended-insert --lock-tables --all {$dbname} > backups/backup.sql");
/*  
	exec("mysqldump {$dbname} --password={$password} --user={$username} --single-transaction >backups/backup.sql");

  #Now zip that file
  $zip = new ZipArchive();
  $filename = "backups/backup-$suffix.zip";
  if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
   exit("cannot open <$filename>n"); 
  }
  $zip->addFile("backups/backup.sql" , "backup.sql");
  $zip->close();
  #Now delete the .sql file without any warning
  @unlink("backups/backup.sql");
  #Return the path to the zip backup file
  return "backups/backup-$suffix.zip";
*/

/* Store All Table name in an Array */
$allTables = array();
$result = db_query('SHOW TABLES');
while($row = mysqli_fetch_row($result)){
     $allTables[] = $row[0];
}

foreach($allTables as $table){
$result = db_query('SELECT * FROM '.$table);
$num_fields = mysqli_num_fields($result);

$return.= 'DROP TABLE IF EXISTS '.$table.';';
$row2 = mysqli_fetch_row(db_query('SHOW CREATE TABLE '.$table));
$return.= "\n\n".$row2[1].";\n\n";

for ($i = 0; $i < $num_fields; $i++) {
while($row = mysqli_fetch_row($result)){
   $return.= 'INSERT INTO '.$table.' VALUES(';
     for($j=0; $j<$num_fields; $j++){
       $row[$j] = addslashes($row[$j]);
       $row[$j] = str_replace("\n","\\n",$row[$j]);
       if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } 
       else { $return.= '""'; }
       if ($j<($num_fields-1)) { $return.= ','; }
     }
   $return.= ");\n";
}
}
$return.="\n\n";
}

// Create Backup Folder
//$folder = 'DB_Backup/';
//if (!is_dir($folder))
//mkdir($folder, 0777, true);
//chmod($folder, 0777);

$suffix = date('m-d-Y-H-i-s', time()); 
$sqlfile = "db-backup".$suffix.".sql"; 

$handle = fopen($sqlfile,'w+');
fwrite($handle,$return);
fclose($handle);

  #Now zip that file
  $zip = new ZipArchive();
  $filename = $folder."backup-$suffix.zip";
  if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
   exit("cannot open <$filename>n"); 
  }
  $zip->addFile($sqlfile , "db-backup".$suffix.".sql");
  $zip->close();
  #Now delete the .sql file without any warning
  //@unlink($sqlfile);
  #Return the path to the zip backup file
  return $filename;


 
}
backup();
?>