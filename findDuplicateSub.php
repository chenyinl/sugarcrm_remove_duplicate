#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php

//This is used to update UTM and plan code on Production 
// Chen Lin
// 09/23/2014
define("CAMPAIGN_ID_SUBSCRIBER", "1722198f-b731-240c-ab87-541c80da3b3c");
include_once( "Sugarcrm.class.php" );
$sc = new Sugarcrm();
$sc->searchCampaignStatus( CAMPAIGN_ID_SUBSCRIBER );
