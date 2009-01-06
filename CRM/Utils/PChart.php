<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
     * colors in R-G-B format
     * @var array
     * @static
     */
    private static $_colors = array( array( 195,   204,   56 ),
                                     array( 200.6, 185.2, 53 ),
                                     array( 206.2, 166.4, 50 ),
                                     array( 211.8, 147.6, 47 ),
                                     array( 217.4, 128.8, 44 ),
                                     array( 250,   105,   0  ),
                                     array( 220,   155,   87 ),
                                     array( 247,   143,   1  ),
                                     array( 90,    181,   110),
                                     array( 111,   128,   105),
                                     array( 201,   34,    0  ), 
                                     array( 235,   108,   92 ));
    
    /**
     * Build The Pie Graph images with given params 
     * and store in upload/pChart directory.
     *
     * @param  array $params    an assoc array of name/value pairs          
     * @return array $filesPath created image files Path.
     *
     * @static
     */
    static function pieGraph( $params ) 
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
                    $shades++;
                }
            }
            $legend = CRM_Utils_Array::value('legend', $chartValues );
            
            $dataSet = new pData;
            $dataSet->AddPoint( $names, "Serie2" );
            $dataSet->AddPoint( $values, "Serie1" );
            $dataSet->AddAllSeries( );
            $dataSet->SetAbsciseLabelSerie( "Serie2" );
            
            //Initialise the graph variables.
            //with only radius we can resize entire image.
            $radius         = 110;
            $skew           = 50;
            $spliceHeight   = 20;
            $spliceDistance = 4;
            
            //get the length for legend.
            $legendLength   = 45 + 7 * max( $lengths );
            
            //cofigure all other parameters at run time.
            $xSize = 2 * ( $radius + $legendLength ) + 60;
            $ySize = ( 2 * $radius ) + 40;
            
            //resize vertically if more values.
            if ( count( $values ) > 18 ) {
                $ySize = $ySize + 15 *( count( $values ) - 18 );
            }
            
            $xPosition = ( $xSize - $legendLength )/2;
            $yPosition = $ySize/2;
            
            //Initialise the graph
            $chart = new pChart( $xSize, $ySize );
            $chart->drawFilledRoundedRectangle( 0, 0,  $xSize, $ySize,  5, 240, 240, 240 );
            $chart->drawRoundedRectangle( 0, 0,  $xSize, $ySize, 5, 230, 230, 230 );
            
            //set color shades.
            $chart->setColorShades( $shades, self::$_colors );
            
            //Draw the pie chart
            $chart->setFontProperties( $pChartPath.'tahoma.ttf', 10 );
            $chart->AntialiasQuality = 0;
            
            $chart->drawPieGraph( $dataSet->GetData( ),
                                  $dataSet->GetDataDescription( ), 
                                  $xPosition, $yPosition, $radius, 
                                  PIE_PERCENTAGE_LABEL, FALSE, $skew, $spliceHeight, $spliceDistance );
            
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
            
            //get the coordinates.
            $allCoords = $chart->coordinates( );
            
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
     * Build The Bar Gharph image with given params 
     * and store in upload/pChart directory.
     *
     * @param  array  $params    an assoc array of name/value pairs          
     * @return array  $filesPath created image files Path.
     *
     * @static
     */
    static function barGraph( $params ) 
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
                $shades++;
            }
            $legend = CRM_Utils_Array::value('legend', $chartValues );
            //calculate max scale for graph.
            $maxScale =  ceil( max( $values ) * 1.1 );
            
            require_once 'CRM/Utils/Money.php';
            $formatedMoney = CRM_Utils_Money::format( $maxScale );
            $positions  = imageftbbox( 12, 0, $pChartPath ."tahoma.ttf", $formatedMoney );
            $scaleTextWidth =  $positions[2]-$Positions[0];
            
            //Initialise the co-ordinates.
            $x1    = $scaleTextWidth;
            $y1    = 35;
            $ySize = 300;
            $y2    = $ySize - 30;
            
            //calculate x axis size as per number of months.
            $divisionWidth = 44;
            $x2 = $divisionWidth + $scaleTextWidth + ( count( $chartValues['values'] ) - 1 ) * $divisionWidth;
            $xSize = $x2 + 20;
            
            $dataSet = new pData;
            $dataSet->AddPoint( $values, "Serie1" );
            $dataSet->AddPoint( $names, "Serie2" );
            $dataSet->AddSerie( "Serie1" );
            $dataSet->SetAbsciseLabelSerie( "Serie2" );
            
            //Initialise the graph
            $chart = new pChart( $xSize, $ySize );
            $chart->setFontProperties( $pChartPath ."tahoma.ttf", 8 );
            
            $chart->setGraphArea( $x1, $y1, $x2, $y2 );
            
            //set the y axis scale.
            $chart->setFixedScale( 0, $maxScale, 1 );
            
            $chart->drawFilledRoundedRectangle( 0, ($y1-33), ($x2+20), ($y2+27), 5, 240, 240, 240 );
            $chart->drawRoundedRectangle( 0, ($y1-33), ($x2+20), ($y2+27), 5, 230, 230, 230 );
            
            $chart->drawGraphArea( 255, 255, 255, TRUE );
            $chart->drawScale( $dataSet->GetData( ), $dataSet->GetDataDescription( ),
                               SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2, TRUE, 1, FALSE, $divisionWidth, true );
            
            $chart->drawGrid( 4, TRUE, 230, 230, 230, 50 );
            
            $chart->setFontProperties( $pChartPath.'tahoma.ttf', 8 );
            
            //set colors.
            $chart->setColorShades( $shades, self::$_colors );
            
            //Draw the bar chart
            $chart->drawBarGraph( $dataSet->GetData( ), $dataSet->GetDataDescription( ), TRUE, 80, true );
            
            //get the series values and write at top.
            $chart->setColorPalette( 0, 0, 0, 255 );
            $dataDesc = $dataSet->GetDataDescription( );
            $chart->writeValues( $dataSet->GetData( ), $dataSet->GetDataDescription( ), $dataDesc['Values'] ); 
            
            //Write the title
            if ( $legend ) {
                $chart->setFontProperties( $pChartPath . "tahoma.ttf", 10 );
                $positions  = imageftbbox( 12, 0, $pChartPath ."tahoma.ttf", $legend );
                $scaleTextWidth =  $positions[2]-$Positions[0];
                if ( $scaleTextWidth > $x2 ) {
                    $chart->drawTitle( 5, $y1-15, $legend, 50, 50, 50 );
                } else {
                    $chart->drawTitle( $xSize/2, $y1-15, $legend, 50, 50, 50 );
                }   
            }
            
            $fileName = "pChartByMonth{$chartCount}" . time( ) . '.png';
            $chart->Render( $uploadDirPath . $fileName );
            
            //get the file path.
            $filesValues[$chartIndex]['file_name'] = $uploadDirURL . $fileName;
            
            //get the co-ordinates
            $coords = $chart->coordinates( );
            
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


