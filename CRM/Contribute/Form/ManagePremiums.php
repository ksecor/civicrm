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
        //parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'name' ) );
        $this->addRule( 'name', ts('Please enter a Product name.'), 'required' );
        $this->addRule( 'name', ts('A Product with this name already exists. Please select another name.'), 'objectExists', array( 'CRM_Contribute_DAO_Product', $this->_id ) );
        $this->add('text', 'sku', ts('SKU'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'sku' ),true );

        $this->add('textarea', 'description', ts('Description'), 'rows=3, cols=60' );
        //$this->add('radio', 'image', ts('Get image from my computer'), null ,null);
        $image['image']     = $this->createElement('radio',null, null,ts('Get image from my computer '),'image','onClick="add_upload_file_block(\'image\');');
        $image['thumbnail'] = $this->createElement('radio',null, null,ts('Display image and thumbnail from these locations:'),'thumbnail', 'onClick="add_upload_file_block(\'thumbnail\');');
        $image['defalut']   = $this->createElement('radio',null, null,ts('Use default image'),'defalut', 'onClick="add_upload_file_block(\'default\');');
        $image['noImage']   = $this->createElement('radio',null, null,ts('Do not display an image'),'noImage','onClick="add_upload_file_block(\'noImage\');');

        $image['current']   = $this->createElement('radio',null, null,ts('Use current image'),'current','onClick="add_upload_file_block(\'current\');');
        
        $this->addGroup($image,'image',ts('Image'));
        $this->addRule( 'image', ts('Please enter the value for Image'), 'required' );
        
        $this->addElement( 'file', 'imageUrl',ts('Image URL'), 'size=30 maxlength=60');
        $this->addElement( 'file', 'thumbnailUrl',ts('Thumbnail URL'), 'size=30 maxlength=60');
       
        

        $this->addElement( 'file','uploadFile',ts('Image File Name'), 'size=30 maxlength=60');

               
        $this->add( 'text', 'price',ts('Market Vlaue'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'price' ), true );
        $this->addRule( 'price', ts('Please enter the Market Value for this product'), 'required' );
        
        $this->add( 'text', 'cost',ts('Actual Cost of Product'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'cost' ), true );
        $this->addRule( 'price', ts('Please enter the Actual Cost of Product'), 'required' );

        $this->add( 'text', 'min_contribution',ts('Minimum Contribution Amount'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'min_contribution' ), true );
        $this->addRule( 'min_contribution', ts('Please enter the Minimum Contribution Amount'), 'required' );

        $this->add('textarea', 'options', ts('Option'), 'rows=3, cols=60' );

        $this->add('select', 'period_type', ts('Period Type'),array(''=>'--Select--','rolling'=> 'Rolling','fixed'=>'Fixed'));
               
        $this->add('text', 'fixed_period_start_day', ts('Fixed Period Start Day'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'fixed_period_start_day' ));    

        
        $this->add('Select', 'duration_unit', ts('Duration Unit'),array(''=>'--Select--','day'=> 'Day','week'=>'Week','month'=>'Month','year'=>'Year'));    
    
        $this->add('text', 'duration_interval', ts('Duration Interval'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'duration_interval' ));
        
        $this->add('Select', 'frequency_unit', ts('Frequency Unit'),array(''=>'--Select--','day'=> 'Day','week'=>'Week','month'=>'Month','year'=>'Year'));    

        $this->add('text', 'frequency_interval', ts('Duration Interval'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'frequency_interval' ));
       
    
        $this->add('checkbox', 'is_active', ts('Enabled?'));
        
        $this->addFormRule(array('CRM_Contribute_Form_ManagePremiums', 'formRule'));
        
        $this->addButtons( array( 
                                 array ( 'type'      => 'upload', 
                                         'name'      => ts('Save'), 
                                         'isDefault' => true   ), 
                                 array ( 'type'      => 'cancel', 
                                         'name'      => ts('Cancel') ), 
                                 ) 
                           );
             
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params) {
        //print_r($params);
        if ( $params['image'] == 'thumbnail' ) {
            if ( ! $params['imageUrl']) {
                $errors ['imageUrl']= "Image URL is Reqiured ";
            }
            if ( ! $params['thumbnailUrl']) {
                $errors ['thumbnailUrl']= "Thumbnail URL is Reqiured ";
            }
        }

        if ( ! $params['period_type'] ) {
            if ( $params['fixed_period_start_day'] || $params['duration_unit'] || $parmas['duration_interval'] ||
                 $params['frequency_unit'] || $params['frequency_interval'] ) {
                $errors ['period_type']= "Please Enter the Period Type";
            }
        }

        if( $params['duration_unit'] && ! $params['duration_interval'] ) {
            $errors ['duration_interval']= "Please Enter the Duration Interval";
        }

        if( $params['duration_interval'] && ! $params['duration_unit'] ) {
            $errors ['duration_unit']= "Please Enter the Duration Unit";
        }

        if( $params['frequency_interval'] && ! $params['frequency_unit'] ) {
            $errors ['frequency_unit']= "Please Enter the Frequency Unit";
        }

        if( $params['frequency_unit'] && ! $params['frequency_interval'] ) {
            $errors ['frequency_interval']= "Please Enter the Frequency Interval";
        }


        return empty($errors) ? true : $errors;
    }
   
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        
        require_once 'CRM/Contribute/BAO/ManagePremiums.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Contribute_BAO_ManagePremiums::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected Premium Product type has been deleted.') );
        } else { 
            $imageFile            = $this->controller->exportValue( $this->_name, 'uploadFile' );
            $imageFileURL         = $this->controller->exportValue( $this->_name, 'imageUrl' );
            $thumbnailRUL         = $this->controller->exportValue( $this->_name, 'thumbnailUrl' );
            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();
            
           
            // FIX ME 
            if(CRM_Utils_Array::value( 'image',$params, false )) {
                
                $value = CRM_Utils_Array::value( 'image',$params, false );
                if ( $value == 'image' ) {
                    $params['image'] = $imageFile;
                } else if (  $value == 'thumbnail' ) {
                    $params['image']   = $imageFileURL;
                    $params['thumbnail'] = $thumbnailRUL;
                } else if ( $value == 'default' ) {
                    $params['image'] = 'default_image.gif';
                } else {
                    $params['image'] = '';
                }
            }

            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['premium'] = $this->_id;
            }
            
            $premium = CRM_Contribute_BAO_ManagePremiums::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The Premium Product  "%1" has been saved.', array( 1 => $premium->name )) );
        }
    }
}

?>
