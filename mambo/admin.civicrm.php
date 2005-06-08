<?php

// prevents direct access
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


include_once 'config.inc.php';

require_once 'PEAR.php';

require_once 'CRM/Core/Action.php';
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Block.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Core/Error.php';

require_once 'CRM/Utils/Wrapper.php';

require_once 'CRM/Contact/Page/View.php';

require_once 'CRM/Group/Page/Group.php';

// insert any functions/includes etc. that apply to all "tasks"
// HERE

switch ($task) {
	/** Sample Task **/
	case 'civicrm/contacts/view':
	default:
        civicrm_invoke( );
		break;
}

// want to use wrapper functions instead of direct calls?
// this is probably overkill, but might be considered a good
// abstraction. this would be the place to put them.

function civicrm_init( ) {
    $config =& CRM_Core_Config::singleton();
    CRM_Core_DAO::init($config->dsn, $config->daoDebug);

    $factoryClass = 'CRM_Contact_DAO_Factory';

    CRM_Core_DAO::setFactory(new $factoryClass());

    // set error handling
    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('CRM_Core_Error', 'handle'));
}


function civicrm_invoke( ) {
    civicrm_init( );

    $view =& new CRM_Group_Page_Group(ts('View Groups'));
    $view->run();
}

?>