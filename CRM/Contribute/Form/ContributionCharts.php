<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
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
class CRM_Contribute_Form_ContributionCharts extends CRM_Core_Form
{
    /** 
     * @access protected 
     * @var boolean 
     */ 
   
    function preProcess( ) 
    {
      
        $this->postProcess( );
    } 

  
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    public function buildQuickForm()
    {

        $this->addElement('select', 'select_map', ts('Select Map'), array( 'bvg' => 'Bar','p3'=> 'Pi' ) );
        foreach( $this->_years as  $k => $v ){
            $years[$k] = $k;
        }
        $this->addElement('select', 'select_year', ts('Select Year'), $years );
        $this->addButtons( array( 
                                 array ( 'type'      => 'refresh', 
                                         'name'      => ts('View'), 
                                         'isDefault' => true   ), 
                                 ) 
                           );
       
        //CRM_Core_Error::debug( '$this', $this );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
    
        $submittedValues = $this->controller->exportValues( $this->_name );
        $config  =& CRM_Core_Config::singleton( );
        $currency = $config->defaultCurrency;
       
        if ( $submittedValues['select_map'] == 'p3' ) {
            $this->assign( 'chartType', 'p3');
        } else {
            $this->assign( 'chartType', 'bvg');
        }
        //take contribution information monthly
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $chartInfoMonthly = CRM_Contribute_BAO_Contribution::contributionChartMonthly( $submittedValues['select_year'] );
        $this->assign( 'monthlyData', true );       
        if ( is_array( $chartInfoMonthly ) ) {
            foreach ( $chartInfoMonthly as $key => $value ) {
                $data['marker'] = array_values( $value );
                $data['values'] = implode( ',', $value );
                $data['names']  = implode( '|', array_keys( $value ) );
            }			
            foreach( $data['marker'] as $keys => $values ){
                $marking[] ='t'.$values.',0000FF,0,'.$keys.',10';
               
            }
            //marker on each bar to show exact value
            $mMarker = implode ('|', $marking);
                      
            $this->assign( 'mMarker',$mMarker );
            $legend = array_keys( $chartInfoMonthly );
            $this->assign( 'monthMaxAmount', max( $chartInfoMonthly['Contribution By Month']));
            $this->assign( 'chatData',$data['values'] );
            $this->assign( 'chatLabel',$data['names'] );
            $this->assign( 'chartLegend',$legend[0] );
        } else {
            $this->assign( 'monthlyData', false );
        } 
        //take contribution information by yearly
        $chartInfoYearly = CRM_Contribute_BAO_Contribution::contributionChartYearly();
        
        $this->_years =  $chartInfoYearly['Contribution By Year'];
        if ( is_array( $chartInfoYearly ) ) {
            foreach ( $chartInfoYearly as $key => $value ) {
                $data1['marker'] = array_values( $value );
                $data1['values'] = implode( ',', $value );
                $data1['names'] = implode( '|', array_keys( $value ) );
                
            }
            foreach( $data1['marker'] as $keys => $values ){
                $marking1[] ='t'.$values.',0000FF,0,'.$keys.',10';
               
            }
            //marker on each bar to show exact value
            $yMarker = implode ('|', $marking1);
           
            $this->assign( 'yMarker',$yMarker );
            $legend = array_keys( $chartInfoYearly );
            $this->assign( 'yearMaxAmount', max( $chartInfoYearly['Contribution By Year']));
            $this->assign( 'chatData1',$data1['values'] );
            $this->assign( 'chatLabel1',$data1['names'] );
            $this->assign( 'chartLegend1',$legend[0] );
        } 
        $this->assign( 'noContribution' ,true );
        if ( empty ( $chartInfoYearly ) ) {
            //if no contribution available, show the message
            $this->assign( 'noContribution' ,false );
        }

    }//end of function
}