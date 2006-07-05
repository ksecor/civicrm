<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 * Class to handle capthca related image and verification
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

class CRM_Utils_PDFlib {
    static function &compose( $fileName,
                             $searchPath,
                             &$values,
                             $echo    = true,
                             $output  = 'College_Prep_App',
                             $creator = 'CiviCRM',
                             $author  = 'http://www.civicrm.org/',
                             $title   = '2006 College Prep Scholarship Application' ) {
        try {
            $pdf = new PDFlib( );
            $pdf->set_parameter( "compatibility", "1.6");

            if ( $pdf->begin_document( '', '' ) == 0 ) {
                CRM_Utils_Error::statusBounce( "PDFlib Error: " . $pdf->get_errmsg( ) );
            }

            $config =& CRM_Core_Config::singleton( );
            $pdf->set_parameter( 'resourcefile', $config->templateDir . '/Quest/pdf/pdflib.upr' );

            /* Set the search path for fonts and PDF files */
            $pdf->set_parameter( 'SearchPath', $searchPath );

            /* This line is required to avoid problems on Japanese systems */
            $pdf->set_parameter( 'hypertextencoding', 'winansi' );

            $pdf->set_info( 'Creator', $creator );
            $pdf->set_info( 'Author' , $author  );
            $pdf->set_info( 'Title'  , $title   );

            $blockContainer = $pdf->open_pdi( $fileName, '', 0 );
            if ( $blockContainer == 0 ) {
                CRM_Utils_System::statusBounce( 'PDFlib Error: ' . $pdf->get_errmsg( ) );
            }

            for ( $i = 1; $i  <= 6; $i++ ) {
                $page = $pdf->open_pdi_page( $blockContainer, $i, '' );
                if ( $page == 0 ) {
                    CRM_Utils_System::statusBounce( 'PDFlib Error: ' . $pdf->get_errmsg( ) );
                }
                
                $pdf->begin_page_ext( 20, 20, '' ); /* dummy page size */
                
                /* This will adjust the page size to the block container's size. */
                $pdf->fit_pdi_page( $page, 0, 0, 'adjustpage' );
                
                $status = array( );
                /* Fill all text blocks with dynamic data */
                foreach ( $values as $key => $value ) {
                    if ( is_array( $value ) ) {
                        continue;
                    }

                    // pdflib does like the forward slash character, hence convert
                    $value = str_replace( '/', '_', $value );

                    $pdf->fill_textblock( $page,
                                          $key,
                                          $value,
                                          'embedding encoding=winansi' );
                }
                
                $pdf->end_page_ext( '' );
                $pdf->close_pdi_page( $page );
            }

            $pdf->end_document( '' );
            $pdf->close_pdi( $blockContainer );

            $buf = $pdf->get_buffer();
            $len = strlen($buf);

            if ( $echo ) {
                header('Content-type: application/pdf');
                header("Content-Length: $len");
                header("Content-Disposition: inline; filename={$output}.pdf");
                echo $buf;
                exit( );
            } else {
                return $buf;
            }
        }
        catch ( PDFlibException $excp ) {
            CRM_Utils_System::statusBounce( 'PDFlib Error: Exception' .
                                          "[" . $excp->get_errnum( ) . "] " . $excp->get_apiname( ) . ": " .
                                          $excp->get_errmsg( ) );
        }
        catch (Exception $excp) {
            CRM_Utils_System::statusBounce( "PDFlib Error: " . $excp->get_errmsg( ) );
        }
    }
}

?>
