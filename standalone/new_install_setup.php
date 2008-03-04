<html>
  <head>
    <title>CiviCRM Installation - Database Setup</title>
  </head>
  <body>
    <h1>CiviCRM Installation - Database Setup</h1>

    <p>Welcome to CiviCRM Standalone! First we need to setup your database connection and put some data in it. Fill out the form below and click &quot;Continue&quot;.</p>
    
    <form name="db_setup" action="db_setup.php" method="post">
        <label for="db_host">Database host:</label> <input id="db_host" type="text" name="db_host" value="localhost"/><br/><br/>
        <label for="db_admin_user">Database admin username:</label> <input id="db_admin_user" type="text" name="db_admin_user" value="root" size="10"/><br/><br/>
        <label for="db_admin_pass" style="font-weight: bold">*** Database admin password:</label> <input id="db_admin_pass" type="password" name="db_admin_pass"/><br/><br/>
        <input type="submit" name="submit" value="Continue >>"/><br/>
        <hr/>
        <h2>Advanced Options</h2>
        <p>If you don't know what these are, you probably don't need to change them.</p>
        <h3>CiviCRM environment</h3>
<?php
$crmRoot = dirname( realpath( $_SERVER['SCRIPT_FILENAME'] ) );
$crmRoot = str_replace( '\\', '/', $crmRoot );
$crmRootParts = explode( '/', $crmRoot );
$crmRootParts = array_slice( $crmRootParts, 0, -1 );
$crmRoot = implode( '/', $crmRootParts );
$filesDir = "$crmRoot/standalone/files";

$baseURL = dirname( $_SERVER['REQUEST_URI'] );
$baseURL = 'http://' . $_SERVER['HTTP_HOST'] . "$baseURL/";

$mysqlPath = dirname( exec( 'which mysql' ) );
$mysqlSocket = exec( "$mysqlPath/mysql_config --socket" );

        <label for="crm_root">CiviCRM root:</label> <input id="crm_root" type="text" name="crm_root" size="50" value="<?php print $crmRoot ?>"/><br/><br/>
        <label for="files_dir">Persistent files directory:</label> <input id="files_dir" type="text" name="files_dir" size="50" value="<?php print $filesDir ?>"/><br/><span style="font-size: small">(compiled templates, uploaded files, etc.--many people like to put these outside their CiviCRM installation directory)</span><br/><br/>
        <label for="base_url">Base URL of CiviCRM:</label> <input id="base_url" type="text" name="base_url" value="<?php print $baseURL ?>" size="50"/><br/><br/>
        <h3>Database connection</h3>
        <h4>Local database server</h4>
        &nbsp;&nbsp;&nbsp;&nbsp;<label for="socket_file">Socket file (for local connections):</label> <input type="text" name="socket_file" size="50" value="<?php print $mysqlSocket ?>"/><br/><br/>
        <h4>Remote database server</h4>
        &nbsp;&nbsp;&nbsp;&nbsp;<label for="port_number">Port:</label> <input type="text" name="port_number" size="5" value="3306"/><br/><br/>
	<h4>Other options</h4>
        <label for="mysql_path">Path to mysql bin directory:</label> <input id="mysql_path" type="text" name="mysql_path" value="<?php print $mysqlPath ?>" size="50"/><br/><br/>
        <label for="db_user">DB Username for CiviCRM to use:</label> <input id="db_user" type="text" name="db_user" value="civicrm"/><br/><br/>
        <label for="db_name">Name of the schema for CiviCRM to use:</label> <input id="db_name" type="text" name="db_name" value="civicrm"/><br/><br/>
        <input type="submit" name="submit" value="Continue >>"/><br/>
    </form>
  </body>
</html>
