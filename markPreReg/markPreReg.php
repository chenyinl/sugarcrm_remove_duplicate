#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php
/**
 * This is used to update Pre-Reg code on Production
 * It takes Saffron CSV on 9/16, take the email, fine the corresponding 
 * Lead account and update the "pre-reg" field to 1
 * @author Chen Lin clin@one-k.com
 * @example ./markPreReg.php Good.vsc 0 -1
 * 2014/10/8
 */

include ("db.config.php");
include ("class.MarkPreReg.php");

if( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3])){
    echo "Missing file parameters.\nUsage:./markPreReg.php Good.csv start limit\n";
    die();
}
if( !is_file( $argv[1] ) ){
    echo "Faile to open file ".$argv[1]."\n";
    exit();
}

if( !( ((STRING)(INT)$argv[2] )== $argv[2])){
    echo "The second parameters ".$argv[2]." has to be integer; you put ".gettype($argv[2])."\n";
    exit();
}

if( !( ((STRING)(INT)$argv[3] )== $argv[3])){
    echo "The third parameters ".$argv[3]." has to be integer; you put ".gettype($argv[3])."\n";
    exit();
}

$filename = $argv[1];
$maxrow = 100000;

$startRow = (INT)$argv[2];
if( ( (INT)$argv[3] != -1 )){
    $maxrow = $startRow + (INT)$argv[3];
}
// try to open file to read
try{
    if (($handle = fopen($filename, "r")) === FALSE) {
        echo "Faile to open file\n";
        exit();
    }
} catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

$count=0;
$emailList = array();
$tempData = fgetcsv($handle, 1000, "\t");
while (( $tempData = fgetcsv($handle, 1000, "\t")) !== FALSE && $count++ < $maxrow) {
    if($count< $startRow) {
        //echo "Skip ".$count." ";
        continue;
    }
    if($count== $startRow) {
        echo "Start from  ".$count."\n";
    }
            if(!($tempData[0])) continue;
            list(
                $saffron_id,
                //$external_id  
                $email,
                $attributes,
                //$password,
                $window_id,
                $status
            ) = $tempData;
    //echo $count.". ".$email."\n";
    $emailList[]=$email;
}

fclose( $handle );
echo "Total count: ".$count."\n";
$oMySQL = new MarkPreReg( DB_NAME,DB_USERNAME, DB_PASSWORD, DB_HOST );
$oMySQL->setEmailList($emailList);

$oMySQL->setQuery();

//var_dump($oMySQL->query);
$result = $oMySQL -> ExecuteSQL( $oMySQL->query );
if( $result == FALSE ){
    echo $oMySQL->lastError."\n";
}else if ($result === TRUE){
    echo "SQL executed successfully\n";
    //var_dump( $result);
}else{
    foreach ($result AS $r ){
        echo $r["email_address"]."\n";
    }
}

$oMySQL ->closeConnection();


