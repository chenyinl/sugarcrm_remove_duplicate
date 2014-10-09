<?php
include ("class.MySQL.php");
class MarkPreReg extends MySQL
{
    var $emailList = array();         // email array
    var $query;                      // query for the 

    public function setQuery()
    {
        // the final query
        $this->query = "
            UPDATE leads_cstm leadscstm
            SET pre_reg_subscriber_c=1
            WHERE 
                leadscstm.id_c IN (
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
                    eaddr.email_address IN (";
        $emails = "";
        foreach( $this->emailList as $email ){
            $emails.= "\"".$email."\", ";
        }
        $emails = substr( $emails, 0, strlen($emails)-2);
        $this->query.= $emails."))";
    }
    //find leads that associated to the emails
    public function testQuery(){
        $this->query = "

            SELECT 
                /*DISTINCT eaddr.email_address*/
                count(le.id)
            FROM 
                email_addresses AS eaddr, 
                email_addr_bean_rel AS ebean,
                leads AS le
            WHERE 
                le.id = ebean.bean_id AND
                ebean.bean_module='Leads' AND
                ebean.email_address_id=eaddr.id AND
                le.deleted<>1 AND
                eaddr.email_address IN (";
        $emails = "";
        foreach( $this->emailList as $email ){
            $emails.= "\"".$email."\", ";
        }
        $emails = substr( $emails, 0, strlen($emails)-2);
        $this->query.= $emails.")";
    }
        public function test2Query(){
            $emailstr="";
            foreach( $this->emailList as $email ){
            $emailstr.= "\"".$email."\", ";
        }
        $emailstr = substr( $emailstr, 0, strlen($emailstr)-2);
         //found 11 dup
        $this->query = "
          
        SELECT *
        FROM(
            SELECT 
                ebean.bean_id AS bid,
                eaddr.email_address eadd,
                /*count(eaddr.email_address) AS ec*/
            FROM 
                email_addresses AS eaddr, 
                email_addr_bean_rel AS ebean,
                leads AS le
            WHERE 
                le.deleted<>1 AND
                le.id=ebean.bean_id AND
                ebean.bean_module='Leads' AND
                ebean.email_address_id=eaddr.id AND
                ebean.deleted<>1 AND
                eaddr.email_address IN (".$emailstr.")
            GROUP BY
                eaddr.email_address
        ) AS bs
        /*WHERE bs.ec<>'1'*/
            ";
        $this->query="
        SELECT * 
        FROM(
        SELECT 
            COUNT(ebean.email_address_id) AS c,
            le.id AS er
        FROM 
            email_addresses AS eaddr, 
            email_addr_bean_rel AS ebean,
            leads AS le
        WHERE
            le.id=ebean.bean_id AND
            le.deleted<>1 AND
            eaddr.id=ebean.email_address_id AND
            eaddr.email_address IN (".$emailstr.")
        GROUP BY
            ebean.email_address_id
        ) AS ttable
        WHERE ttable.c<>1
        ";

    }
    public function setEmailList( $list )
    {
        $this->emailList = $list;
    }

}