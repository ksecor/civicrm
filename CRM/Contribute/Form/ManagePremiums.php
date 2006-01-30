<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form.php';

/**
 * This class generates form components for Premiums
 * 
 */
class CRM_Contribute_Form_ManagePremiums extends CRM_Contribute_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'name' ) );
        $this->addRule( 'name', ts('Please enter a Product name.'), 'required' );
        $this->addRule( 'name', ts('A Product with this name already exists. Please select another name.'), 'objectExists', array( 'CRM_Contribute_DAO_Product', $this->_id ) );
        $this->add('text', 'sku', ts('SKU'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'sku' ) );

        $this->add('textarea', 'description', ts('Description'), 'rows=3, cols=60' );
        //$this->add('radio', 'image', ts('Get image from my computer'), null ,null);
        $image['image']     = $this->createElement('radio',null, null,ts('Get image from my computer '),'image', null );
        $image['thumbnail'] = $this->createElement('radio',null, null,ts('Display image and thumbnail from these locations:'),'thumbnail', null );
        $image['defalut']   = $this->createElement('radio',null, null,ts('Use default image'),'defalut', null );
        $image['noImage']   = $this->createElement('radio',null, null,ts('Do not display an image'),'noImage', null );
        
        $this->addGroup($image,'image',ts('Image'));
        $this->addRule( 'image', ts('Please enter the value for Image'), 'required' );
        
        $this->add( 'file', 'imageFile',null, 'size=30 maxlength=60', true );
        
        $this->add( 'text', 'price',ts('Market Vlaue'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'price' ), true );
        $this->addRule( 'price', ts('Please enter the Market Value for this product'), 'required' );
        
        $this->add( 'text', 'cost',ts('Actual Cost of Product'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'cost' ), true );
        $this->addRule( 'price', ts('Please enter the Actual Cost of Product'), 'required' );

        $this->add( 'text', 'min_contribution',ts('Minimum Contribution Amount'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'min_contribution' ), true );
        $this->addRule( 'min_contribution', ts('Please enter the Minimum Contribution Amount'), 'required' );

        $this->add('textarea', 'option', ts('Option'), 'rows=3, cols=60' );

        $this->add('select', 'period_type', ts('Period Type'),array(''=>'--Select--','rolling'=> 'Rolling','fixed'=>'Fixed'));
               
        $this->add('text', 'fixed_period_start_day', ts('Fixed Period Start Day'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'fixed_period_start_day' ));    

        
        $this->add('Select', 'duration_unit', ts('Duration Unit'),array(''=>'--Select--','day'=> 'Day','week'=>'Week','month'=>'Month','year'=>'Year'));    
    
        $this->add('text', 'duration_interval', ts('Duration Interval'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'duration_interval' ));
        
        $this->add('Select', 'frequency_unit', ts('Frequency Unit'),array(''=>'--Select--','day'=> 'Day','week'=>'Week','month'=>'Month','year'=>'Year'));    

        $this->add('text', 'frequency_interval', ts('Duration Interval'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'frequency_interval' ));
       
    
        $this->add('checkbox', 'is_active', ts('Enabled?'));

             
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Contribute/BAO/ContributionType.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Contribute_BAO_ContributionType::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected contribution type has been deleted.') );
        } else { 

            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();
            
            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['contributionType'] = $this->_id;
            }
            
            $contributionType = CRM_Contribute_BAO_ContributionType::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The contribution type "%1" has been saved.', array( 1 => $contributionType->name )) );
        }
    }
}

?>
