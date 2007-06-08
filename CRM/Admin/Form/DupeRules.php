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
    const RULES_COUNT = 5;
    protected $_defaults = array();
    protected $_fields   = array();
    protected $_rgid;

    /**
     * Function to pre processing
     *
     * @return None
     * @access public
     */
    function preProcess()
    {
        $this->_rgid      = CRM_Utils_Request::retrieve('id', 'Positive', $this, false, 0);
        $rgDao            =& new CRM_Dedupe_DAO_RuleGroup();
        $rgDao->domain_id = CRM_Core_Config::domainID();
        $rgDao->id        = $this->_rgid;
        $rgDao->find(true);
        $this->_defaults['threshold'] = $rgDao->threshold;

        $ruleDao =& new CRM_Dedupe_DAO_Rule();
        $ruleDao->dedupe_rule_group_id = $this->_rgid;
        $ruleDao->find();
        $count = 0;
        while ($ruleDao->fetch()) {
            $this->_defaults["where_$count"]  = "{$ruleDao->rule_table}.{$ruleDao->rule_field}";
            $this->_defaults["length_$count"] = $ruleDao->rule_length;
            $this->_defaults["weight_$count"] = $ruleDao->rule_weight;
            $count++;
        }

        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Dedupe/Criterion.php';
        $importableFields = CRM_Contact_BAO_Contact::importableFields($rgDao->contact_type);
        // FIXME: this is what you end up doing when abusing importableFields()
        $replacements = array(
            'civicrm_country.name'        => 'civicrm_address.country_id',
            'civicrm_county.name'         => 'civicrm_address.county_id',
            'civicrm_state_province.name' => 'civicrm_address.state_province_id',
            'gender.label'                => 'civicrm_individual.gender_id',
            'individual_prefix.label'     => 'civicrm_individual.prefix_id',
            'individual_suffix.label'     => 'civicrm_individual.suffix_id',
        );
        foreach ($importableFields as $iField) {
            if (isset($iField['where'])) {
                $where = $iField['where'];
                if (isset($replacements[$where])) {
                    $where = $replacements[$where];
                }
                $table = array_shift(explode('.', $where));
                if (in_array($table, CRM_Dedupe_Criterion::$supportedTables)) {
                    $this->_fields[$where] = $iField['title'];
                }
            }
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
        for ($count = 0; $count < self::RULES_COUNT; $count++) {
            $this->add('select', "where_$count", ts('Field'), array(null => ts('- none -')) + $this->_fields);
            $this->add('text', "length_$count", ts('Length'));
            $this->add('text', "weight_$count", ts('Weight'));
        }
        $this->add('text', 'threshold', ts("Weight Threshold to Consider Two Contacts 'Matching':"));
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
        $values = $this->exportValues();

        $rgDao            =& new CRM_Dedupe_DAO_RuleGroup();
        $rgDao->domain_id = CRM_Core_Config::domainID();
        $rgDao->id        = $this->_rgid;
        $rgDao->find(true);
        $rgDao->threshold = $values['threshold'];
        $rgDao->save();

        $ruleDao =& new CRM_Dedupe_DAO_Rule();
        $ruleDao->dedupe_rule_group_id = $this->_rgid;
        $ruleDao->delete();
        $ruleDao->free();

        for ($count = 0; $count < self::RULES_COUNT; $count++) {
            list($table, $field) = explode('.', $values["where_$count"]);
            $length = $values["length_$count"];
            $weight = $values["weight_$count"];
            if ($table and $field) {
                $ruleDao =& new CRM_Dedupe_DAO_Rule();
                $ruleDao->dedupe_rule_group_id = $this->_rgid;
                $ruleDao->rule_table           = $table;
                $ruleDao->rule_field           = $field;
                $ruleDao->rule_length          = $length;
                $ruleDao->rule_weight          = $weight;
                $ruleDao->save();
                $ruleDao->free();
            }
        }
    }
    
}

?>
