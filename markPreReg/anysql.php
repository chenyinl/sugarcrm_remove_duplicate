#!/Applications/MAMP/bin/php/php5.4.10/bin/php
<?php
include ("db.config.php");
include ("class.MySQL.php");
/*
$query="
SELECT
    le.id,
    eaddr.email_address as email
FROM 
    leads AS le,
    leads_cstm AS lecstm,
    email_addresses AS eaddr,
    email_addr_bean_rel AS ebean
WHERE 
    le.id=lecstm.id_c AND
    lecstm.pre_reg_subscriber_c=1 AND
    le.deleted=0 AND
    ebean.bean_id = le.id AND
    ebean.email_address_id=eaddr.id AND
    eaddr.email_address='barbgreen33@comcast.net'
";
$query="
SELECT
    count(le.id)
    eaddr.email_address as email,
    count(eaddr.email_address) AS c
FROM 
    leads AS le,
    leads_cstm AS lecstm,
    email_addresses AS eaddr,
    email_addr_bean_rel AS ebean
WHERE 
    le.id=lecstm.id_c AND
    lecstm.pre_reg_subscriber_c=1 AND
    le.deleted=0 AND
    ebean.bean_id = le.id AND
    ebean.email_address_id=eaddr.id AND
    eaddr.email_address='barbgreen33@comcast.net'
GROUP BY
    email

";
$query="
SELECT
    eaddr.email_address
FROM 
    leads AS le,
    leads_cstm AS lecstm,
    email_addresses AS eaddr,
    email_addr_bean_rel AS ebean
WHERE 
    le.id=lecstm.id_c AND
    lecstm.pre_reg_subscriber_c=1 AND
    le.deleted<>1 AND
    eaddr.deleted<>1 AND
    ebean.bean_id = le.id AND
    ebean.email_address_id=eaddr.id

";
//fine one by one
$query = "
SELECT 
    le.id
FROM 
    email_addresses AS eaddr, 
    email_addr_bean_rel AS ebean,
    leads AS le
WHERE 
    le.id = ebean.bean_id AND
    ebean.bean_module='Leads' AND
    ebean.email_address_id=eaddr.id AND
    le.deleted<>1 AND
    eaddr.email_address IN ('darleneross624@yahoo.com')
";
//find deleted email
$query = "
SELECT 
    es.email_address
FROM
    email_addr_bean_rel AS el,
    email_addresses AS es
WHERE 
    el.deleted=1 AND
    el.email_address_id=es.id
";
*/
$query="
SELECT 
  /* count(le.id) AS lid */
  es.email_address AS email,
  count(es.email_address) AS c
FROM 
    leads AS le,
    leads_cstm as lm, 
    email_addresses AS es,
    email_addr_bean_rel AS el
WHERE 
    le.id=lm.id_c AND
    le.id=el.bean_id AND
    el.email_address_id=es.id AND
    el.deleted<>1 AND
    lm.pre_reg_subscriber_c=1 AND
    le.deleted<>1
GROUP BY
    es.email_address
";

// fine accounts form email that has more associated
$mailstr="(
'gmhbc344@yahoo.com',
'CThomp2623@aol.com',
'wandajo1972@hotmail.com',
'brendalange@comcast.net',
'pkcfibrohote@mchsi.com',
'barbgreen33@comcast.net',
'easley4nc@yahoo.com',
'ebranch9985@yahoo.com',
'dakotahwallace@yahoo.com'
)";
$query = "
SELECT
    le.first_name AS fn,
    le.last_name AS ln,
    le.id an,
    es.email_address AS em
FROM 
    leads AS le,
    email_addresses AS es,
    email_addr_bean_rel AS el
WHERE
    es.email_address IN ".$mailstr." AND
    le.id=el.bean_id AND
    el.email_address_id=es.id AND
    le.deleted<>1    

";




$oMySQL = new MySQL( DB_NAME,DB_USERNAME, DB_PASSWORD, DB_HOST );
$result = $oMySQL -> ExecuteSQL( $query );
//var_dump($result);
//exit();
if( $result === false ){
    echo $oMySQL->lastError."\n";
}else{
    //var_dump($result);
    //echo $result["email_address"]."\n";
    //var_dump( $result);
}

$oMySQL ->closeConnection();
//exit();
foreach ($result as $r){
   echo $r["fn"];
   echo " ";
   echo $r["ln"];
   echo " ";
   echo $r["em"];
   echo " ";
   echo $r["an"];
   echo " \n";   

}
/*
$dup=0;
foreach ($result as $r ){
    if($r["c"]!=1){
        var_dump($r);
        $dup++;
    }

echo "\nDuplicates: ".$dup."\n";

SELECT * 
FROM  `leads` 
WHERE id =  "d18e5bdb-4fb3-4748-d5cf-5420753acc87"
OR id =  "723f6e47-6374-7d3b-35d3-53cd8ac73ec8"
LIMIT 0 , 30
*/
