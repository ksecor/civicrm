<?php

require_once 'CRM/Pager.php';

/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria
 *
 * This class is a generic class and should be used by any / all
 * objects that requires contacts to be selectively listed (list / search)
 *
 */
class CRM_Contacts_Selector extends CRM_Selector implements CRM_Selector_API {

  static $_links = array(
                         CRM_Action::VIEW => array(
                                                   'name'     => 'View Contact',
                                                   'link'     => '/crm/contact?action=view&id=%%id%%',
                                                   'linkName' => 'View Contact',
                                                   'menuName' => 'View Contact Details'
                                                   ),
                         CRM_Action::EDIT => array(
                                                   'name'     => 'Edit Contact',
                                                   'link'     => '/crm/contact?action=edit&id=%%id%%',
                                                   'linkName' => 'Edit Contact',
                                                   'menuName' => 'Edit Contact Details'
                                                   ),
                         );

  protected $_contact;

  function __construct() {
    $this->_contact = new CRM_Contacts_BAO_Contact_Individual();
    
    $contact->domain_id = 1;
  }

  function &getLinks() {
    return CRM_Contacts_Selector::$_links;
  }

  function getPagerParams( $action, &$params ) {
    $params['status']    = "Contacts %%statusMessage%%";
    $params['csvString'] = null;
    $params['rowCount']  = CRM_Pager::ROWCOUNT;
  }

  function getSortOrder( $action ) {
    static $order = array(
                          'Individual_last_name'  => CRM_Sort::ASCENDING,
                          'Individual_first_name' => CRM_Sort::ASCENDING,
                          'Email_email'           => CRM_Sort::ASCENDING
                          );
    return $order;
  }

  function getColumnHeaders( $action ) {
    static $headers = array(
                            array(
                                  'label' => 'First Name',
                                  'sort'  => 'Individual_first_name',
                                  ),
                            array(
                                  'label' => 'Last Name',
                                  'sort'  => 'Individual_last_name',
                                  ),
                            array(
                                  'label' => 'Email'
                                  'sort'  => 'Email_email',
                                  )
                            );
    return $headers;
  }

}

?>