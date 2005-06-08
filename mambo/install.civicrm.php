<?
function com_install() {
global $database;

// these queries manually set the correct icons for given menu items
# Set up new icons for admin menu
/*
$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/edit.png' WHERE admin_menu_link='option=com_email_form&task=view'");
$iconresult[0] = $database->query();
$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/config.png' WHERE admin_menu_link='option=com_email_form&task=config'");
$iconresult[1] = $database->query();
$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/user.png' WHERE admin_menu_link='option=com_email_form&task=language'");
$iconresult[2] = $database->query();
$database->setQuery("UPDATE #__components SET admin_menu_img='js/ThemeOffice/credits.png' WHERE admin_menu_link='option=com_email_form&task=about'");
$iconresult[3] = $database->query();*/

# clear link on top-level component menu item
# (Ryan does not like the top-level component menu item to be a link)
/*$database->setQuery("UPDATE #__components SET admin_menu_link='' where name='Email Form'");
$database->query();*/

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
<?
}
?>