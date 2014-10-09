#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php
include ("db.config.php");
include ("class.MarkPreReg.php");



$oMySQL = new MarkPreReg( DB_NAME,DB_USERNAME, DB_PASSWORD, DB_HOST );

$oMySQL->setQuery();
var_dump($oMySQL->query);
$result = $oMySQL -> ExecuteSQL( $oMySQL->query );
if( $result == FALSE ){
    echo $oMySQL->lastError."\n";
}else{
    var_dump( $result);
}

$oMySQL ->closeConnection();


