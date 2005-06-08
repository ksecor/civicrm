<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

# start the session with the mossession ID (or else it won't work!)
#session_start($mainframe->_session['session_id']);

switch($task)
{
default:
echo "You could put something here in case you want to allow
end users to access the component for some reason. For example, to 
sign up,
or edit their own data.<br /><br />
Works just like the admin component in terms of style/code.";
break;
}

?>