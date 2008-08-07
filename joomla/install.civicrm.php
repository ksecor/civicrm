<?php

function com_install() {
    if ( ! file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'civicrm.settings.php' ) ) {
        // this require actually runs the function needed
        // bad code, but easier to debug on remote machines
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configure.php';
    }

    // Show installation result to user
?>

<center>
  <table width="100%" border="0">
    <tr>
      <td><strong>Files uploaded <font color="green">succesfully</font></strong><br/>
      </td>
    </tr>
    <tr>
      <td><p>If this is a <strong>new installation</strong> of CiviCRM, please review the <a href="http://wiki.civicrm.org/confluence/display/CRMDOC/Install+2.1+for+Joomla">online installation documentation</a>. </p>
        <p>If you are <strong>upgrading</strong> an existing installation of CiviCRM, please review the <a href="http://wiki.civicrm.org/confluence/display/CRMDOC/Upgrade+Joomla+Sites+to+2.1">upgrade documentation</a>. </p>
        <p>CiviCRM includes the ability to expose Profile forms and listings, as well as
          Online Contribution Pages, to users and visitors of the 'front-end' of your Joomla!
          site. If you wish to use this option, you will need to follow the <a href="http://wiki.civicrm.org/confluence//x/6Bk">instructions for
          modifying your Joomla! installation</a>. </p></td>
    </tr>
  </table>
</center>

<?php
}

