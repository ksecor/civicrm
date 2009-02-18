<?php

require_once '../civicrm.config.php';

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/I18n.php';

require_once 'CRM/Contact/BAO/Group.php';

$config = CRM_Core_Config::singleton();

$prefix = 'Automated Generated Group: ';
$query = "DELETE FROM civicrm_group where name like '%{$prefix}%'";
CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

$numGroups = 100;

$visibility = array( 'User and User Admin Only', 'Public Pages' );
$groupType  = array( null, '1', '2', '12' );

for ( $i = 1; $i <= $numGroups; $i++ ) {
	$group = new CRM_Contact_BAO_Group();
    $cnt      = sprintf( '%05d', $i );
    $alphabet = mt_rand( 97, 122 );
    $group->name = $group->title = chr($alphabet) . ": $prefix $cnt";
    $group->is_active  = 1;

    $v = mt_rand( 0, 1 );
    $group->visibility = $visibility[$v];

    $t = mt_rand( 0, 3 );
    $group->group_type = $groupType[$t];

    $group->save( );
}


