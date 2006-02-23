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
     * Function to pre  process the form
     *
     * @access public
     * @return None
     */
    public function preProcess() 
    {
        parent::preProcess();
 
    }

     /**
     * This function sets the default values for the form. Manage Premiums that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        require_once 'CRM/Utils/Rule.php';
        $defaults = parent::setDefaultValues( );
        if ( $this->_id ) {
            $params = array( 'id' => $this->_id );
            CRM_Contribute_BAO_ManagePremiums::retrieve( $params , $tempDefaults );
            $imageUrl  = $tempDefaults['image'];
            if ( CRM_Utils_Rule::url($imageUrl) ) {
                $defaults ['imageUrl']     = $tempDefaults['image'];
                $defaults ['thumbnailUrl'] = $tempDefaults['thumbnail'];
                $defaults ['imageOption' ] = 'thumbnail'; 
            } else {
                $defaults ['imageOption' ] = 'current'; 
            }
        }
        return $defaults;
    }


    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        //parent::buildQuickForm( );
        
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            require_once 'CRM/Contribute/BAO/Premium.php';
            CRM_Contribute_BAO_Premium::buildPremiumPreviewBlock( $this, $this->_id );
            
            $this->addButtons(array(
                                    array ('type'      => 'next',
                                           'name'      => ts('Done With Preview'),
                                           'isDefault' => true),
                                    )
                              );
            
            return;
        }
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            $this->addButtons(array(
                                    array ('type'      => 'next',
                                           'name'      => ts('Delete'),
                                           'isDefault' => true),
                                    array ('type'      => 'cancel',
                                           'name'      => ts('Cancel')),
                                    )
                              );
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'name' ) );
        $this->addRule( 'name', ts('Please enter a Product name.'), 'required' );
        $this->addRule( 'name', ts('A Product with this name already exists. Please select another name.'), 'objectExists', array( 'CRM_Contribute_DAO_Product', $this->_id ) );
        $this->add('text', 'sku', ts('SKU'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'sku' ),true );

        $this->add('textarea', 'description', ts('Description'), 'rows=3, cols=60' );
        //$this->add('radio', 'image', ts('Get image from my computer'), null ,null);
        $image['image']     = $this->createElement('radio',null, null,ts('Upload an image from my computer'),'image','onClick="add_upload_file_block(\'image\');');
        $image['thumbnail'] = $this->createElement('radio',null, null,ts('Display image and thumbnail from these locations:'),'thumbnail', 'onClick="add_upload_file_block(\'thumbnail\');');
        $image['defalut']   = $this->createElement('radio',null, null,ts('Use default image'),'defalut', 'onClick="add_upload_file_block(\'default\');');
        $image['noImage']   = $this->createElement('radio',null, null,ts('Do not display an image'),'noImage','onClick="add_upload_file_block(\'noImage\');');

        $image['current']   = $this->createElement('radio',null, null,ts('Use current image'),'current','onClick="add_upload_file_block(\'current\');');
        
        $this->addGroup($image,'imageOption',ts('Image'));
        $this->addRule( 'imageOption', ts('Please select an option for the premium image.'), 'required' );
        
        $this->addElement( 'text', 'imageUrl',ts('Image URL'));
        $this->addRule('imageUrl','Please enter the valid URL to display this image.','url');
        $this->addElement( 'text', 'thumbnailUrl',ts('Thumbnail URL'));
        $this->addRule('thumbnailUrl','Please enter the valid URL to display a thumbnail of this image.','url');
       
        

        $this->add( 'file','uploadFile',ts('Image File Name'), 'onChange="select_option();"');

               
        $this->add( 'text', 'price',ts('Market Value'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'price' ), true );
        $this->addRule( 'price', ts('Please enter the Market Value for this product.'), 'required' );
        
        $this->add( 'text', 'cost',ts('Actual Cost of Product'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'cost' ), true );
        $this->addRule( 'price', ts('Please enter the Actual Cost of Product.'), 'required' );

        $this->add( 'text', 'min_contribution',ts('Minimum Contribution Amount'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'min_contribution' ), true );
        $this->addRule( 'min_contribution', ts('Please enter a monetary value for the Minimum Contribution Amount.'), 'required' );

        $this->add('textarea', 'options', ts('Options'), 'rows=3, cols=60' );

        $this->add('select', 'period_type', ts('Period Type'),array(''=>'--Select--','rolling'=> 'Rolling','fixed'=>'Fixed'));
               
        $this->add('text', 'fixed_period_start_day', ts('Fixed Period Start Day'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'fixed_period_start_day' ));    

        
        $this->add('Select', 'duration_unit', ts('Duration Unit'),array(''=>'--Select--','day'=> 'Day','week'=>'Week','month'=>'Month','year'=>'Year'));    
    
        $this->add('text', 'duration_interval', ts('Duration Interval'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'duration_interval' ));
        
        $this->add('Select', 'frequency_unit', ts('Frequency Unit'),array(''=>'--Select--','day'=> 'Day','week'=>'Week','month'=>'Month','year'=>'Year'));    

        $this->add('text', 'frequency_interval', ts('Frequency Interval'),CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Product', 'frequency_interval' ));
       
    
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
    public function formRule(&$params, &$files) {

        if ( $params['imageOption'] == 'thumbnail' ) {
            if ( ! $params['imageUrl']) {
                $errors ['imageUrl']= "Image URL is Reqiured ";
            }
            if ( ! $params['thumbnailUrl']) {
                $errors ['thumbnailUrl']= "Thumbnail URL is Reqiured ";
            }
        }

        
        $fileLocation  = $files['uploadFile']['tmp_name'];
        list($width, $height ) = getimagesize($fileLocation); 
        
        if ( ($width < 80 || $width > 500) ||  ( $height  < 80 || $height > 500) ) {
            //$errors ['uploadFile'] = "Please Enter files with dimensions between 80 x 80 and 500 x 500," . " Dimensions of this file is ".$width."X".$height;
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
        
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            return;
        }
        
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Contribute_BAO_ManagePremiums::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected Premium Product type has been deleted.') );
        } else { 
            $imageFile            = $this->controller->exportValue( $this->_name, 'uploadFile' );
           
            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();
           
            // FIX ME 
            if(CRM_Utils_Array::value( 'imageOption',$params, false )) {
                
                $value = CRM_Utils_Array::value( 'imageOption',$params, false );
                if ( $value == 'image' ) {
                    if ( $imageFile ) {
                        $params['image'] = $imageFile;
                        // to check wether GD is installed or not

                        require_once 'CRM/Utils/System.php';

                        $gdSupport = CRM_Utils_System::getModuleSetting('gd','GD Support');
                        $jpgSupport = CRM_Utils_System::getModuleSetting('gd','JPG Support');
                        $gifSupport = CRM_Utils_System::getModuleSetting('gd','GIF Read Support');
                        $pngSupport = CRM_Utils_System::getModuleSetting('gd','PNG Support');
                        $error = false; 
                        if ( $gdSupport == 'enabled' && $jpgSupport == 'enabled' && $gifSupport == 'enabled' && $pngSupport == 'enabled' ){
                            list($width_orig, $height_orig) = getimagesize($imageFile);
                            $imageInfo = getimagesize($imageFile);
                            echo $mime;
                            $width_orig."<br>";
                            $height_orig."<br>";    
                            $path = explode( '/', $imageFile );
                            $thumbFileName = $path[count($path) - 1];
                            $thumbFileName = $thumbFileName.".thumb";
                            $path[count($path) - 1] = $thumbFileName;
                            $path = implode('/',$path);
                            
                            $width = $height = 100;
                            
                            $thumb = imagecreate($width, $height);
                            if( $imageInfo['mime']  == 'image/gif' ) {
                                $source = imagecreatefromgif($imageFile);
                            } else if ( $imageInfo['mime']  == 'image/png' ) {
                                $source = imagecreatefrompng($imageFile);
                            } else {
                                $source = imagecreatefromjpeg($imageFile);
                            }
                            imagecopyresized($thumb,$source, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                            
                            $fp=fopen( $path ,'w+');
                            ob_start();
                            ImageJPEG($thumb);
                            $image_buffer = ob_get_contents();
                            ob_end_clean();
                            ImageDestroy($thumb);
                            fwrite($fp, $image_buffer); 
                            rewind($fp);
                            fclose($fp);
                            $params['thumbnail'] = $path;
                        } else {
                            $error = true; 
                        }
                    }
                } else if (  $value == 'thumbnail' ) {
                    $params['image']   = $params['imageUrl'];//empty( $imageFileURL ) ? null : $imageFileURL;
                    $params['thumbnail'] = $params['thumbnailUrl']; //empty ( $thumbnailRUL ) ? null : $thumbnailRUL;
                } else if ( $value == 'default' ) {
                    $params['image'] = 'default_image.gif';
                } 
            }

            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['premium'] = $this->_id;
            }
            
            $premium = CRM_Contribute_BAO_ManagePremiums::add($params, $ids);
            if ( $error ) {
                CRM_Core_Session::setStatus(ts('NOTICE: No thumbnail of your image was created because the GD image library is not currently compiled in your PHP installation. If you have a local thumbnail image you can upload it separately and enter the image URL by editing this product.'));
            } else {
                CRM_Core_Session::setStatus( ts('The Premium Product  "%1" has been saved.', array( 1 => $premium->name )) );
            }
        }
    }
}

?>
