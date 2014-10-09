#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php
$file="pregemail.csv";
$handle = fopen($file, "r");
$headers = fgetcsv($handle, 1000, "\t");
$emailList = array();
while (( $tempData = fgetcsv($handle, 1000, ",")) !== FALSE ) {
    $mail = $tempData[0];
    if( in_array( $mail,$emailList)){
        echo $mail."\n";
    }else{
        $emailList[]=$mail;
        //echo "no";
    }
}
fclose( $handle );