<?php

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// this is the convention for toolbars:
#require_once( $mainframe->getPath( 'toolbar_html' ) );
#require_once( $mainframe->getPath( 'toolbar_default' ) );
// it includes the "toolbar.html.php" file and the default toolbar set

// but we can condense it into a single file to save time:

// look familiar? Joomla components are little else but switches
switch ($task) {
	// again, the same task as before...
	case 'view_contacts':
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
    break;
}
?>
