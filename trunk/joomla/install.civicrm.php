<?php

defined('_JEXEC') or die('No direct access allowed'); 

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
        <p>CiviCRM includes the ability to expose Profile forms and listings, as well as Online Contribution and Event Registration pages, to users and visitors of the 'front-end' of your Joomla! site.
           Review <a href="http://wiki.civicrm.org/confluence//x/6Bk">this document to learn about configuring Profiles as custom front-end forms and search pages</a>. Review
           <a href="http://wiki.civicrm.org/confluence/x/QBo">this document to learn about configuring Online Contribution Pages</a>, and review <a href="http://wiki.civicrm.org/confluence/x/ezg">this document
            to learn about Event Registration pages</a>.</p></td>
    </tr>
  </table>
</center>

<?php
}

