<?php

require_once '../../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/I18n.php';
require_once 'CRM/Utils/Date.php';


// assign the weekday and month names

$DN  = CRM_Utils_Date::getFullWeekdayNames();
$SDN = CRM_Utils_Date::getAbbrWeekdayNames();
$MN  = CRM_Utils_Date::getFullMonthNames();
$SMN = CRM_Utils_Date::getAbbrMonthNames();



// assign the strings hash

$TT['INFO'] = ts('About the calendar');

$TT['ABOUT'] = "DHTML Date/Time Selector\n(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" . 
ts('For latest version visit: %1', array(1 => 'http://www.dynarch.com/projects/calendar/')) . "\n" .
ts('Distributed under GNU LGPL. See %1 for details.', array(1 => 'http://gnu.org/licenses/lgpl.html')) . "\n\n" .
ts('Date selection:') . "\n" .
ts('- use the \xab, \xbb buttons to select year,') . "\n" .
ts('- use the \u2039, \u203a buttons to select month,') . "\n" .
ts('- hold mouse button on any of the above buttons for faster selection.');

$TT['ABOUT_TIME'] = "\n\n" . ts('Time selection:') . "\n" .
ts('- click on any of the time parts to increase it') . "\n" .
ts('- or Shift-click to decrease it') . "\n" .
ts('- or click and drag for faster selection.');

$holdForMenu = ts('hold for menu');

$TT['PREV_YEAR']    = ts('Prev. year') . " ($holdForMenu)";
$TT['PREV_MONTH']   = ts('Prev. month') . " ($holdForMenu)";
$TT['GO_TODAY']     = ts('Go Today');
$TT['NEXT_MONTH']   = ts('Next month') . " ($holdForMenu)";
$TT['NEXT_YEAR']    = ts('Next year') . " ($holdForMenu)";
$TT['SEL_DATE']     = ts('Select date');
$TT['DRAG_TO_MOVE'] = ts('Drag to move');
$TT['PART_TODAY']   = ' (' . ts('today') . ')';

$TT['DAY_FIRST'] = ts('Display %s first');
$TT['WEEKEND']   = '0,6';
$TT['CLOSE']     = ts('Close');
$TT['TODAY']     = ts('Today');
$TT['TIME_PART'] = ts('(Shift-)Click or drag to change value');

$TT['DEF_DATE_FORMAT'] = '%Y-%m-%d';
$TT['TT_DATE_FORMAT']  = '%a, %b %e';

$TT['WK']   = ts('wk');
$TT['TIME'] = ts('Time:');


// generate the final JavaScript file
header('Content-Type: application/x-javascript; charset=utf-8');

// cache this file properly
header('Expires: ' . date('r', time() + 86400));
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: max_age=86400');

print 'Calendar._DN = new Array("' . implode('", "', $DN) . "\",\"$DN[0]\");\n";
print 'Calendar._SDN = new Array("' . implode('", "', $SDN) . "\",\"$SDN[0]\");\n";
print 'Calendar._MN = new Array("' . implode('", "', $MN) . "\");\n";
print 'Calendar._SMN = new Array("' . implode('", "', $SMN) . "\");\n";
print "Calendar._FD = 0;\n";

print "Calendar._TT = {};\n";
foreach ($TT as $key => $value) {
    $value = str_replace("\n", '\n', $value);
    print "Calendar._TT[\"$key\"] = \"$value\";\n";
}


