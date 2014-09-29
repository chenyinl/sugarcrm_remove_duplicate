#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php

//This is used to update UTM and plan code on Production 
// Chen Lin
// 09/23/2014

define("CAMPAIGN_ID_SUBSCRIBER", "1722198f-b731-240c-ab87-541c80da3b3c");
if( !isset($argv[1]) || !isset($argv[2]) ){
    echo "Missing file parameters.\nUsage:./findDuplicate.php Good.csv limit\n";
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

$filename = $argv[1];
$maxrow = 100000;
if( ( (INT)$argv[2] != -1 )){
    $maxrow = $argv[2];
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
    //var_dump($tempData);
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
            

            //$saffron_id = str_replace("\n", "", $saffron_id);
            $attributes = json_decode($attributes);

            $utm = $attributes->utm_campaign;
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
            $result = $sc->searchDuplicate( $email );
            if($result === FALSE){
                echo $email." - has only one record.\n\n";
            }else{
                //var_dump($result);
                $dup_count = count($result);
                if( $dup_count==2) {
                    if(($result[0]->date_entered->value) < ($result[1]->date_entered->value)){
                        $old = $result[0];
                        $new = $result[1];
                    } else {
                        $new = $result[0];
                        $old = $result[1];
                    }

                    echo $email." - found ".$dup_count." records\n";
                    //echo "old: ".var_dump( $old );
                    //echo "new: ".var_dump( $new );
                    echo "old: ".$old->id->value." ".$old->date_entered->value."\n".
                    "new: ".$new->id->value." ".$new->date_entered->value."\n\n";
                    $updatedata = 
                    array(
                        array(
                            "name" => "id",
                            "value" => $new->id->value
                        ),
                        array(
                            "name" => "status",
                            "value" => $old->status->value
                        ),
                        array(
                            "name" => "package_purchased_c",
                            "value" => $old->package_purchased_c->value
                        ),
                        array(
                            "name" => "assigned_user_id",
                            "value" => $old->assigned_user_id->value
                        ),
                        array(
                            "name" => "utm_campaign_c",
                            "value" => $old->utm_campaign_c->value
                        ),
                        array(
                            "name" => "lead_source_description",
                            "value" => $old->date_entered->value." created at Pre-Reg."
                        )
                        
                    );
                $deletedata = array(
                    array(
                        "name" => "id",
                        "value" => $old->id->value
                    ),
                    array(
                        "name" => "deleted",
                        "value" => 1
                    )
                );
                $sc->updateLead( array( $updatedata, $deletedata) );
                } else{
                    echo $email." - found ".$dup_count." records\n";
                }
                
                //echo $row.". find ". $email." - ".$lead_id." with plan ".$window_id." CRM id: ".$source."\n";
            }

  
 
            //
            $row++;   
        }
fclose( $handle );



