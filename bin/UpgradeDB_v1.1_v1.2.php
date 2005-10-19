<?php

ini_set( 'include_path', ".:../packages:.." );

$build_version = 1.2 ;

// for SQL l10n use
require_once '../modules/config.inc.php';
require_once 'Smarty/Smarty.class.php';
require_once 'PHP/Beautifier.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/I18n.php';
require_once 'DB.php';

$config   =& new CRM_Core_Config();
$configValues = DB::parseDSN($config->dsn);
$username     = $configValues['username'];
$password     = $configValues['password'];
$dbase        = $configValues['database'];
$host         = $configValues['hostspec'];

//dump the 1.1 version data 
echo "Dumping 1.1 database....\n";

exec($config->mysqlPath."mysqldump -c -t -n -u".$username." -p".$password." ". $dbase."  > generated_data_1.1.mysql");

$dbConnect = DB::connect($config->dsn);

//get the prefix and suffix for the old db
$sql   = 'SELECT id , prefix , suffix , gender FROM civicrm_individual ';
$query = $dbConnect->query($sql);
$prefixSuffix = array( );

while($row = $query->fetchRow( DB_FETCHMODE_ASSOC )) {
    $prefixSuffix[$row['id']] = array($row['prefix'],$row['suffix'],$row['gender']);
}

//drop the existing database
exec($config->mysqlPath."mysqladmin -f" . " -u". $username ." -p".$password ." drop " .$dbase );

//create new database
exec($config->mysqlPath."mysqladmin -f" . " -u". $username ." -p".$password ." create ". $dbase );

//dumped file
$filename = "generated_data_1.1.mysql";
$writeFilename = 'temp.mysql';

//manipulating the dump file
$handleRead = fopen($filename , "r");
$handleWrite = fopen($writeFilename,"w+");

while (!feof($handleRead)) {
    $buffer = fgets($handleRead, 4096);
    $fields = array("prefix","suffix","gender","_suffix_id","_prefix_id","_gender_id");
    $newfields = array("prefix_id","suffix_id","gender_id","_suffix","_prefix","_gender"); 
    $newcontents = str_replace($fields,$newfields,$buffer);
    fwrite($handleWrite,$newcontents);
}

fclose($handleRead);
fclose($handleWrite);


echo "Creating the Database for 1.2\n";
exec($config->mysqlPath."mysql -u".$username." -p".$password." ". $dbase." < ../sql/civicrm_41.mysql");
exec($config->mysqlPath."mysql -u".$username." -p".$password." ". $dbase." < temp.mysql");

unlink('temp.mysql');
unlink('generated_data_1.1.mysql');

$prefix = array(1 => 'Mrs', 2 => 'Ms', 3 => 'Mr', 4 => 'Dr');
$suffix = array(1 => 'Jr', 2 => 'Sr', 3 => 'II');
$gender = array(1 => 'Female', 2 =>'Male',3 => 'Transgender');

//add perfix in individual_prefix
foreach ($prefix as $key => $value ) {
    $query = "INSERT INTO civicrm_individual_prefix(domain_id,name,weight,is_active) VALUES ( 1,'$value', $key, 1)";
    $dbConnect->query($query);
}

//add suffix in individual_suffix
foreach ($suffix as $key => $value ) {
    $query = "INSERT INTO civicrm_individual_suffix(domain_id,name,weight,is_active) VALUES ( 1,'$value', $key, 1)";
    $dbConnect->query($query);
}

//add gender in individual_gender
foreach ($gender as $key => $value ) {
    $query = "INSERT INTO civicrm_gender(domain_id,name,weight,is_active) VALUES ( 1,'$value', $key, 1)";
    $dbConnect->query($query);
}

foreach ($prefixSuffix as $key => $value) {
    $updateColumn = array( );
    $prefix_id    = array_keys($prefix, $value[0]);
    $suffix_id    = array_keys($suffix, $value[1]);
    $gender_id    = array_keys($gender, $value[2]);


    if (count($prefix_id)) {
        $updateColumn[] = ' prefix_id='.$prefix_id[0];
    } 

    if (count($suffix_id)) {
        $updateColumn[] = ' suffix_id='.$suffix_id[0];
    } 

    if (count($gender_id)) {
        $updateColumn[] = ' gender_id='.$gender_id[0];
    } 
    
    if ( count($updateColumn) ) {
        $columns = implode(" , ", $updateColumn);
        $query   = "UPDATE civicrm_individual SET ". $columns ." WHERE id = ".$key;
        $dbConnect->query($query);
    }
}

$dbConnect->disconnect();

?>
