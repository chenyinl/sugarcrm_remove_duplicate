#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php
//This is used to find the duplicates and delete it with user interaction
// Chen Lin
// 10/02/2014
define( "USER_SUB_NEWS", "86ceaca3-6ba9-1795-b027-53c87e634db2" );

if( !isset($argv[1]) ){
    die( "Missing file parameters.\nUsage:./findDuplicateSub.php EMAIL_FILE_NAME\n" );
}

include_once( "SugarFindDupByEmail.class.php" );
echo "This program will go through a list of duplicate accounts.\n";

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

$sc = new Sugarcrm();
while (( $tempData = fgets($handle, 1000)) !== FALSE ) {
    $mailAddress = trim( $tempData );
    echo "Email: ".$mailAddress."\n";
    $result = $sc->searchDuplicate( $mailAddress );
    if( count($result) !=2){
        echo "  Does not have 2 records- skip.\n";
        continue;
    }
    
    echo "  Found ".count( $result )." records.\n";
    foreach( $result as $r ){
        echo_out_detail( $r );
    }
    $newRecord = preapreRecordToUpdate( $result );
    echo "  New Record:\n";
    echo_out_new_detail( $newRecord );

    $deleteRecord = prepareRecordToDelete( $result );
    
    //if( "y" == getInput( "  Update and Continue? (y/n) " )){ //one by one
    if( true ){   // run it all
        $sc->updateLead( array( $newRecord, $deleteRecord) );
        echo "\n";
    }else{
        die( "Bye!\n" );
    }
}

/**
 * functions
 */
function prepareRecordToDelete( $data ){
    if(($data[0]->date_entered->value) < ($data[1]->date_entered->value)){
        $old = $data[0];
        $new = $data[1];
    } else {
        $new = $data[0];
        $old = $data[1];
    }
    $dataToDelete = array(
        array(
            "name" => "id",
            "value" => $old->id->value
        ),
        array(
            "name" => "deleted",
            "value" => 1
        )
    );
    return $dataToDelete;
}

function preapreRecordToUpdate( $data ){
     if(($data[0]->date_entered->value) < ($data[1]->date_entered->value)){
        $old = $data[0];
        $new = $data[1];
    } else {
        $new = $data[0];
        $old = $data[1];
    }
    if(strlen($old->utm_campaign_c->value)>0){
        $utm = $old->utm_campaign_c->value." ";
    }else{
        $utm = "";
    }
    $newData = array(
        array(
            "name" => "id",
            "value" => $new->id->value
        ),
        array(
            "name" => "status",
            "value" => "Converted"
        ),
        array(
            "name" => "assigned_user_id",
            "value" => USER_SUB_NEWS
        ),
        array(
            "name" => "lead_source_description",
            "value" => $utm.$old->date_entered->value." created at Pre-Reg."
        )
    );
    return $newData;
}

function echo_out_detail( $data ){
    echo "    [".$data->id->value."]\t".
        "[".$data->date_entered->value."]\t".
        "[".$data->status->value."]\t".
        "[".$data->campaign_id->value."]\t".
        "[".$data->utm_campaign_c->value."]\t".
        "[".$data->package_purchased_c->value."]\t".
        "\n";
}

function echo_out_new_detail( $data ){
    echo "    [".$data[0]["value"]."] ".
        "[".$data[1]["value"]."] ".
        "[".$data[2]["value"]."] ".
        "[".$data[3]["value"]."]".
        "\n";
}
function getInput( $message ){
    echo $message;
    while(true){
        $ans = trim(fgets( STDIN ));
        if($ans == "y"){
            //echo "You entered 'y'\n";
            return true;
        }else if( $ans == "n"){
            //echo "You entered 'n'\n";
            return false;
        }else{
            echo "Please enter only 'y' or 'n'.\n";
        }
    }
}

