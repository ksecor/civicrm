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
        //get the submitted form values.
        $submittedValues = $this->controller->exportValues( $this->_name );
        
        //take contribution information monthly
        require_once 'CRM/Contribute/BAO/Contribution/Utils.php';
        $selectedYear = CRM_Utils_Array::value( 'select_year', $submittedValues, date('Y') ); 
        $chartInfoMonthly = CRM_Contribute_BAO_Contribution_Utils::contributionChartMonthly( $selectedYear );
        
        $pChartParams = array( );
        $monthlyData = false;
        if ( is_array( $chartInfoMonthly ) ) {
            $monthlyData = true;
            
            //display bar chart linearly ::showing zero (0)
            //contribution for month if contribution for that
            //month not exist
            if ( ( CRM_Utils_Array::value( 'select_year', $submittedValues ) == date('Y') ) || 
                 ( ! isset( $submittedValues['select_year'] ) ) ) {
                //if selected year is current, show the months up to
                //current month
                $j = date('m');
            } else {
                $j = 12;
            }
            for ($i = 1; $i <= $j; $i++) {
                $abbrMonthNames[$i] = strftime('%b', mktime(0, 0, 0, $i, 10, 1970 ));
            }
            
            foreach ( $abbrMonthNames as $monthKey => $monthName ) {
                if ( ! CRM_Utils_Array::value( $monthKey, $chartInfoMonthly['By Month'] ) ) {
                    //set zero value to month which is not in db
                    $chartInfoMonthly['By Month'][$monthKey] = 0;
                }
            }
            
            //sort the array.
            ksort( $chartInfoMonthly['By Month'] );
            
            //build the params for pChart.
            $pChartParams['by_month']['values'] = array_combine( $abbrMonthNames, $chartInfoMonthly['By Month'] );
            $pChartParams['by_month']['legend'] = 'By Month' . ' - ' . $selectedYear;
        }
        $this->assign( 'monthlyData', $monthlyData ); 
        
        //take contribution information by yearly
        $chartInfoYearly = CRM_Contribute_BAO_Contribution_Utils::contributionChartYearly( );
        
        //get the years.
        $this->_years =  $chartInfoYearly['By Year'];
        $hasContributions = false;
        if ( is_array( $chartInfoYearly ) ) {
            $hasContributions = true;
            $pChartParams['by_year']['legend'] = 'By Year';
            $pChartParams['by_year']['values'] = $chartInfoYearly['By Year'];
        }
        $this->assign( 'hasContributions', $hasContributions );
        
        //handle pchart functionality.
        if ( !empty( $pChartParams ) ) {
            $filesPath = array( );
            require_once 'CRM/Utils/PChart.php';
            if ( 'p3' == CRM_Utils_Array::value( 'chart_type', $submittedValues, 'bvg' ) ) {
                //build the pie graph
                $filesPath = CRM_Utils_PChart::buildPieChart( $pChartParams );
            } else {
                //build the bar graph.
                $filesPath = CRM_Utils_PChart::buildBarChart( $pChartParams );
            }
            
            //assign all files path.
            $this->assign( 'monthFilePath', CRM_Utils_Array::value('by_month_file', $filesPath ) );
            $this->assign( 'yearFilePath',  CRM_Utils_Array::value('by_year_file',  $filesPath ) );
        }
    }
}
