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
     * Build The Pie Chart images with given params 
     * and store in upload/pChart directory.
     *
     * @param  array $params    an assoc array of name/value pairs          
     * @return array $filesPath created image files Path.
     *
     * @static
     */
    static function pieChart( $params ) 
    {        
        if ( empty( $params ) ) {
            return;
        }
        
        //get the currency.
        $currency = $config->defaultCurrency;
        
        //configure the directory for pChart.
        $config =& CRM_Core_Config::singleton( );
        
        $pChartPath    = str_replace( 'templates', 'packages', $config->templateDir ) ;
        $pChartPath   .= 'pChart/Fonts/';
        $uploadDirURL  = str_replace( 'persist/contribute/', 'upload/pChart/', $config->imageUploadURL);
        $uploadDirPath = $config->uploadDir . 'pChart/' ;
        
        //create pchart directory, if exist clean and then create again.
        if ( !is_dir( $uploadDirPath ) ) {
            CRM_Utils_File::cleanDir( $uploadDirPath );
            CRM_Utils_File::createDir( $uploadDirPath );
        } else {
            CRM_Utils_File::createDir( $uploadDirPath ); 
        }
        
        require_once 'packages/pChart/pData.class.php';
        require_once 'packages/pChart/pChart.class.php';
        
        $chartCount  = 0;
        $filesValues = array( );
        foreach ( $params as $chartIndex => $chartValues ) {
            $chartCount++;
            $shades = 0;
            $names  = $values = $lengths = array( );
            foreach ( $chartValues['values'] as $indexName => $indexValue ) {
                if ( $indexValue ) {
                    $names[]  = $indexName;
                    $values[] = $indexValue;
                    $lengths[] = strlen( $indexName );
                }
                $shades++;
            }
            $legend = CRM_Utils_Array::value('legend', $chartValues );
            
            $dataSet = new pData;
            $dataSet->AddPoint( $names, "Serie2" );
            $dataSet->AddPoint( $values, "Serie1" );
            $dataSet->AddAllSeries( );
            $dataSet->SetAbsciseLabelSerie( "Serie2" );
            
            //Initialise the co-ordinates.
            $ySize          = 220;
            $radius         = 95;
            $skew           = 45;
            $yPosition      = 120;
            $xPosition      = 140;
            $spliceHeight   = 15;
            $spliceDistance = 0;
            $legendLength   = 45 + 7 * max( $lengths );
            
            //make the graph resizable
            $xSize = 300 + $legendLength;
            
            //Initialise the graph
            $chart = new pChart( $xSize, $ySize );
            $chart->drawFilledRoundedRectangle( 0, 0,  $xSize, $ySize,  5, 240, 240, 240 );
            $chart->drawRoundedRectangle( 0, 0,  $xSize, $ySize, 5, 230, 230, 230 );
            
            //set colors.
            $chart->createColorGradientPalette( 195, 204, 56, 223, 110, 41, $shades );
            
            //Draw the pie chart
            $chart->setFontProperties( $pChartPath.'tahoma.ttf', 10 );
            $chart->AntialiasQuality = 0;
            $chart->setShadowProperties( 1, 1, 60, 60, 60, 15 , 0 );
            
            $chart->drawPieGraph( $dataSet->GetData( ),
                                  $dataSet->GetDataDescription( ), 
                                  $xPosition, $yPosition, $radius, 
                                  PIE_PERCENTAGE_LABEL, TRUE, $skew, $spliceHeight, $spliceDistance );
            
            //get the coordinates.
            $allCoords = $chart->coordinates( );
            $chart->drawPieLegend( ($xSize - $legendLength), 10, $dataSet->GetData( ),
                                   $dataSet->GetDataDescription( ), 250, 250, 250 );
            
            //write the title
            if ( $legend ) {
                $chart->setFontProperties( $pChartPath . "tahoma.ttf", 10 );
                $chart->drawTitle( ($xPosition-(strlen($legend)*3)), 22, $legend, 50, 50, 50 );
            }
            
            $fileName = "pChart{$chartCount}" . time( ) . '.png';
            $chart->Render( $uploadDirPath . $fileName );
            
            //get the created file path.
            $filesValues[$chartIndex]['file_name'] = $uploadDirURL . $fileName;
            
            //format the month cordinates
            $position = 0;
            $chartCoords = array( );
            foreach ( $chartValues['values'] as $indexName => $indexValue ) {
                if ( $indexValue ) {
                    foreach ( $allCoords as $type => $coords ) {
                        $chartCoords[$indexName][$type] = implode( ',', $coords[$position] );
                    }
                    $position++;
                }
            }
            $filesValues[$chartIndex]['coords'] = $chartCoords;
            
            //free the chart and data objects.
            unset( $chart );
            unset( $dataSet );
        }
        
        return $filesValues;
    }
    
    /**
     * Build The Bar Chart image with given params 
     * and store in upload/pChart directory.
     *
     * @param  array  $params    an assoc array of name/value pairs          
     * @return array  $filesPath created image files Path.
     *
     * @static
     */
    static function barChart( $params ) 
    {
        if ( empty( $params ) ) {
            return;
        }
        
        //get the required directory path.
        $config =& CRM_Core_Config::singleton( );			
        
        //get the default currency.
        $currency = $config->defaultCurrency;
        
        $pChartPath    = str_replace( 'templates', 'packages', $config->templateDir ) ;
        $pChartPath   .= 'pChart/Fonts/';
        $uploadDirURL  = str_replace( 'persist/contribute/', 'upload/pChart/', $config->imageUploadURL);
        $uploadDirPath = $config->uploadDir . 'pChart/' ;
        
        //create pchart directory, if exist clean and then create again.
        if ( is_dir( $uploadDirPath ) ) {
            CRM_Utils_File::cleanDir( $uploadDirPath );
            CRM_Utils_File::createDir( $uploadDirPath );
        } else {
            CRM_Utils_File::createDir( $uploadDirPath ); 
        }
        
        require_once 'packages/pChart/pData.class.php';
        require_once 'packages/pChart/pChart.class.php';
        
        $chartCount = 0;
        $filesValues = array( );
        foreach ( $params as $chartIndex => $chartValues ) {
            $chartCount++;
            $shades = 0;
            $names  = $values = array( );
            foreach ( $chartValues['values'] as $indexName => $indexValue ) {
                $names[] = $indexName;
                $values[] = $indexValue;
            }
            $legend = CRM_Utils_Array::value('legend', $chartValues );
            $maxScale = round( ( max( $chartValues['values'] ) + 300 ) / 100 ) * 100;
            
            //Initialise the co-ordinates.
            $x1    = 60;
            $y1    = 27;
            $y2    = 270;
            $ySize = 300;
            
            //calculate x axis size as per number of months.
            $divisionWidth = 44;
            $x2    = 105 + ( count( $chartValues['values'] ) - 1 ) * $divisionWidth;
            $xSize = $x2 + 20;
            
            $dataSet = new pData;
            $dataSet->AddPoint( $values, "Serie1" );
            $dataSet->AddPoint( $names, "Serie2" );
            $dataSet->AddSerie( "Serie1" );
            $dataSet->SetAbsciseLabelSerie( "Serie2" );
            $dataSet->SetYAxisUnit( $currency );
            
            //Initialise the graph
            $chart = new pChart( $xSize, $ySize );
            $chart->setFontProperties( $pChartPath ."tahoma.ttf", 8 );
            $chart->setGraphArea( $x1, $y1, ($xSize-20), ($ySize-30) );
            
            //set the y axis scale.
            $chart->setFixedScale( 0, $maxScale, 1 );
            
            $chart->drawFilledRoundedRectangle( ($x1-58), ($y1-25), ($x2+20), ($y2+27), 5, 240, 240, 240 );
            $chart->drawRoundedRectangle( ($x1-58), ($y1-25), ($x2+20), ($y2+27), 5, 230, 230, 230 );
            
            $chart->drawGraphArea( 255, 255, 255, TRUE );
            $chart->drawScale( $dataSet->GetData( ), $dataSet->GetDataDescription( ),
                               SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2, TRUE, 1, FALSE, $divisionWidth );
            
            $chart->drawGrid( 4, TRUE, 230, 230, 230, 50 );
            
            //Draw the bar chart
            $chart->setFontProperties( $pChartPath.'tahoma.ttf', 8 );
            
            $chart->setColorPalette( 0, 69, 139, 0 );
            $chart->drawBarGraph( $dataSet->GetData( ), $dataSet->GetDataDescription( ), TRUE, 80 );
            
            //get the co-ordinates
            $coords = $chart->coordinates( );
            
            //get the series values and write at top.
            $chart->setColorPalette( 0, 0, 0, 255 );
            $dataDesc = $dataSet->GetDataDescription( );
            $chart->writeValues( $dataSet->GetData( ), $dataSet->GetDataDescription( ), $dataDesc['Values'] ); 
            
            //Write the title
            if ( $legend ) {
                $chart->setFontProperties( $pChartPath . "tahoma.ttf", 10 );
                $chart->drawTitle( $xSize/2, $y1-7, $legend, 50, 50, 50 );
            }
            
            $fileName = "pChartByMonth{$chartCount}" . time( ) . '.png';
            $chart->Render( $uploadDirPath . $fileName );
            
            //get the file path.
            $filesValues[$chartIndex]['file_name'] = $uploadDirURL . $fileName;
            
            //format the coordinates to make graph clickable.
            $position = 0;
            $chartCoords = array( );
            foreach ( $chartValues['values'] as $name => $value ) {
                $chartCoords[$name] = implode( ',', array( $coords['xCoords'][$position],
                                                           $coords['yCoords'][$position], 
                                                           $coords['xCoords'][$position] + $divisionWidth/2,
                                                           $y2) );
                $position++;
            }
            $filesValues[$chartIndex]['coords'] = $chartCoords;
            
            //free the chart and data objects.
            unset( $chart );
            unset( $dataSet );
        }
        
        return $filesValues;
    }
}


