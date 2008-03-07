#!/usr/bin/env php
<?php
error_reporting(E_ALL ^ E_NOTICE);
/* Release version 0.2
 * Written by Dan Coulter (dancoulter@users.sourceforge.net)
 * Project Homepage: http://dancoulter.com/release/
 * Sourceforge Project Page: http://www.sourceforge.net/projects/release/
 * Released under GNU Lesser General Public License (http://www.gnu.org/copyleft/lgpl.html)
 * For more information about this application, please visit http://dancoulter.com/release/
 *     or http://www.sourceforge.net/projects/release/
 *
 *     For installation instructions, open the README.txt file packaged with this
 *     script. If you don't have a copy, you can see it at:
 *     http://www.dancoulter.com/release/README.txt
 *
 */

ini_set('include_path', dirname(__FILE__) . '/PEAR:' . ini_get('include_path'));

require_once "HTTP/Client.php";

$filetypes = array(
    ".gz" => true,
    ".bz2" => true,
    ".zip" => true
);

$settings = parse_ini_file("./release.ini", true);
if (!empty($_SERVER['argv'][2])) {
    $settings['login']['sf_password'] = $_SERVER['argv'][2];
}

if (!empty($settings['application']['svn_path'])) {
    $check_svn_exists = exec("svn list {$settings['application']['svn_path']}/{$settings['application']['svn_release_prefix']}{$_SERVER['argv'][1]}");
    if (strstr($check_svn_exists, "non-existent in that revision")) {
        die("Release not found!\n\n");
    }
    
    //if (empty($_SERVER['argv'][2]))
    
    echo "Generating files from SVN tag... ";
    exec("svn export {$settings['application']['svn_path']}/{$settings['application']['svn_release_prefix']}{$_SERVER['argv'][1]} {$settings['application']['temp_path']}/{$settings['application']['app_name']}-{$_SERVER['argv'][1]}");
    
    chdir($settings['application']['temp_path']);
    
    if ($filetypes['.gz']) {
        exec("tar czf {$settings['application']['app_name']}-{$_SERVER['argv'][1]}.tar.gz {$settings['application']['app_name']}-{$_SERVER['argv'][1]}");
        $files[] = "{$settings['application']['app_name']}-{$_SERVER['argv'][1]}.tar.gz";
    }
    if ($filetypes['.bz2']) {
        exec("tar cjf {$settings['application']['app_name']}-{$_SERVER['argv'][1]}.tar.bz2 {$settings['application']['app_name']}-{$_SERVER['argv'][1]}");
        $files[] = "{$settings['application']['app_name']}-{$_SERVER['argv'][1]}.tar.bz2";
    }
    if ($filetypes['.zip']) {
        exec("zip -r {$settings['application']['app_name']}-{$_SERVER['argv'][1]}.zip {$settings['application']['app_name']}-{$_SERVER['argv'][1]}");
        $files[] = "{$settings['application']['app_name']}-{$_SERVER['argv'][1]}.zip";
    }
    chdir(dirname(__FILE__));
} else {
    echo "Collecting files from specified Folder... ";
    if ($dir = opendir($settings['application']['file_path'])) {
        $filecount = 0;
        while ($file = readdir($dir)) {
            if ($filetypes[substr($file, strrpos($file, "."))] === true) {
                copy($settings['application']['file_path'] . '/' . $file, $settings['application']['temp_path'] . '/' . $file);
                $files[] = $file;
                $filecount++;
            }
        }
        if ($filecount == 0) {
            die("No files were found matching the listed extensions.\n\n");
        }
        closedir($dir);
    } else {
        die("File path directory not found!\n\n");
    }

}

chdir($settings['application']['temp_path']);
$client = new HTTP_Client();

echo "Done\n";
echo "Uploading files... ";
// set up basic connection
$conn_id = ftp_connect("upload.sourceforge.net");

// login with username and password
$login_result = ftp_login($conn_id, "anonymous", "release@dancoulter.com");

// check connection
if ((!$conn_id) || (!$login_result)) {
    exec("rm -R -f *");
	die("FTP connection has failed!\n\n");
}

ftp_chdir($conn_id, "incoming");

// upload the file
//*
foreach ($files as $file) {
	$upload = ftp_put($conn_id, $file, $file, FTP_BINARY);
	if (!$upload) {
        chdir(dirname(__FILE__));
        exec("rm -R -f {$settings['application']['temp_path']}/*");
        die("FTP upload has failed! ({$file})\n\n");
	}
}
//*/
// close the FTP stream
ftp_close($conn_id);

echo "Done\n";

echo "Authenticating to Sourceforge... ";

$post = array(
	"return_to" => "",
	"form_loginname" => $settings['login']['sf_username'],
	"form_pw" => $settings['login']['sf_password'],
	"persistent_login" => "0",
	"login" => "Login"
);
$client->post("https://sourceforge.net/account/login.php", $post);
$rsp = $client->currentResponse();
if ($rsp['code'] != 200) {
    exec("rm -R -f *");
	die ("Login error.\n\n");
}
echo "Done\n";

echo "Creating a new release... ";

$post = array(
	"group_id" => $settings['application']['sf_group_id'],
	"newrelease" => "yes",
	"release_name" => $_SERVER['argv'][1],
	"package_id" => $settings['application']['sf_package_id'],
	"submit" => "Create This Release"
);
//*
$client->post("http://sourceforge.net/project/admin/newrelease.php", $post);
$rsp = $client->currentResponse();
if (!ereg('input type="hidden" name="release_id" value="([0-9]+)"', $rsp['body'], $matches)) {
    exec("rm -R -f *");
	die("Problem creating release\n\n");
}

echo "Done\n";

echo "Uploading release notes and change log... ";

$release_id = $matches[1];
$release_date = strftime("%Y-%m-%d");
$release_name = $_SERVER['argv'][1];

chdir(dirname(__FILE__));

$change_log = file_get_contents($settings['application']['change_log']);
$release_notes = file_get_contents($settings['application']['release_notes']);

$post = array(
	"group_id" => $settings['application']['sf_group_id'],
	"package_id" => $settings['application']['sf_package_id'],
	"release_id" => $release_id,
	"step1" => "1",
	"release_date" => $release_date,
	"release_name" => $release_name,
	"status_id" => "1",
	"new_package_id" => $settings['application']['sf_package_id'],
	"release_notes" => $release_notes,
	"release_changes" => $change_log,
	"preformatted" => "1",
	"submit" => "Submit/Refresh"
);

$client->post("http://sourceforge.net/project/admin/editreleases.php", $post);
$rsp = $client->currentResponse();

echo "Done\n";

echo "Adding files to release... ";

foreach ($files as $file) {
	$post = array(
		"group_id" => $settings['application']['sf_group_id'],
		"package_id" => $settings['application']['sf_package_id'],
		"release_id" => $release_id,
		"step2" => "1",
		"file_list[]" => $file,
		"submit" => "Add Files and/or Refresh View"
	);
	
	$client->post("http://sourceforge.net/project/admin/editreleases.php", $post);
}

echo "Done\n";

echo "Setting file types... ";

$client->get("http://sourceforge.net/project/admin/editreleases.php?package_id={$settings['application']['sf_package_id']}&release_id={$release_id}&group_id={$settings['application']['sf_group_id']}");
$rsp = $client->currentResponse();

$string = $rsp['body'];
//echo "<pre>" . htmlspecialchars($string);
while(ereg('input type="hidden" name="file_id" value="([0-9]+)"', $string, $match)) {
   $id = $match[1];
   $string = str_replace($id, "*", $string);
   $file_ids[] = $id;
}

sort($files);

foreach ($file_ids as $key => $id) {
	$file_matches[$id] = $files[$key];
}

$file_types = array(
	".bz2" => "5001",
	".gz" => "5002",
	".tgz" => "5002",
	".zip" => "5000"
);

foreach ($file_matches as $id => $file) {
	$post = array(
		"group_id" => $settings['application']['sf_group_id'],
		"package_id" => $settings['application']['sf_package_id'],
		"release_id" => $release_id,
		"new_release_id" => $release_id,
		"file_id" => $id,
		"step3" => "1",
		"processor_id" => '8500',
		"type_id" => $file_types[substr($file, strrpos($file, "."))],
		"release_time" => $release_date,
		"submit" => "Update/Refresh"
	);
	$client->post("http://sourceforge.net/project/admin/editreleases.php", $post);
}
echo "Done\n";
echo "Deleting tmp files... ";
chdir(dirname(__FILE__));
exec("rm -R -f {$settings['application']['temp_path']}/*");
echo "Done\n\n";

?>
