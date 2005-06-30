<?php

require_once '/home/yvb/svn/crm/modules/config.inc.php';
require_once 'CRM/Core/Form.php';
require_once 'CRM/Utils/String.php';
require_once 'CRM/Utils/System.php';
require_once 'packages/HTML/QuickForm/Controller.php';
require_once 'CRM/Core/I18n.php';
require_once 'Smarty/Smarty.class.php';

class ShowHide1 extends CRM_Core_Form
{
    /**
     * Function to build the form
     *
     * @param none
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        for ($i=1; $i<=5; $i++) {
            CRM_Core_ShowHideBlocks::linksForArray($this, $i, 5, "field", 'another field', 'hide this field');
            $this->addElement('text', "field[$i]", "Field $i");
        }
    }
}


$smarty =& new Smarty( );
$smarty->template_dir = './templates';
$smarty->compile_dir  = '/tmp/templates_c';

$showhide   = new ShowHide1(); 
$showhide->accept($smarty);

$controller = new HTML_QuickForm_Controller('ShowHide1');
$controller->addPage($showhide);
$controller->run();

?>

