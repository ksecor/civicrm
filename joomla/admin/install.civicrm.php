<?php

defined('_JEXEC') or die('No direct access allowed'); 

function com_install() {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configure.php';
    global $civicrmUpgrade;
    
    $liveSite = substr_replace(JURI::root(), '', -1, 1);
    $configTaskUrl = $liveSite . '/administrator/index2.php?option=com_civicrm&task=civicrm/admin/configtask&reset=1';
    $upgradeUrl = $liveSite . '/administrator/index2.php?option=com_civicrm&task=civicrm/upgrade&reset=1';

    if ( $civicrmUpgrade ) {
        // UPGRADE successful status and links
        $content = '
  <center>
  <table width="100%" border="0">
    <tr>
        <td>
            <strong>CiviCRM component files have been UPGRADED <font color="green">succesfully</font></strong>.
            <p><strong>Please run the <a href="' . $upgradeUrl . '">CiviCRM Database Upgrade Utility</a> now. This utility will check your database and perform any needed upgrades.</strong></p>
            <p>Also review the <a target="_blank" href="http://wiki.civicrm.org/confluence/display/CRMDOC/Upgrade+Joomla+Sites+to+2.2">upgrade documentation</a> for any additional steps required to complete this upgrade.</p>
        </td>
    </tr>
  </table>
  </center>';

    } else {
    // INSTALL successful status and links
        $content = '
  <center>
  <table width="100%" border="0">
    <tr>
        <td>
            <strong>CiviCRM component files and database tables have been INSTALLED <font color="green">succesfully</font></strong>.
            <p><strong>Please review the <a target="_blank" href="http://wiki.civicrm.org/confluence/display/CRMDOC/Install+2.2+for+Joomla">online installation documentation</a> for any additional steps required to complete the installation.</strong></p>
            <p><strong>Then use the <a href="' . $configTaskUrl . '">Configuration Checklist</a> to review and configure CiviCRM settings for your new site.</strong></p>
            <p><strong>Additional Resources:</strong>
                <ul><li><a target="_blank" href="http://wiki.civicrm.org/confluence/display/CRMDOC/Configuring+Front-end+Profile+Listings+and+Forms+in+Joomla!+Sites">Create front-end forms and searchable directories using Profiles</a>.</li>
                    <li><a target="_blank" href="http://wiki.civicrm.org/confluence/display/CRMDOC/Displaying+Online+Contribution+Pages+in+Joomla!+Frontend+Sites">Create online contribution pages</a></li>
                    <li><a target="_blank" href="http://wiki.civicrm.org/confluence/display/CRMDOC/Configuring+Front-end+Event+Info+and+Registration+in+Joomla!+Sites">Create events with online event registration</a>.</li>
            </p>
        </td>
    </tr>
  </table>
  </center>';
    }
    
    echo $content;
}