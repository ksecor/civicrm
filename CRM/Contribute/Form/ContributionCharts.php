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
        //p3 = Three dimensional pie chart.
        //bvg = Vertical bar chart
        $this->addElement('select', 'chart_type', ts('Chart Style'), array( 'bvg' => ts('Bar'), 'p3'=> ts('Pie') ) );

        //take available years from database to show in drop down
        if ( !empty( $this->_years ) ) {
            foreach( $this->_years as  $k => $v ){
                $years[$k] = $k;
            }
        }
        $this->addElement('select', 'select_year', ts('Select Year (for monthly breakdown)'), $years );
        $this->addButtons( array( 
                                 array ( 'type'      => 'refresh', 
                                         'name'      => ts('Reload Charts'), 
                                         'isDefault' => true   ), 
                                 ) 
                           );
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

        //set default currency to graph
        $currency = $config->defaultCurrency;

        //default chart is Bar chart to show data
        if ( $submittedValues['chart_type'] == 'p3' ) {
            $this->assign( 'chartType', 'p3');
        } else {
            $this->assign( 'chartType', 'bvg');
        }

        //take contribution information monthly
        require_once 'CRM/Contribute/BAO/Contribution/Utils.php';
        $chartInfoMonthly = CRM_Contribute_BAO_Contribution_Utils::contributionChartMonthly( $submittedValues['select_year'] );
        if ( is_array( $chartInfoMonthly ) ) {
            $this->assign( 'monthlyData', true );   
            //display bar chart linearly ::showing zero (0)
            //contribution for month if contribution for that
            //month not exist
            if ( ( $submittedValues['select_year'] == date('Y') ) || ( ! isset( $submittedValues['select_year'] ) ) ) {
                //if selected year is current, show the months up to
                //current month
                $j = date('m');
            } else {
                $j = 12;
            }
            for ($i = 1; $i <= $j; $i++) {
                $abbrMonthNames[$i] = strftime('%b', mktime(0, 0, 0, $i, 10, 1970 ));
            }
            foreach( $abbrMonthNames as $monthKey => $monthName ) {
                if ( ! $chartInfoMonthly['By Month'][$monthKey] ) {
                    //set zero value to month which is not in db
                    $chartInfoMonthly['By Month'][$monthKey] = 0;
                }   
            }
            ksort( $chartInfoMonthly['By Month'] );
            $totalMonths = count( $chartInfoMonthly['By Month'] );
            $this->assign( 'totalMonths', $totalMonths );
            $chartMonthly = array();
            $chartMonthly['By Month'] = array_combine($abbrMonthNames,$chartInfoMonthly['By Month'] );
            if ( $submittedValues['chart_type'] == 'p3' ) {
                foreach( $chartMonthly['By Month'] as $pieMonthName => $pieMonthValue ) {
                    if ( $pieMonthValue == 0 ) {
                        //unset the zero value month since not
                        //required in pie chart
                        unset( $chartMonthly['By Month'][$pieMonthName] );
                    }
                }
            }
            //label are separated by '|' and data is separated by ','
            foreach ( $chartMonthly as $key => $value ) {
                $data['marker'] = array_values( $value );
                $data['values'] = implode( ',', $value );
                $data['names']  = implode( '|', array_keys( $value ) );
            }	
            //set marker value for each bar with color and size
            foreach( $data['marker'] as $keys => $values ){
                $marking[] ='t'.$values.',0000FF,0,'.$keys.',10';
               
            }
            //marker on each bar to show exact value
            $mMarker = implode ('|', $marking);
                      
            $this->assign( 'mMarker',$mMarker );
            $legend = array_keys( $chartInfoMonthly );
            $maxAmount =  max( $chartInfoMonthly['By Month']);
            //increase the y axis length more than maximum amount
            //if total months are greater than one
            if ( $totalMonths > 1 ) {
                $percentage = $maxAmount / 5;
                $maxAmount += $percentage;
            }
            $this->assign( 'monthMaxAmount', $maxAmount );
            $this->assign( 'chartData',$data['values'] );
            $this->assign( 'chartLabel',$data['names'] );

            if ( $submittedValues['select_year'] ) {
                $legendYear = $submittedValues['select_year'];
            } else {
                $legendYear = date('Y');
            }
            $this->assign( 'chartLegend',$legend[0] . ' - ' . $legendYear);
            
        } else {
            $this->assign( 'monthlyData', false );
        } 
        //take contribution information by yearly
        $chartInfoYearly = CRM_Contribute_BAO_Contribution_Utils::contributionChartYearly();
        $totalYears = count( $chartInfoYearly['By Year'] );
        $this->assign( 'totalYears', $totalYears );
        $this->_years =  $chartInfoYearly['By Year'];
        if ( is_array( $chartInfoYearly ) ) {
            //label are separated by '|' and data is separated by ','
            foreach ( $chartInfoYearly as $key => $value ) {
                $data1['marker'] = array_values( $value );
                $data1['values'] = implode( ',', $value );
                $data1['names'] = implode( '|', array_keys( $value ) );
                
            }
            //set marker value for each bar with color and size
            foreach( $data1['marker'] as $keys => $values ){
                $marking1[] ='t'.$values.',0000FF,0,'.$keys.',10';
               
            }
            //marker on each bar to show exact value
            $yMarker = implode ('|', $marking1);
           
            $this->assign( 'yMarker',$yMarker );
            $maxAmount = max( $chartInfoYearly['By Year']);
            $legend = array_keys( $chartInfoYearly );
            //increase the y axis length more than maximum amount
            //if total years are greater than one
            if ( $totalYears > 1 ) {
                $percentage = $maxAmount / 5;
                $maxAmount += $percentage;
            }
            $this->assign( 'yearMaxAmount', $maxAmount );
            $this->assign( 'chartData1',$data1['values'] );
            $this->assign( 'chartLabel1',$data1['names'] );
            $this->assign( 'chartLegend1',$legend[0] );
        } 
        $this->assign( 'hasContributions' ,true );
        if ( empty ( $chartInfoYearly ) ) {
            // if no contributions available, show the message
            $this->assign( 'hasContributions' , false );
        }

    }//end of function
}
