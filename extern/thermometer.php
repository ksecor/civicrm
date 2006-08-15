<?php

  // This work is licensed under the Creative Commons Attribution 2.5 License. 
  // To view a copy of this license, visit 
  // http://creativecommons.org/licenses/by/2.5/ 
  // or send a letter to Creative Commons, 543 Howard Street, 5th Floor, 
  // San Francisco, California, 94105, USA.
  // http://arkie.net/~scripts/thermometer/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * $Id$
 *
 */

/**
 * class to provide a simple thermometer
 */
class CRM_GD_Thermometer {

    function draw( $current, $goal, $width, $height, $font ) {
        $bar = 0.50;

        // create the image
        $image = imagecreate       ( $width, $height );
        $bg    = imagecolorallocate( $image, 255, 255, 255 );
        $fg    = imagecolorallocate( $image, 255, 0  , 0   );
        $tx    = imagecolorallocate( $image, 0  , 0  , 0   );

        //  Build background
        imagefilledrectangle( $image, 0, 0, $width, $height, $bg );

        //  Build bottom bulb
        imagearc( $image, $width / 2, $height - ( $width / 2 ), $width, $width, 0, 360, $fg );
        imagefilltoborder( $image, $width / 2, $height - ( $width / 2 ), $fg, $fg );

        //  Build "Bottom level
        imagefilledrectangle( $image,
                              ( $width / 2 ) - ( ( $width / 2 ) * $bar ),
                             $height - $width,
                             ( $width / 2 ) + ( ( $width / 2 ) * $bar ),
                             $height - ( $width / 2 ),
                             $fg );

        //  Draw Top Border
        imagerectangle( $image,
                        ( $width / 2 ) - ( ( $width / 2 ) * $bar ),
                        0,
                        ( $width / 2 ) + ( ( $width / 2 ) * $bar ),
                        $height - $width,
                        $fg );

        //  Fill to %
        imagefilledrectangle( $image,
                              ( $width / 2 ) - ( ( $width / 2 ) * $bar ),
                              ( $height - $width ) * ( 1 - ( $current / $goal ) ),
                              ( $width / 2 ) + ( ( $width / 2 ) * $bar ),
                              $height - $width,
                              $fg );

        //  Add tic's
        for( $k = 25; $k < 100; $k += 25 ) {
            imagefilledrectangle( $image,
                                  ( $width / 2 ) + ( ( $width / 2 ) * $bar ) - 5,
                                  ( $height - $width ) - ( $height - $width ) * ( $k / 100 ) - 1,
                                  ( $width / 2 ) + ( ( $width / 2 ) * $bar ) - 1,
                                  ( $height - $width ) - ( $height - $width ) * ( $k / 100 ) +1, $tx );


            imagestring( $image, $font,
                         ( $width / 2 ) + ( ( $width / 2 ) * $bar ) + 2,
                         ( ( $height - $width ) - ( $height - $width ) * ( $k / 100 ) ) - ( imagefontheight( $font ) / 2 ),
                         sprintf( "%2d", $k ), $tx );
        }

        // Add % over BULB
        $pct = sprintf( "%d%%", ( $current / $goal ) * 100 );

        imagestring( $image, $font + 2,
                     ( $width / 2 ) - ( ( strlen( $pct ) / 2 ) * imagefontwidth( $font + 2 ) ),
                     ( $height - ( $width / 2 ) ) - ( imagefontheight( $font + 2 ) / 2 ),
                     $pct, $bg );

        // send the image
        header("content-type: image/png");

        imagepng( $image );
    }

}

$w = isset( $_GET['w'] ) ? $_GET['w'] :  60;
$h = isset( $_GET['h'] ) ? $_GET['h'] : 150;
$f = isset( $_GET['f'] ) ? $_GET['f'] :   1;

CRM_GD_Thermometer::draw( $_GET['c'],
                          $_GET['g'],
                          $w, $h, $f );

?>
