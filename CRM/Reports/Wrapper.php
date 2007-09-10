<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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


require_once('./birtGlobals.php');
   
// Include Zend Birt report design API
require_once(BIRT_DIR . '/Zend/Birt_Report/Zend_Birt_Report_Design.php');
   
// Create the report design object from the rptdesign file (usecase1.rptdesign)
$birt = new Zend_Birt_Report_Design(dirname(__FILE__) . '/usecase1.rptdesign');
   
// Set parameter sample, in this case:
// Show only order number 10101
$birt->setParameter('OrderNumber','10101');
   
// "BIRT_TMP_DIR" represents the path to a writable directory, and "birtImage.php?image=" is
// a php script that displays the image from its original location
$birt->setImageConfiguration(BIRT_TMP_DIR, 'birtImage.php?image=');

// Render report.
// BIRT_REPORT_FORMAT_HTML - render an html report
echo $birt->renderReport(BIRT_REPORT_FORMAT_HTML);
?> 