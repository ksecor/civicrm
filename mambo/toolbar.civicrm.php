<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// this is the convention for toolbars:
#require_once( $mainframe->getPath( 'toolbar_html' ) );
#require_once( $mainframe->getPath( 'toolbar_default' ) );
// it includes the "toolbar.html.php" file and the default toolbar set

// but we can condense it into a single file to save time:

// look familiar? Mambo components are little else but switches
switch ($task) {
	// again, the same task as before...
	case 'view_contacts':
		mosMenuBar::startTable();
		// as you can see, 2 arguments -- the name of the $task it calls and
		// the display text next to the button
		mosMenuBar::publish( 'publish_contact', 'Publish' );
		mosMenuBar::publish( 'unpublish_contact', 'Unpublish' );
		mosMenuBar::addNew( 'new_contact', 'Add Issue' );
		mosMenuBar::editList('edit_contact', 'Edit Issue');
		mosMenuBar::deleteList('','remove_contact', 'Delete Issue' );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
    break;
}
?>
