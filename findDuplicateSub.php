#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php

//This is used to find the duplicates and delete it with user interaction
// Chen Lin
// 10/01/2014
echo "This program will go through a list of duplicate accounts\n";
//getInput( "Start (y/n)? " );
$filename = $argv[1];
ini_set("auto_detect_line_endings", "1");
try{
    if (($handle = fopen($filename, "r")) === FALSE) {
        echo "Faile to open file\n";
        exit();
    }
} catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
while (( $tempData = fgets($handle, 1000)) !== FALSE ) {
    echo $tempData."\n";
}


function getInput( $message ){
    echo $message;
    while(true){
        $ans = trim(fgets( STDIN ));
        if($ans == "y"){
            echo "You entered 'y'\n";
            return true;
        }else if( $ans == "n"){
            echo "You entered 'n'\n";
            return false;
        }else{
            echo "Please enter only 'y' or 'n'.\n";
        }
    }
}
/*
define("CAMPAIGN_ID_SUBSCRIBER", "1722198f-b731-240c-ab87-541c80da3b3c");
define("STATUS", "Dead");
include_once( "SugarFindCamp.class.php" );
$sc = new SugarFindCamp();
$emailList = $sc->searchCampaignStatus( CAMPAIGN_ID_SUBSCRIBER, STATUS );
foreach( $emailList as $email ){
    
    $result = $sc->searchDuplicate( $email );
    echo $email.": ".count( $result )."\n";
}
*/


