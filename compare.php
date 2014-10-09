#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php

//This is used to update Pre-Reg code on Production 
// Chen Lin
// 09/23/2014
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
$utime= microtime( true ); //unix time, to calculate the time

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

//$emailListx = getList();
//var_dump($emailListx);
$ni=0; //not in
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
    //if( !in_array(strtolower($email), $emailListx)){
        //echo "Not in list: ".$email." ".$ni++."\n";
    //}
    $emailList[]=strtolower($email);
}

fclose( $handle );

echo "Total count: ".$count."\n";
getList($emailList);

function getlist($emailList){

    $file="pregemail.csv";
    $handle = fopen($file, "r");
    $headers = fgetcsv($handle, 1000, "\t");
    $emailListx = array();
    $ni=0;
    while (( $tempData = fgetcsv($handle, 1000, ",")) !== FALSE ) {
        $mail = strtolower($tempData[0]);
        if( !in_array( $mail,$emailList)){
            echo $ni++." Not in list: ".$mail."\n";
        }else{
            $emailListx[]=$mail;
            //echo "no";
        }
    }
    fclose( $handle );
    return $emailListx;
}

