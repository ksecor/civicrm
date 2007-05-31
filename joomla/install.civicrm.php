<?php

function com_install() {
    global $database;

    global $mosConfig_absolute_path;
    $path = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'administrator'          . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR .
        'com_civicrm'            . DIRECTORY_SEPARATOR ;

    // this require actually runs the function needed
    // bad code, but easier to debug on remote machines
    require_once $path . 'configure.php';
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
        <p><code>Installation: <font color="green">Succesful</font></code></p>
        <p>If this is a <strong>new installation</strong> of CiviCRM, please review the
            <a href="http://wiki.civicrm.org/confluence//x/ixI">online installation
            documentation</a>.
        </p>
        <p>If you are <strong>upgrading</strong> an existing installation of CiviCRM, please review the
            <a href="http://wiki.civicrm.org/confluence//x/lxs">upgrade
            documentation</a>.
        </p>
        <p>CiviCRM includes the ability to expose Profile forms and listings, as well as
         Online Contribution Pages, to users and visitors of the 'front-end' of your Joomla!
         site. If you wish to use this option, you will need to follow the
         <a href="http://wiki.civicrm.org/confluence//x/6Bk">instructions for
         modifying your Joomla! installation</a>.
        </p>
    </td>
  </tr>
</table>
</center>
