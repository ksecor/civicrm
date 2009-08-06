#!/usr/bin/env php
<?php
/* 
 * This scripts adds new orgs to CiviCRM 3.0+ multi-site setups. You need to
 * create the sites first (i.e. put civicrm.settings.php files in
 * sites/www.example.org/ directories for each of your sites).
 *
 * Written by Wes Morgan
 * 8/6/09
 */

$debug = false;

function run( $argc, $argv ) {
    global $debug;

    session_start( );
    require_once '../civicrm.config.php';
    require_once 'api/v2/Domain.php';
    require_once 'api/v2/Group.php';
    require_once 'api/v2/GroupOrganization.php';
    require_once 'api/v2/Contact.php';

    if ($argc != 3) {
        #var_dump($argv);
        print_usage( $argv[0] );
        exit(-1);
    }

    $org_name = $argv[1];
    $org_desc = $argv[2];

    $config =& CRM_Core_Config::singleton();

    # create the domain
    $existing_domain = civicrm_domain_get( );
    $domain_params = array('name' => $org_name, 'description' => $org_desc,
        'version' => $existing_domain['version']);
    $domain = civicrm_domain_create( $domain_params );
    if ($debug) {
        print "Create domain result: ".print_r($domain)."\n";
    }
    $domain_id = $domain['id'];

    # create the group
    $group_params = array('title' => $org_name, 'description' => $org_desc);
    $group = civicrm_group_add( $group_params );
    if ($debug) {
        print "Create group result: ".print_r($group)."\n";
    }
    $group_id = $group['result'];

    # create the org contact
    $org_params = array('organization_name' => $org_name,
        'contact_type' => 'Organization');
    $org = civicrm_contact_create( $org_params );
    if ($debug) {
        print "Create org contact result: ".print_r($org)."\n";
    }
    $org_id = $org['contact_id'];

    # associate the two
    $group_org_params = array('group_id' => $group_id,
        'organization_id' => $org_id);
    $group_org_id = civicrm_group_organization_create( $group_org_params );
    if ($debug) {
        print "Create group-org association result: ".print_r($group_org_id)."\n";
    }

    print "\n";
    print "Add or modify the following lines in the appropriate ";
    print "civicrm.settings.php file for $org_name:\n";
    print "\tdefine( 'CIVICRM_DOMAIN_ID', $domain_id );\n";
    print "\tdefine( 'CIVICRM_DOMAIN_GROUP_ID', $group_id );\n";
    print "\tdefine( 'CIVICRM_DOMAIN_ORG_ID', $org_id );\n";
    print "\n";
}

function print_usage( $cmd_name ) {
    print "Usage: ".$cmd_name." 'Org Name' 'Org Description'\n";
}

run( $argc, $argv );

?>
