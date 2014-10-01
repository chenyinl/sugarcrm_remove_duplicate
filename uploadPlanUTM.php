#!/usr/bin/php
<?php

//This is used to update UTM and plan code on Production 
// Chen Lin
// 09/23/2014

define("CAMPAIGN_ID_SUBSCRIBER", "1722198f-b731-240c-ab87-541c80da3b3c");
if( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3])){
    echo "Missing file parameters.\nUsage:./findDuplicate.php Good.csv start limit\n";
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

$row = 1;
$planCount=array();
    $planCount[0]=0;
    $planCount[1]=0;
    $planCount[2]=0;
    $planCount[3]=0;
   include_once( "Sugarcrm.class.php" );
    //include_once( "LocalDB.class.php" );
$success=true; //the success data
    $sc = new Sugarcrm();
        //$sc -> login();

        $count = 0;
        //$data = fgetcsv($handle, 1000, ",");

        $rowData = fgetcsv($handle, 1000, "\t"); //remove first line
        $sc = new Sugarcrm();
        $sc->login();
        // use local database
        //$db = new LocalDB();
        //$db -> readDB();
        //$localData = $db->data;

while (( $tempData = fgetcsv($handle, 1000, "\t")) !== FALSE && $count++ < $maxrow) {
    if($count< $startRow) {
        //echo "Skip ".$count." ";
        continue;
    }
    if($count== $startRow) {
        echo "Start from ".$count." to ".$maxrow."\n";
    }
    //var_dump($tempData);
    if(!($tempData[0])) continue;
            list(
                $saffron_id,
                //$external_id  
                $email,
                $attributes,
                //$password,
                $window_id,
                $status,
                $modufied
            ) = $tempData;
            

            //$saffron_id = str_replace("\n", "", $saffron_id);
            $attributes = json_decode($attributes);
            if( isset($attributes->utm_campaign)){
                $utm = $attributes->utm_campaign;
            }else{
                $utm = "00000";
            }
            
            if( $success){
                switch( $window_id ){
                    case ( "d611a7e7-a767-4bc3-9bfa-65dc079e0f57" ):
                        $plan = 1;
                        $planCount[1]++;
                        break;
                    case ( "da320f02-2d56-4833-b0c7-b1d1fbc01a53" ):
                        $plan = 2;
                        $planCount[2]++;
                        break;
                    case ( "ac882d72-e89d-4cc8-bea3-7536b7539551" ):
                        $plan = 3;
                        $planCount[3]++;
                        break;
                }
                // not pulling from attribute
                //$plan = $attributes->price_selection;
            } else {
                $plan = 0;
                $planCount[0]++;
            }
            if($email=="NULL") continue;
            $result = $sc->searchByEmail( $email );
            if( !$result ) continue;
            //var_dump($result);
            $id = $result->id->value;
            $needUpdate=false;
            
            $current_utm = $result->utm_campaign_c->value;
            if(strlen($current_utm)==0){
                echo "Use csv UTM\n";
                $needUpdate=true;
                $current_utm = $utm;
            }
            $current_plan = $result->package_purchased_c->value;
            if(strlen($current_plan)==0){
                echo "Use csv plan\n";
                $needUpdate=true;
                $current_plan = $window_id;
            }
            
            
             $t = round((microtime( true )-$utime), 2);
                $utime=microtime( true );
            if($result != FALSE){
               
                echo $count.". ".$email." - one record: ".$id.". Current UTM/Plan: ".$current_utm."/".$current_plan."- time: ".$t."\n";
                
            }else{
                echo $count.". ".$email." - has more than one record, skip. - time: ".$t."\n";
                continue;
            }
            if( $needUpdate ){
                $updateData = 
                    array(
                        array(
                            "name" => "id",
                            "value" => $id
                        ),
                     
                        array(
                            "name" => "package_purchased_c",
                            "value" => $current_plan
                        ),
                    
                        array(
                            "name" => "utm_campaign_c",
                            "value" => $current_utm
                        )
                    );
                
                $updateResult = $sc->updateLead( array($updateData) );
                //var_dump($updateResult);
            }
            
            $row++;   
        }
fclose( $handle );



