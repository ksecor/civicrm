<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';
require_once 'CRM/Dedupe/DAO/Rule.php';
require_once 'CRM/Dedupe/DAO/RuleGroup.php';

/**
 * This class generates form components for DupeRules
 * 
 */
class CRM_Admin_Form_DupeRules extends CRM_Admin_Form
{
    protected $_defaults = array();

    /**
     * Function to pre processing
     *
     * @return None
     * @access public
     */
    function preProcess()
    {
        $rgid             = CRM_Utils_Request::retrieve('id', 'Positive', $this, false, 0);
        $rgDao            =& new CRM_Dedupe_DAO_RuleGroup();
        $rgDao->domain_id = CRM_Core_Config::domainID();
        $rgDao->id        = $rgid;
        $rgDao->find(true);
        $this->_defaults['threshold'] = $rgDao->threshold;

        $ruleDao =& new CRM_Dedupe_DAO_Rule();
        $ruleDao->dedupe_rule_group_id = $rgid;
        $ruleDao->find();
        $count = 0;
        while ($ruleDao->fetch()) {
            $this->_defaults["where_$count"]  = "{$ruleDao->rule_table}.{$ruleDao->rule_field}";
            $this->_defaults["length_$count"] = $ruleDao->rule_length;
            $this->_defaults["weight_$count"] = $ruleDao->rule_weight;
        }
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        require_once 'CRM/Contact/BAO/Contact.php';
        $importableFields = CRM_Contact_BAO_Contact::importableFields($rgDao->contact_type);
        $fields = array();
        foreach ($importableFields as $iField) {
            if (isset($iField['where'])) {
                list($table, $column) = explode('.', $iField['where']);
                $fields[] = array('title' => $iField['title'], 'table' => $table, 'column' => $column);
            }
        }

        $this->add('text', 'threshold', ts('Threshold'), null, true);
        $this->addButtons(array(
            array('type' => 'next',   'name' => ts('Save'), 'isDefault' => true),
            array('type' => 'cancel', 'name' => ts('Cancel')),
        ));
        parent::buildQuickForm();
    }

    function setDefaultValues()
    {
        return $this->_defaults;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // FIXME; post process
    }
    
}

?>
