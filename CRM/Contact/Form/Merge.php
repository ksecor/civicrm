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
require_once 'api/Location.php';

class CRM_Contact_Form_Merge extends CRM_Core_Form
{
    var $_defaults = array();

    var $_cid         = null;
    var $_contactType = null;

    // an ugly hack to be able to cleanly address the radios in Smarty
    var $_col = 'column';

    function preProcess()
    {
        require_once 'api/Contact.php';
        require_once 'api/Search.php';
        require_once 'CRM/Core/OptionGroup.php';
        require_once 'CRM/Core/OptionValue.php';
        require_once 'CRM/Dedupe/Merger.php';
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
                $rows[] = $field;
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
                    $group[$moniker] = HTML_QuickForm::createElement('radio', $this->_col, null, $label, $value);
                    if ($moniker == 'main') $this->_defaults[$field] = $value;
                }
                $this->addGroup($group, $field, $fields[$field]['title']);
            }
        }

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
            $rows[] = "custom_$id";
            $this->_defaults["custom_$id"] = $customValues['main'][$id];
            $group['main']  = HTML_QuickForm::createElement('radio', $this->_col, null, $customLabels['main'][$id],  $customValues['main'][$id]);
            $group['other'] = HTML_QuickForm::createElement('radio', $this->_col, null, $customLabels['other'][$id], $customValues['other'][$id]);
            $this->addGroup($group, "custom_$id", CRM_Core_BAO_CustomField::getTitle($id));
        }

        foreach (CRM_Core_PseudoConstant::locationType() as $locTypeId => $locTypeName) {
            foreach (array('main', 'other') as $moniker) {
                $locations[$locTypeName][$moniker] = crm_get_locations($$moniker, array($locTypeName));
                if (empty($locations[$locTypeName][$moniker])) {
                    $locValue[$moniker] = 0;
                    $locLabel[$moniker] = ts('[DELETE]');
                } else {
                    $locValue[$moniker] = $locations[$locTypeName][$moniker][0]->id;
                    $locLabel[$moniker] = $locations[$locTypeName][$moniker][0]->name . '<br />'
                                        . $locations[$locTypeName][$moniker][0]->email[1]->email . '<br />'
                                        . nl2br($locations[$locTypeName][$moniker][0]->address->display);
                }
            }
            if (!empty($locations[$locTypeName]['main']) or !empty($locations[$locTypeName]['other'])) {
                $rows[] = "location_$locTypeId";
                $this->_defaults["location_$locTypeId"] = $locValue['main'];
                $group['main']  = HTML_QuickForm::createElement('radio', $this->_col, null, $locLabel['main'],  $locValue['main']);
                $group['other'] = HTML_QuickForm::createElement('radio', $this->_col, null, $locLabel['other'], $locValue['other']);
                $this->addGroup($group, "location_$locTypeId", ts('Location: %1', array(1 => $locTypeName)));
            }
        }

        $this->assign('rows', $rows);

        // make defaults compatible with the ugly _col hack
        foreach ($this->_defaults as $key => $value) $this->_defaults["{$key}[{$this->_col}]"] = $value;
    }
    
    function setDefaultValues()
    {
        return $this->_defaults;
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

        // get submitted contact values, unhack them and clear
        $validFields = array_merge(CRM_Dedupe_Merger::$validFields['Contact'], CRM_Dedupe_Merger::$validFields[$this->_contactType]);
        foreach ($formValues as $key => $value) {
            if ((in_array($key, $validFields) and array_key_exists($this->_col, $value)) or substr($key, 0, 7) == 'custom_') {
                $submitted[$key] = $value[$this->_col];
            } elseif (substr($key, 0, 9) == 'location_') {
                $locations[substr($key, 9)] = $value[$this->_col];
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
        foreach ($locations as $locTypeId => $locId) {
            $mainLocation = crm_get_locations($main, array($locTypes[$locTypeId]));
            // if we stay with the same location, skip it
            if ($locId == $mainLocation[0]->id) {
                continue;
            }
            // delete the old location
            crm_delete_location($main, $mainLocation[0]->id);
            // if the new one is 0, we're done
            if ($locId == 0) {
                continue;
            }
            // otherwise, move the new one to the
            // main contact (preserving primariness)
            require_once 'CRM/Core/DAO/Location.php';
            $locDAO =& new CRM_Core_DAO_Location();
            $locDAO->id = $locId;
            $locDAO->fetch();
            $locDAO->entity_id = $this->_cid;
            $locDAO->is_primary = $mainLocation[0]->is_primary;
            $locDAO->save();
        }
    }
}

?>
