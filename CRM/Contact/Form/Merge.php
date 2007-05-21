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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Dedupe/Merger.php';
require_once 'api/Location.php';

class CRM_Contact_Form_Merge extends CRM_Core_Form
{
    var $_cid         = null;
    var $_oid         = null;
    var $_contactType = null;

    // FIXME: QuickForm can't create advcheckboxes with value set to 0 or '0' :(
    // see HTML_QuickForm_advcheckbox::setValues() - but patching that doesn't 
    // help, as QF doesn't put the 0-value elements in exportValues() anyway...
    var $_qfZeroBug   = 'QuickFormCantMakeAdvcheckboxesWithZeroValue';

    function preProcess()
    {
        require_once 'api/Contact.php';
        require_once 'api/Search.php';
        require_once 'CRM/Core/OptionGroup.php';
        require_once 'CRM/Core/OptionValue.php';
        $cid   = CRM_Utils_Request::retrieve('cid', 'Positive', $this, false);
        $oid   = CRM_Utils_Request::retrieve('oid', 'Positive', $this, false);
        $diffs = CRM_Dedupe_Merger::findDifferences($cid, $oid);
        $main  =& crm_get_contact(array('contact_id' => $cid));
        $other =& crm_get_contact(array('contact_id' => $oid));
        $this->assign('contact_type', $main->contact_type);
        $this->assign('main_name',    $main->display_name);
        $this->assign('other_name',   $other->display_name);
        $this->assign('main_cid',     $main->contact_id);
        $this->assign('other_cid',    $other->contact_id);

        $this->_cid         = $cid;
        $this->_oid         = $oid;
        $this->_contactType = $main->contact_type;

        // FIXME: there must be a better way
        $names = array('preferred_communication_method' => array('newName'   => 'preferred_communication_method_display',
                                                                 'groupName' => 'preferred_communication_method'),
                       'gender_id'                      => array('newName'   => 'gender_id_display',
                                                                 'groupName' => 'gender'),
                       'prefix_id'                      => array('newName'   => 'prefix_id_display',
                                                                 'groupName' => 'individual_prefix'),
                       'suffix_id'                      => array('newName'   => 'suffix_id_display',
                                                                 'groupName' => 'individual_suffix'),
        );
        foreach (array('main', 'other') as $moniker) {
            $specialValues[$moniker] = array('preferred_communication_method' => $$moniker->preferred_communication_method,
                                             'gender_id'                      => $$moniker->contact_type_object->gender_id,
                                             'prefix_id'                      => $$moniker->contact_type_object->prefix_id,
                                             'suffix_id'                      => $$moniker->contact_type_object->suffix_id);
            CRM_Core_OptionGroup::lookupValues($specialValues[$moniker], $names);
        }

        foreach (array($main->contact_type, 'Contact') as $ct) {
            require_once "CRM/Contact/DAO/$ct.php";
            eval("\$fields =& CRM_Contact_DAO_$ct::fields();");
            if ($ct == 'Contact') {
                // FIXME: civcrm_contact.source is not being given title, 
                // because it has a <uniqueName>contact_source</uniqueName>
                // - bug in fields() to return uniqueName instead of name?
                $fields['source']['title'] = $fields['contact_source']['title'];
            } else {
                // FIXME: there must be a better way
                $ovFields = CRM_Core_OptionValue::getFields();
                $fields['gender_id']['title'] = $ovFields['gender']['title'];
                $fields['prefix_id']['title'] = $ovFields['individual_prefix']['title'];
                $fields['suffix_id']['title'] = $ovFields['individual_suffix']['title'];
            }

            if (!isset($diffs[$ct])) $diffs[$ct] = array();
            foreach ($diffs[$ct] as $field) {
                foreach (array('main', 'other') as $moniker) {
                    $value = isset($$moniker->$field) ? $$moniker->$field : $$moniker->contact_type_object->$field;
                    $label = isset($specialValues[$moniker][$field]) ? $specialValues[$moniker]["{$field}_display"] : $value;
                    if ($fields[$field]['type'] == CRM_Utils_Type::T_DATE) {
                        $value = str_replace('-', '', $value);
                        $label = CRM_Utils_Date::customFormat($label);
                    } elseif ($fields[$field]['type'] == CRM_Utils_Type::T_BOOLEAN) {
                        if ($label === '0') $label = ts('No');
                        if ($label === '1') $label = ts('Yes');
                    }
                    $rows[$field][$moniker] = $label;
                    if ($moniker == 'other') {
                        if ($value === 0 or $value === '0') $value = $this->_qfZeroBug;
                        $this->addElement('advcheckbox', $field, null, null, null, $value);
                    }
                }
                $rows[$field]['title'] = $fields[$field]['title'];
            }
        }

        // handle locations
        foreach (CRM_Core_PseudoConstant::locationType() as $locTypeId => $locTypeName) {
            foreach (array('main', 'other') as $moniker) {
                $locations[$locTypeName][$moniker] = crm_get_locations($$moniker, array($locTypeName));
                if (empty($locations[$locTypeName][$moniker])) {
                    $locValue[$moniker] = 0;
                    $locLabel[$moniker] = '[' . ts('EMPTY') . ']';
                } else {
                    $locValue[$moniker] = $locations[$locTypeName][$moniker][0]->id;
                    $locLabel[$moniker] = $locations[$locTypeName][$moniker][0]->name . "\n";
                    foreach ($locations[$locTypeName][$moniker][0]->email as $email) {
                        $locLabel[$moniker] .= $email->email . "\n";
                    }
                    foreach ($locations[$locTypeName][$moniker][0]->phone as $phone) {
                        $locLabel[$moniker] .= $phone->phone . "\n";
                    }
                    $locLabel[$moniker] .= $locations[$locTypeName][$moniker][0]->address->display;
                    // drop consecutive newlines and convert the rest to <br />s
                    $locLabel[$moniker] = preg_replace('/\n+/', "\n", $locLabel[$moniker]);
                    $locLabel[$moniker] = nl2br(trim($locLabel[$moniker]));
                }
            }
            if (!empty($locations[$locTypeName]['main']) or !empty($locations[$locTypeName]['other'])) {
                $rows["location_$locTypeId"]['main']  = $locLabel['main'];
                $rows["location_$locTypeId"]['other'] = $locLabel['other'];
                $rows["location_$locTypeId"]['title'] = ts('Location: %1', array(1 => $locTypeName));
                $this->addElement('advcheckbox', "location_$locTypeId", null, null, null, $locValue['other']);
            }
        }

        // handle custom fields
        if (!isset($diffs['custom'])) $diffs['custom'] = array();
        foreach (array('main', 'other') as $moniker) {
            $contact =& $$moniker;
            foreach ($contact->custom_values as $cv) {
                if (in_array($cv['custom_field_id'], $diffs['custom'])) {
                    $customValues[$moniker][$cv['custom_field_id']] = $cv['value'];
                    $customLabels[$moniker][$cv['custom_field_id']] = CRM_Core_BAO_CustomOption::getOptionLabel($id, $cv['value']);
                }
            }
        }
        foreach ($diffs['custom'] as $id) {
            $rows["custom_$id"]['main']  = $customLabels['main'][$id];
            $rows["custom_$id"]['other'] = $customLabels['other'][$id];
            $rows["custom_$id"]['title'] = CRM_Core_BAO_CustomField::getTitle($id);
            $this->addElement('advcheckbox', "custom_$id", null, null, null, $customValues['other'][$id]);
        }

        $this->assign('rows', $rows);

        // add the related tables and unset the ones that don't sport any of the duplicate contact's info
        $relTables = CRM_Dedupe_Merger::relTables();
        $activeRelTables = CRM_Dedupe_Merger::getActiveRelTables($oid);
        foreach ($relTables as $name => $null) {
            if (!in_array($name, $activeRelTables)) {
                unset($relTables[$name]);
                continue;
            }
            $this->addElement('checkbox', $name);
            $relTables[$name]['main_url']  = str_replace('$cid', $cid, $relTables[$name]['url']);
            $relTables[$name]['other_url'] = str_replace('$cid', $oid, $relTables[$name]['url']);
        }
        $this->assign('rel_tables', $relTables);

        // add the 'move belongings?' and 'delete other?' elements
        $this->addElement('checkbox', 'moveBelongings', ts("Move other information associated with the Duplicate Contact to the Main Contact"));
        $this->addElement('hidden', 'deleteOther', 1);
        // alternatively, make the 'deleteOther' a visible checkbox - also uncomment the proper <p> in the template
        // $this->addElement('checkbox', 'deleteOther', ts('Delete the lleft-side ceft-side contact after merging'));
    }
    
    function setDefaultValues()
    {
        return array('deleteOther' => 1);
    }
    
    function addRules()
    {
    }

    public function buildQuickForm()
    {
        $this->addButtons(array(
            array('type' => 'next',   'name' => ts('Merge'), 'isDefault' => true),
            array('type' => 'cancel', 'name' => ts('Cancel')),
        ));
    }

    public function postProcess()
    {
        $formValues = $this->exportValues();

        // get submitted contact values and clear them
        $validFields = array_merge(CRM_Dedupe_Merger::$validFields['Contact'], CRM_Dedupe_Merger::$validFields[$this->_contactType]);
        $relTables =& CRM_Dedupe_Merger::relTables();
        $moveTables = array();
        foreach ($formValues as $key => $value) {
            if ($value == $this->_qfZeroBug) $value = '0';
            if ((in_array($key, $validFields) or substr($key, 0, 7) == 'custom_') and $value != null) {
                $submitted[$key] = $value;
            } elseif (substr($key, 0, 9) == 'location_' and $value != null) {
                $locations[substr($key, 9)] = $value;
            } elseif (substr($key, 0, 10) == 'rel_table_' and $value == '1') {
                $moveTables = array_merge($moveTables, $relTables[$key]['tables']);
            }
        }
        // FIXME: source vs. contact_source workaround
        if (isset($submitted['source'])) {
            $submitted['contact_source'] = $submitted['source'];
            unset($submitted['source']);
        }
        // FIXME: crm_update_contact() cannot eat preferred_communication_method generated by crm_get_contact()
        if (isset($submitted['preferred_communication_method'])) {
            $pcm =& $submitted['preferred_communication_method'];
            $pcm = trim($pcm, CRM_Core_BAO_CustomOption::VALUE_SEPERATOR);
            $pcm = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $pcm);
            $pcm = array_flip($pcm);
        }

        $main =& crm_get_contact(array('contact_id' => $this->_cid));

        if (isset($submitted)) {
            crm_update_contact($main, $submitted);
        }

        // FIXME: the simplest approach to locations
        $locTypes =& CRM_Core_PseudoConstant::locationType();
        if (!isset($locations)) $locations = array();
        foreach ($locations as $locTypeId => $locId) {
            $mainLocation = crm_get_locations($main, array($locTypes[$locTypeId]));
            // if we stay with the same location, skip it
            // otherwise, delete the old location
            if (isset($mainLocation[0]) and $locId == $mainLocation[0]->id) {
                continue;
            } elseif (isset($mainLocation[0])) {
                crm_delete_location($main, $mainLocation[0]->id);
            }
            // if the new one is 0, we're done
            if ($locId == 0) {
                continue;
            }
            // otherwise, move the new one to the
            // main contact (preserving its primariness)
            require_once 'CRM/Core/DAO/Location.php';
            $locDAO =& new CRM_Core_DAO_Location();
            $locDAO->id = $locId;
            $locDAO->fetch();
            $locDAO->entity_id = $this->_cid;
            $locDAO->is_primary = $mainLocation[0]->is_primary;
            $locDAO->save();
        }

        // handle the related tables
        if (isset($moveTables)) {
            CRM_Dedupe_Merger::moveContactBelongings($this->_cid, $this->_oid, $moveTables);
        }

        // handle the 'move belongings' and 'delete other' checkboxes
        if ($formValues['moveBelongings']) {
            CRM_Dedupe_Merger::moveContactBelongings($this->_cid, $this->_oid);
        }
        if ($formValues['deleteOther']) {
            $other =& crm_get_contact(array('contact_id' => $this->_oid));
            crm_delete_contact($other);
        }
    }
}

?>
