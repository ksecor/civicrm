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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

Class CRM_Core_Form_Date
{

    /**
     * various Date Formats
     */
    const
        DATE_yyyy_mm_dd     = 1,
        DATE_mm_dd_yy       = 2,
        DATE_mm_dd_yyyy     = 4,
        DATE_Month_dd_yyyy  = 8,
        DATE_dd_mon_yy      = 16,
        DATE_dd_mm_yyyy     = 32;
    

    /**
     * This function is to build the date-format form
     *
     * @param Object  $form   the form object that we are operating on
     * 
     * @static
     * @access public
     */
    static function buildAllowedDateFormats( &$form ) {
        
        $dateOptions = array();
        
        require_once "CRM/Utils/System.php";
        if ( CRM_Utils_System::getClassName( $form ) == 'CRM_Activity_Import_Form_UploadFile' ) {
            $dateText = ts('yyyy-mm-dd OR yyyymmdd OR yyyymmdd hh:mm (1998-12-25 OR 19981225 OR 19981225 10:30) OR (2008-9-1 OR 20080901 10:30)');
        } else {
            $dateText = ts('yyyy-mm-dd OR yyyymmdd (1998-12-25 OR 19981225) OR (2008-9-1 OR 20080901)');
        }

        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, $dateText, self::DATE_yyyy_mm_dd);

        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('mm/dd/yy OR mm-dd-yy (12/25/98 OR 12-25-98) OR (9/1/08 OR 9-1-08)'), self::DATE_mm_dd_yy);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('mm/dd/yyyy OR mm-dd-yyyy (12/25/1998 OR 12-25-1998) OR (9/1/2008 OR 9-1-2008)'), self::DATE_mm_dd_yyyy);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('Month dd, yyyy (December 12, 1998)'), self::DATE_Month_dd_yyyy);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('dd-mon-yy OR dd/mm/yy (25-Dec-98 OR 25/12/98)'), self::DATE_dd_mon_yy);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('dd/mm/yyyy (25/12/1998) OR (1/9/2008)'), self::DATE_dd_mm_yyyy);
        $form->addGroup($dateOptions, 'dateFormats', ts('Date Format'), '<br/>');
        $form->setDefaults(array('dateFormats' => self::DATE_yyyy_mm_dd));
    }
    
}



