<?php

ini_set( 'include_path', ".:../packages:.." );
//require_once '/home/kurund/public_html/drupal/sites/default/civicrm.settings.php';
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Quest/API.php';
require_once 'api/crm.php';


//code need to be removed
$groupParams = array( 'name'        => 'Preapplication_Completed');
$groupCompleted = crm_get_groups( $groupParams );

//get relationship records
require_once  'CRM/Core/DAO.php';
$contactRelationships = array( );

$query = "
SELECT c1.id as contact_id, civicrm_relationship.id as relationship_id, c1.sort_name AS c1name, c2.sort_name AS organization_name, start_date, end_date
FROM civicrm_contact AS c1, civicrm_relationship, civicrm_custom_value, civicrm_contact AS c2
WHERE c1.id = civicrm_relationship.contact_id_a
AND civicrm_relationship.relationship_type_id =8
AND civicrm_custom_value.entity_table = 'civicrm_contact'
AND civicrm_custom_value.entity_id = civicrm_relationship.contact_id_b
AND civicrm_custom_value.custom_field_id =4
AND c2.id = civicrm_relationship.contact_id_b
ORDER BY civicrm_relationship.id DESC
";

$p = array();
$dao =& CRM_Core_DAO::executeQuery( $query, $p );
while ( $dao->fetch( ) ) {
  $contactRelationships[$dao->relationship_id]['contact_id'       ] = $dao->contact_id;
  $contactRelationships[$dao->relationship_id]['organization_name'] = $dao->organization_name;
  $contactRelationships[$dao->relationship_id]['start_date'       ] = $dao->start_date;
  $contactRelationships[$dao->relationship_id]['end_date'         ] = $dao->end_date;
}

echo "Total Relationships:" . count($contactRelationships) . "\n";

//check for duplicate relationships
$relationshipsToDelete = array( );
$relationshipsToKeep   = array( );

echo "Checking for duplicate Relationships ...\n";
foreach ($contactRelationships as $key => $var) {
  $contactId = $var['contact_id'];
  if ( array_key_exists( $contactId, $relationshipsToKeep ) ) {
    foreach ($relationshipsToKeep[$contactId] as $k => $v)  {
      if ( $v['organization_name'] == $var['organization_name'] && $v['start_date'] == $var['start_date'] && $v['end_date'] == $var['end_date'] ) {
	$relationshipsToDelete[$key] = 1;
      } else {
	$relationshipsToKeep[$contactId][$key]['organization_name'] = $var['organization_name'];
	$relationshipsToKeep[$contactId][$key]['start_date'       ] = $var['start_date'  ];
	$relationshipsToKeep[$contactId][$key]['end_date'         ] = $var['end_date'    ];
      }
    }

  } else {
    $relationshipsToKeep[$contactId][$key]['organization_name'] = $var['organization_name'];
    $relationshipsToKeep[$contactId][$key]['start_date'       ] = $var['start_date'  ];
    $relationshipsToKeep[$contactId][$key]['end_date'         ] = $var['end_date'    ];
  }
}

echo "Duplicate Relationships:" . count($relationshipsToDelete) . "\n";

//delete duplicate relationships
echo "Deleting Duplicate Relationships\n";
require_once 'CRM/Contact/BAO/Relationship.php';
foreach ($relationshipsToDelete as $k1 => $v1 ) {
  CRM_Contact_BAO_Relationship::del($k1);   
}

echo "Done...\n";



?>