<?php
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

mysql_query($sql);

print file_get_contents('stable.txt');
