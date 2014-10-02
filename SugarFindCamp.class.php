<?php
require_once( "Sugarcrm.class.php" );
class SugarFindCamp extends Sugarcrm {
    
    public function __construct() 
    { 
        
    } 
       public function searchCampaignStatus( $campaign_id, $status ){
        if(! $this->session_id) $this->login();
        $get_entry_list_parameters = array(
             //session id
             'session' => $this->session_id,
             //The name of the module from which to retrieve records
             'module_name' => 'Leads',
             //The SQL WHERE clause without the word "where".
             'query' => "leads.status='".$status."' AND leads.campaign_id='".$campaign_id."'",
             //The SQL ORDER BY clause without the phrase "order by".
             'order_by' => "",
             //The record offset from which to start.
             'offset' => 100,
             //A list of fields to include in the results.
             'select_fields' => array(
                  'id'//,

                  //'name',
                  //'title',
             ),
             //A list of link names and the fields to be returned for each link name.

             'link_name_to_fields_array' => array(
                 array(
                     'name' => 'email_addresses',
                     'value' => array(
                         'email_address',
                         //'opt_out',
                         'id'
                     ),
                 ),
             ),
             //The maximum number of results to return.
             'max_results' => "",
             //If deleted records should be included in results.
             'deleted' => 0,
             //If only records marked as favorites should be returned.
             'favorites' => false,
        );

        $search_result = $this->call('get_entry_list', $get_entry_list_parameters);
        //var_dump($search_result);
        if( !$search_result){
            die( "search failed\n");
        }
        $email_list_array = array();
        foreach ($search_result -> relationship_list as $rel){
            //var_dump( $rel );
            $email_list_array[$rel->link_list[0]->records[0]->link_value->id->value] =
                ($rel->link_list[0]->records[0]->link_value->email_address->value);
        }
        return $email_list_array; 
    }

}