<?php

function com_install() {
    global $database;

    global $mosConfig_absolute_path;
    $path = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'administrator'          . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR .
        'com_civicrm'            . DIRECTORY_SEPARATOR ;

    require_once $path . 'civicrm.php';

    civicrm_main( );
}

# Show installation result to user

?>
<center>
<table width="100%" border="0">
  <tr>
    <td>
      <strong>Install Successful</strong><br/>
      <br/>
     CiviCRM has been successfully installed.
    </td>
  </tr>
  <tr>
    <td>
      <code>Installation: <font color="green">Succesful</font></code>
    </td>
  </tr>
</table>
</center>
