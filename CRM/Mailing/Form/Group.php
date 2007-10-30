<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

/**
 * Choose include / exclude groups and mailings
 *
 */
class CRM_Mailing_Form_Group extends CRM_Core_Form 
{

    /**
     * The number of groups / mailings we will process
     */
    const NUMBER_OF_ELEMENTS = 5;

    /**
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $mailingID = $this->get("mid");

        // check that the user has permission to access mailing id
        require_once 'CRM/Mailing/BAO/Mailing.php';
        CRM_Mailing_BAO_Mailing::checkPermission( $mailingID );

        $defaults = array( );
        if ( $mailingID ) {
            $defaults["name"] = ts('Copy of %1', array(1 => CRM_Core_DAO::getFieldValue('CRM_Mailing_DAO_Mailing', $mailingID, 'name', 'id')));

            require_once "CRM/Mailing/DAO/Group.php";
            $dao =&new  CRM_Mailing_DAO_Group();
            
            $mailingGroups = array();
            $dao->mailing_id = $mailingID;
            $dao->find();
            while ( $dao->fetch() ) {
                $mailingGroups[$dao->entity_table][$dao->group_type][] = $dao->entity_id;
            }
            
            $defaults['includeGroups'] = $mailingGroups['civicrm_group']['Include'];
            $defaults['excludeGroups'] = CRM_Utils_Array::value('Exclude',$mailingGroups['civicrm_group']);

            $defaults['includeMailings'] = CRM_Utils_Array::value('Include',$mailingGroups['civicrm_mailing']);
            $defaults['excludeMailings'] = $mailingGroups['civicrm_mailing']['Exclude'];
        }

        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        require_once 'CRM/Mailing/PseudoConstant.php';

        $this->add( 'text', 'name', ts('Name Your Mailing'),
                    CRM_Core_DAO::getAttribute( 'CRM_Mailing_DAO_Mailing', 'name' ),
                    true );

        $groups         =& CRM_Core_PseudoConstant::group('Mailing');
        $groupIterator  =& CRM_Core_PseudoConstant::groupIterator( true );
        require_once 'CRM/Core/QuickForm/GroupMultiSelect.php';
        $inGroupsSelect =& new CRM_Core_QuickForm_GroupMultiSelect( 'includeGroups',
        ts('Include Group(s)') . ' ', $groupIterator,
        array( 'size'  => 5,
               'style' => 'width:240px',
               'class' => 'advmultiselect' )
        );
        $inG =& $this->addElement( $inGroupsSelect );
        $this->addRule( 'includeGroups', ts('Please select a group to be mailed.'), 'required' );
        
        $outGroupsSelect =& new CRM_Core_QuickForm_GroupMultiSelect( 'excludeGroups',
        ts('Exclude Group(s)') . ' ', $groupIterator,
        array( 'size'  => 5,
               'style' => 'width:240px',
               'class' => 'advmultiselect' )
        );
        $outG =& $this->addElement($outGroupsSelect);

        $inG->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $outG->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $inG->setButtonAttributes('remove', array('value' => ts('<< Remove')));;
        $outG->setButtonAttributes('remove', array('value' => ts('<< Remove')));;
        
        $mailings =& CRM_Mailing_PseudoConstant::completed();
        if (! $mailings) {
            $mailings = array();
        }
        $inM =& $this->addElement('advmultiselect', 'includeMailings', 
                                  ts('INCLUDE Recipients of These Mailing(s)') . ' ', $mailings,
                                  array('size' => 5,
                                        'style' => 'width:240px',
                                        'class' => 'advmultiselect')
                                  );
        $outM =& $this->addElement('advmultiselect', 'excludeMailings', 
                                   ts('EXCLUDE Recipients of These Mailing(s)') . ' ', $mailings,
                                   array('size' => 5,
                                         'style' => 'width:240px',
                                         'class' => 'advmultiselect')
                                   );
        
        $inM->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $outM->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $inM->setButtonAttributes('remove', array('value' => ts('<< Remove')));;
        $outM->setButtonAttributes('remove', array('value' => ts('<< Remove')));;
        
        $this->addFormRule( array( 'CRM_Mailing_Form_Group', 'formRule' ));
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Next >>'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
        $this->assign('groupCount', count($groups));
        $this->assign('mailingCount', count($mailings));
    }

    public function postProcess() 
    {
        $params['name'] = $this->controller->exportValue($this->_name, 'name');
        $this->set('name', $params['name']);

        $inGroups = $this->controller->exportValue($this->_name, 'includeGroups');
        $outGroups = $this->controller->exportValue($this->_name, 'excludeGroups');
        $inMailings = $this->controller->exportValue($this->_name, 'includeMailings');
        $outMailings = $this->controller->exportValue($this->_name, 'excludeMailings');
        $groups = array();
        if (is_array($inGroups)) {
            foreach($inGroups as $key => $id) {
                if ($id) {
                    $groups['include'][] = $id;
                }
            }
        }
        if (is_array($outGroups)) {
            foreach($outGroups as $key => $id) {
                if ($id) {
                    $groups['exclude'][] = $id;
                }
            }
        }

        $mailings = array();
        if (is_array($inMailings)) {
            foreach($inMailings as $key => $id) {
                if ($id) {
                    $mailings['include'][] = $id;
                }
            }
        }
        if (is_array($outMailings)) {
            foreach($outMailings as $key => $id) {
                if ($id) {
                    $mailings['exclude'][] = $id;
                }
            }
        }
        
        $daoComponent =& new CRM_Mailing_DAO_Component();
        $components = array('Reply', 'OptOut', 'Unsubscribe');
        
        foreach ($components as $key => $value) {
            $findDefaultComponent =
                "SELECT id
                FROM    civicrm_mailing_component
                WHERE   component_type = '$value'
                ORDER BY is_default desc";
            
            $daoComponent->query($findDefaultComponent);
            
            if ( $daoComponent->fetch( ) ) {
                $$value = $daoComponent->id;
            }
        }
        
        $params['reply_id'] = $Reply;
        $params['optout_id'] = $OptOut;
        $params['unsubscribe_id'] = $Unsubscribe;
        $session =& CRM_Core_Session::singleton();
        $params['domain_id']  = $session->get('domainID');
        $params['groups']         = $groups;
        $params['mailings']       = $mailings;
        
        if ( $this->get('mailing_id') ) {
            $ids = array();
            // don't create a new mailing if already exists
            $ids['mailing_id']    = $this->get('mailing_id');
            
            // delete previous includes/excludes, if mailing already existed
            require_once 'CRM/Contact/DAO/Group.php';
            foreach( array( 'groups', 'mailings' ) as $entity ) {
                $mg =& new CRM_Mailing_DAO_Group();
                $mg->mailing_id     = $ids['mailing_id'];                        
                $mg->entity_table   = ( $entity == 'groups' ) 
                    ? CRM_Contact_BAO_Group::getTableName( )
                    : CRM_Mailing_BAO_Mailing::getTableName( );
                $mg->find();
                while ( $mg->fetch() ) {
                    $mg->delete( );
                }
            }
        }

        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing = CRM_Mailing_BAO_Mailing::create($params, $ids);
        $this->set('mailing_id', $mailing->id);
        
        require_once "CRM/Mailing/BAO/Mailing.php";
        $count = CRM_Mailing_BAO_Mailing::getRecipientsCount(true, false, $mailing->id);
        $this->set('count',$count );
        $this->assign('count',$count );
        $this->set('groups', $groups);
        $this->set('mailings', $mailings);
    }
    
    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) 
    {
        return ts( 'Select Recipients' );
    }

    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$fields ) 
    {
        $errors = array( );
        if ( isset( $fields['includeGroups'] )    &&
             is_array( $fields['includeGroups'] ) &&
             isset( $fields['excludeGroups'] )    &&
             is_array( $fields['excludeGroups'] ) ) {
            $checkGroups = array();
            $checkGroups = array_intersect($fields['includeGroups'], $fields['excludeGroups']);
            if (!empty($checkGroups)) {
                $errors['excludeGroups'] = ts('Cannot have same groups in Include Group(s) and Exclude Group(s).');
            }
        }

        if ( isset( $fields['includeMailings'] )    &&
             is_array( $fields['includeMailings'] ) &&
             isset( $fields['excludeMailings'] )    &&
             is_array( $fields['excludeMailings'] ) ) {
            $checkMailings = array();
            $checkMailings = array_intersect($fields['includeMailings'], $fields['excludeMailings']);
            if (!empty($checkMailings)) {
                $errors['excludeMailings'] = ts('Cannot have same mail in Include mailing(s) and Exclude mailing(s).');
            }
        }
        
        return empty($errors) ? true : $errors;
    }

}

?>
