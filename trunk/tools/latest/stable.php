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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

$user = $pass = false;
require_once 'config.php';
mysql_connect('localhost', $user, $pass);
mysql_select_db('stats');

$collected = array('strings'  => array('hash', 'uf', 'lang', 'version', 'ufv', 'MySQL', 'PHP', 'PPTypes'),
                   'integers' => array('Activity', 'Case', 'Contact', 'Contribution', 'ContributionPage', 'ContributionProduct',
                                       'Discount', 'Event', 'Friend', 'Grant', 'Mailing', 'Membership', 'MembershipBlock',
                                       'Participant', 'Pledge', 'PledgeBlock', 'PriceSetEntity', 'Relationship', 'UFGroup', 'Widget'));

$params = array();

// sanitize for SQL - quote escaped strings and cast the rest to integers
foreach ($collected['strings'] as $param) {
    if (isset($_REQUEST[$param])) $params[$param] = "'" . mysql_real_escape_string($_REQUEST[$param]) . "'";
}
foreach ($collected['integers'] as $param) {
    if (isset($_REQUEST[$param])) $params[$param] = (int) $_REQUEST[$param];
}

$sql = 'INSERT INTO stats (`' . implode('`, `', array_keys($params)) . '`) VALUES (' . implode(', ', $params) . ')';

if ($params['hash']) mysql_query($sql);

print file_get_contents('stable.txt');
