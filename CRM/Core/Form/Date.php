<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 * This class contains function to build date-format.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
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
        DATE_dd_mon_yy      = 16;


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
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('yyyy-mm-dd OR yyyymmdd (1998-12-25 OR 19981225)'), self::DATE_yyyy_mm_dd);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('mm/dd/yy OR mm-dd-yy (12/25/98 OR 12-25-98)'), self::DATE_mm_dd_yy);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('mm/dd/yyyy OR mm-dd-yyyy (12/25/1998 OR 12-25-1998)'), self::DATE_mm_dd_yyyy);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('Month dd, yyyy (December 12, 1998)'), self::DATE_Month_dd_yyyy);
        $dateOptions[] = HTML_QuickForm::createElement('radio', null, null, ts('dd-mon-yy (25-Dec-98)'), self::DATE_dd_mon_yy);
        $form->addGroup($dateOptions, 'dateFormats', ts('Date Format'), '<br/>');
        $form->setDefaults(array('dateFormats' => self::DATE_yyyy_mm_dd));
    }

}


?>
