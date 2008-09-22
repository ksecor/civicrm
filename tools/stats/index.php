<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>CiviCRM usage statistics</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="Content-Language" content="en" />
  <meta name="Author" content="Piotr Szotkowski" />
</head>
<body>
<h1>CiviCRM usage statistics</h1>
<?php
$user = $pass = false;
require_once 'config.php';
mysql_connect('localhost', $user, $pass);
mysql_select_db('stats');

require_once 'graphs.php';

$charts = array(
    array('title' => 'Distinct installations',
          'query' => 'SELECT COUNT(DISTINCT hash) data, YEAR(time) year, MONTH(time) month FROM stats GROUP BY year, month ORDER BY year, month',
          'type'  => 'trend'),
    array('title' => 'UF usage',
          'query' => 'SELECT COUNT(DISTINCT hash) data, YEAR(time) year, MONTH(time) month, uf compare FROM stats GROUP BY year, month, uf ORDER BY year, month, uf',
          'type'  => 'compare'),
);

foreach ($charts as $chart) {
    print "<h2>{$chart['title']}</h2>";
    switch ($chart['type']) {
    case 'trend':
        print '<p><img src="' . trend($chart['query']) . '" /></p>'; break;
    case 'compare':
        $last = '';
        print "<p><img src='" . compare($chart['query'], $last) . "' /> <img src='$last' /></p>"; break;
    }
}
?>
</body>
</html>
