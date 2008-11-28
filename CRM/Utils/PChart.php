<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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
 * Build various graphs using pCharts library.
 */
class CRM_Utils_PChart 
{
    /**
     * Build The Pie Chart image with given params 
     * and store in upload/pChart directory.
     * Required params are only array of months and years with
     * corresponding values by which we are drawing graphs.
     *
     * @param  array $params    an assoc array of name/value pairs          
     * @return array $filesPath created image files Path.
     *
     * @static
     */
    static function buildPieChart( $params ) 
    {        
        $byMonth   = $byYear      = false;
        $monthName = $monthValues = $yearNames = $yearValues = $filesPath = array( );
        
        //format the month params.
        if ( !empty( $params['by_month']  ) ) {
            $byMonth = true;
            $monthShades = 0;
            foreach ( $params['by_month']['values'] as $month => $value ) {
                if ( $value ) {
                    $monthNames[]  = $month;
                    $monthValues[] = $value;
                }
                $monthShades++; 
            }
            $monthLegend = $params['by_month']['legend'];
        }
        
        //format the year params.
        if ( !empty( $params['by_year'] ) ) {
            $byYear = true;
            $yearShades = 0;
            foreach ( $params['by_year']['values'] as $year => $value ) {
                if ( $value ) {
                    $yearNames[]  = $year;
                    $yearValues[] = $value;
                }
                $yearShades++;  
            }
            $yearLegend = $params['by_year']['legend'];
        }
        
        //get the required directory path.
        if ( $byMonth || $byYear ) {
            $config =& CRM_Core_Config::singleton( );
            
            //get the currency.
            $currency = $config->defaultCurrency;
            $templatesDir = explode( '/', $config->templateDir );
            $resourcePath = array( );
            foreach ( $templatesDir as $key => $val ) {
                if ( $val == 'templates' ) {
                    break;
                }
                $resourcePath[] = $val;
            }
            
            $pChartPath    = implode( '/', $resourcePath ) . 'packages/pChart/Fonts/';
            $uploadDirURL  = self::uploadDirURL( );
            $uploadDirURL .= 'pChart/';
            $uploadDirPath = $config->uploadDir . 'pChart/' ;
            
            //create pchart directory, if exist clean and then create again.
            if ( is_dir( $uploadDirPath ) ) {
                CRM_Utils_File::cleanDir( $uploadDirPath );
                CRM_Utils_File::createDir( $uploadDirPath );
            } else {
                CRM_Utils_File::createDir( $uploadDirPath ); 
            }
        }
        
        require_once 'packages/pChart/pData.class.php';
        require_once 'packages/pChart/pChart.class.php';
        
        //1. build Pie Braph By Month.
        if ( $byMonth ) {
            $monthDataSet = new pData;
            $monthDataSet->AddPoint( $monthNames, "Serie2");
            $monthDataSet->AddPoint( $monthValues, "Serie1");
            $monthDataSet->AddAllSeries( );
            $monthDataSet->SetAbsciseLabelSerie( "Serie2" );
            
            //Initialise the co-ordinates.
            $xSize          = 340;
            $ySize          = 220;
            $radius         = 95;
            $skew           = 45;
            $xPosition      = 140;
            $yPosition      = 120;
            $spliceHeight   = 15;
            $spliceDistance = 0;
            
            //Initialise the graph
            $monthChart = new pChart( $xSize, $ySize );
            $monthChart->drawFilledRoundedRectangle( 0, 0,  $xSize, $ySize,  5, 240, 240, 240 );
            $monthChart->drawRoundedRectangle( 0, 0,  $xSize, $ySize, 5, 230, 230, 230 );
            
            //set colors.
            $monthChart->createColorGradientPalette( 195, 204, 56, 223, 110, 41, $monthShades );
            
            //Draw the pie chart
            $monthChart->setFontProperties( $pChartPath.'tahoma.ttf', 10 );
            $monthChart->AntialiasQuality = 0;
            $monthChart->setShadowProperties( 1, 1, 60, 60, 60, 10 , 0 );
            $monthChart->drawPieGraph( $monthDataSet->GetData( ),
                                       $monthDataSet->GetDataDescription( ), 
                                       $xPosition, $yPosition, $radius, 
                                       PIE_PERCENTAGE_LABEL, TRUE, $skew, $spliceHeight, $spliceDistance );
            
            $monthChart->drawPieLegend( $xSize-60, 10, $monthDataSet->GetData( ),
                                        $monthDataSet->GetDataDescription( ), 250, 250, 250 );
            
            //write the title
            $monthChart->setFontProperties( $pChartPath . "tahoma.ttf", 10 );
            $monthChart->drawTitle( ($xPosition-50), 22, $monthLegend, 50, 50, 50 );
            
            $monthFileName = 'pChartByMonth' . time( ) . '.png';
            $monthChart->Render( $uploadDirPath . $monthFileName );
            
            //get the created file path.
            $filesPath['by_month_file'] = $uploadDirURL . $monthFileName;
        }
        
        //2. build Pie Braph By Year.
        if ( $byYear ) {
            $yearDataSet = new pData;
            $yearDataSet->AddPoint( $yearNames, "Serie2");
            $yearDataSet->AddPoint( $yearValues, "Serie1");
            $yearDataSet->AddAllSeries( );
            $yearDataSet->SetAbsciseLabelSerie( "Serie2" );
            
            //Initialise the graph
            $yearChart = new pChart( $xSize, $ySize );
            
            //set the colors.
            $yearChart->drawFilledRoundedRectangle( 0, 0,  $xSize, $ySize,  5, 240, 240, 240 );
            $yearChart->drawRoundedRectangle( 0, 0,  $xSize, $ySize, 5, 230, 230, 230 );
            
            //set colors.
            $yearChart->createColorGradientPalette( 195, 204, 56, 223, 110, 41, $yearShades );
            
            //Draw the pie chart
            $yearChart->setFontProperties( $pChartPath.'tahoma.ttf', 10 );
            $yearChart->AntialiasQuality = 0;
            $yearChart->setShadowProperties( 1, 1, 60, 60, 60, 10 , 0 );
            $yearChart->drawPieGraph( $yearDataSet->GetData( ),
                                      $yearDataSet->GetDataDescription( ), 
                                      $xPosition, $yPosition, $radius, 
                                      PIE_PERCENTAGE_LABEL, TRUE, $skew, $spliceHeight, $spliceDistance );
            
            $yearChart->drawPieLegend( $xSize-70, 10, $yearDataSet->GetData( ),
                                       $yearDataSet->GetDataDescription( ), 250, 250, 250 );
            
            //write the title
            $yearChart->setFontProperties( $pChartPath . "tahoma.ttf", 10 );
            $yearChart->drawTitle( ($xPosition-35), 22, $yearLegend, 50, 50, 50 );
            
            $yearFileName = "pChartByYear" . time( ) . ".png";
            $yearChart->Render( $uploadDirPath . $yearFileName );
            
            //get the created file path
            $filesPath['by_year_file'] = $uploadDirURL . $yearFileName;
        }
        
        return $filesPath;
    }
    
    /**
     * Build The Bar Chart image with given params 
     * and store in upload/pChart directory.
     * Required params are array of all months and years with
     * corresponding values by which we are drawing graphs.
     *
     * @param  array  $params    an assoc array of name/value pairs          
     * @return array  $filesPath created image files Path.
     *
     * @static
     */
    static function buildBarChart( $params ) 
    {
        //build the formatted arrays.
        $byMonth = $byYear = false;
        $monthName = $monthValues = $yearNames = $yearValues = $filesPath = array( );
        
        //format the month params.
        if ( !empty( $params['by_month']  ) ) {
            $byMonth = true;
            foreach ( $params['by_month']['values'] as $month => $value ) {
                $monthNames[]  = $month;
                $monthValues[] = $value;
            }
            
            $monthLegend = $params['by_month']['legend'];
            
            //rounded to 100's
            $monthMaxScale = round( ( max( $params['by_month']['values'] ) + 300 ) / 100 ) * 100;
        }
        
        //format the year params.
        if ( !empty( $params['by_year'] ) ) {
            $byYear = true;
            foreach ( $params['by_year']['values'] as $year => $value ) {
                $yearNames[]  = $year;
                $yearValues[] = $value;
            }
            $yearLegend = $params['by_year']['legend'];
            
            //rounded to 100's
            $yearMaxScale = round( ( max( $params['by_year']['values'] ) + 300 ) / 100 ) * 100;
        }
        
        //get the required directory path.
        if ( $byMonth || $byYear ) {
            $config =& CRM_Core_Config::singleton( );
            
            //get the default currency.
            $currency = $config->defaultCurrency;
            
            $templatesDir = explode( '/', $config->templateDir );
            $resourcePath = array( );
            foreach ( $templatesDir as $key => $val ) {
                if ( $val == 'templates' ) {
                    break;
                }
                $resourcePath[] = $val;
            }
            
            $pChartPath    = implode( '/', $resourcePath ) . 'packages/pChart/Fonts/';
            
            $uploadDirURL  = self::uploadDirURL( );
            $uploadDirURL .= 'pChart/';
            $uploadDirPath = $config->uploadDir . 'pChart/' ;
            
            //create pchart directory, if exist clean and then create again.
            if ( is_dir( $uploadDirPath ) ) {
                CRM_Utils_File::cleanDir( $uploadDirPath );
                CRM_Utils_File::createDir( $uploadDirPath );
            } else {
                CRM_Utils_File::createDir( $uploadDirPath ); 
            }
        }
        
        require_once 'packages/pChart/pData.class.php';
        require_once 'packages/pChart/pChart.class.php';
        
        //1. build By Month chart
        if ( $byMonth ) {
            
            //Initialise the co-ordinates.
            $monthX1    = 60;
            $monthY1    = 27;
            $monthY2    = 270;
            $monthYsize = 300;
            
            //calculate x axis size as per number of months.
            $monthXsize = count( $params['by_month']['values'] ) * 50;
            $monthX2    = $monthXsize - 20;
            
            $monthDataSet = new pData;
            $monthDataSet->AddPoint( $monthValues, "Serie1" );
            $monthDataSet->AddPoint( $monthNames, "Serie2" );
            $monthDataSet->AddSerie("Serie1");
            $monthDataSet->SetAbsciseLabelSerie( "Serie2" );
            $monthDataSet->SetYAxisUnit( $currency );
            
            //Initialise the graph
            $monthChart = new pChart( $monthXsize, $monthYsize );
            $monthChart->setFontProperties( $pChartPath ."tahoma.ttf",8);
            $monthChart->setGraphArea( $monthX1, $monthY1, ($monthXsize-20), ($monthYsize-30) );
            
            //set the y axis scale.
            $monthChart->setFixedScale( 0, $monthMaxScale, 1 );
            
            $monthChart->drawFilledRoundedRectangle( ($monthX1-58), ($monthY1-25), ($monthX2+20), ($monthY2+27), 5, 240, 240, 240 );
            $monthChart->drawRoundedRectangle( ($monthX1-58), ($monthY1-25),  ($monthX2+20),  ($monthY2+27), 5, 230, 230, 230 );
            
            $monthChart->drawGraphArea(255,255,255,TRUE);
            $monthChart->drawScale($monthDataSet->GetData(),$monthDataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
            $monthChart->drawGrid(4,TRUE,230,230,230,50);
            
            //Draw the bar chart
            $monthChart->setFontProperties( $pChartPath.'tahoma.ttf',8);
            
            $monthChart->setColorPalette( 0, 69, 139, 0 );
            $monthChart->drawBarGraph($monthDataSet->GetData(),$monthDataSet->GetDataDescription(),TRUE,80);
            
            //get the series values and write at top.
            $monthChart->setColorPalette( 0, 0, 0, 255 );
            $monthDataDesc = $monthDataSet->GetDataDescription( );
            $monthChart->writeValues( $monthDataSet->GetData(),$monthDataSet->GetDataDescription( ), $monthDataDesc['Values'] ); 
            
            //Write the title
            $monthChart->setFontProperties( $pChartPath . "tahoma.ttf",10);
            $monthChart->drawTitle( ($monthX2/2)-20, $monthY1-7, $monthLegend, 50, 50, 50 );
            
            $monthFileName = 'pChartByMonth' . time( ) . '.png';
            $monthChart->Render( $uploadDirPath . $monthFileName );
            
            //get the file path.
            $filesPath['by_month_file'] = $uploadDirURL . $monthFileName;
        }
        
        //2. build By Year chart
        if ( $byYear ) {
            
            //Initialise the co-ordinates.
            $yearX1    = 60;
            $yearY1    = 27;
            $yearY2    = 270;
            $yearYsize = 300;
            
            //need to calculate X size as per number of years.
            $yearXsize = count( $params['by_year']['values'] ) * 85;
            $yearX2    = $yearXsize - 20; 
            
            $yearDataSet = new pData;
            $yearDataSet->AddPoint( $yearValues, "Serie1");
            $yearDataSet->AddPoint( $yearNames,  "Serie2");
            $yearDataSet->AddSerie("Serie1");     
            $yearDataSet->SetAbsciseLabelSerie( "Serie2" );
            $yearDataSet->SetYAxisUnit( $currency );
            
            //Initialise the graph
            $yearChart = new pChart( $yearXsize, $yearYsize );
            $yearChart->setFontProperties( $pChartPath ."tahoma.ttf",8 );
            $yearChart->setGraphArea( $yearX1, $yearY1, $yearX2, $yearY2 );
            
            //set the y axis scale.
            $yearChart->setFixedScale( 0, $yearMaxScale, 1 );
            $yearChart->drawFilledRoundedRectangle( ($yearX1-58), ($yearY1-25), ($yearX2+20), ($yearY2+27), 5, 240, 240, 240 );
            $yearChart->drawRoundedRectangle( ($yearX1-58), ($yearY1-25),  ($yearX2+20),  ($yearY2+27), 5, 230, 230, 230 );
            
            //draw graph.
            $yearChart->drawGraphArea(255,255,255,TRUE);
            $yearChart->drawScale($yearDataSet->GetData(),$yearDataSet->GetDataDescription(),SCALE_NORMAL,150, 150, 150,TRUE,0,2,TRUE);
            $yearChart->drawGrid(4,TRUE,230,230,230,50);
            
            $yearChart->setColorPalette( 0, 69, 139, 0 );
            $yearChart->drawBarGraph($yearDataSet->GetData(),$yearDataSet->GetDataDescription(),TRUE, 80 );
            
            //get the series values and write at top.
            $yearChart->setColorPalette( 0, 0, 0, 255 );
            $yearDataDesc = $yearDataSet->GetDataDescription( );
            $yearChart->writeValues( $yearDataSet->GetData(),$yearDataSet->GetDataDescription( ), $yearDataDesc['Values'] ); 
            
            //Write the title
            $yearChart->setFontProperties( $pChartPath . "tahoma.ttf",10);
            $yearChart->drawTitle( $yearX2/2, $yearY1-7, $yearLegend, 50, 50, 50 );
            
            $yearFileName = 'pChartByYear' . time( ) . '.png';
            $yearChart->Render( $uploadDirPath . $yearFileName );
            
            //get the file path.
            $filesPath['by_year_file'] = $uploadDirURL . $yearFileName;
        }
        
        return $filesPath;
    }
    
    /* Build the upload Directory Url.
     *
     */
    static function uploadDirURL( ) 
    {
        $config =& CRM_Core_Config::singleton( );
        
        $checkPath = explode( DIRECTORY_SEPARATOR, dirname( $config->templateCompileDir ) );
        $directories = array( );
        foreach ( $checkPath as $dirIndex => $dirName ) {
            if ( $dirName == 'templates_c' ) {
                break;
            }
            $directories[] = $dirName; 
        }
        
        //build files directory path
        $checkBasePath = explode( DIRECTORY_SEPARATOR, trim( $config->userFrameworkBaseURL ) );
        for ( $i= count( $checkBasePath ) - 1 ; $i>0; $i-- ) {
            if ( CRM_Utils_Array::value( $i, $checkBasePath ) ) {
                $baseDirName = $checkBasePath[$i];
                break;
            }
        }
        
        $baseIndex = array_search( $baseDirName, $checkPath );
        foreach ( $directories as $key => $value ) {
            if ( $key > $baseIndex ) {
                $files[] = $value;
            }
        }
        
        $uploadFilesPath = CRM_Utils_File::addTrailingSlash( implode( DIRECTORY_SEPARATOR, $files ) );
        $uploadDirURL = $config->userFrameworkBaseURL . $uploadFilesPath . "upload/";
        
        return $uploadDirURL;
    }
}


