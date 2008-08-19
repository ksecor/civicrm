<?php

/**
 * Note that this installer has been based of the SilverStripe installer.
 * You can get more information from the SilverStripe Website at
 * http://www.silverstripe.com/. Please check 
 * http://www.silverstripe.com/licensing for licensing details.
 *
 * Copyright (c) 2006-7, SilverStripe Limited - www.silverstripe.com
 * All rights reserved.
 *
 * Changes and modifications (c) 2007 by CiviCRM LLC
 *
 */

/**
 * CiviCRM Installer
 */

ini_set('max_execution_time', 300);

// set installation type - drupal / standalone
session_start();

if ( isset($_GET['mode']) ) {
    $_SESSION['install_type'] = $_GET['mode'];
} else {
    if (! isset($_SESSION['install_type'])) {
        $_SESSION['install_type'] = "drupal";
    }
}

global $installType;
$installType = strtolower($_SESSION['install_type']);

if ( ! in_array($installType, array('drupal', 'standalone')) ) {
    $errorTitle = "Oops! Unsupported installation mode";
    $errorMsg   = "";
    errorDisplayPage( $errorTitle, $errorMsg );
}



if ( $installType == 'drupal' ) {
    // do not check 'sites/all/modules' only since it could be a multi-site
    // install. Rather check for existance of sites & modules in the url
    if ( ! preg_match('/sites\/[a-zA-Z0-9_.]+\/modules/', $_SERVER['SCRIPT_FILENAME']) ) {
        $errorTitle = "Oops! Please Correct Your Install Location";
        $errorMsg = "Please untar (uncompress) your downloaded copy of CiviCRM in the <strong>sites/all/modules</strong> directory below your Drupal root directory. Refer to the online <a href='http://wiki.civicrm.org/confluence//x/mQ8' target='_blank' title='Opens Installation Documentation in a new window.'>Installation Guide</a> for more information.<p>If you want to setup / install a <strong>Standalone CiviCRM</strong> version (i.e. not a Drupal or Joomla module), <a href=\"?mode=standalone\">click here</a>.</p>";
        errorDisplayPage( $errorTitle, $errorMsg );
    }
}

// Load civicrm database config
if(isset($_REQUEST['mysql'])) {
	$databaseConfig = $_REQUEST['mysql'];
} else {
	$databaseConfig = array(
                            "server"   => "localhost",
                            "username" => "civicrm",
                            "password" => "",
                            "database" => "civicrm",
                            );
}

if ( $installType == 'drupal' ) {
    // Load drupal database config
    if(isset($_REQUEST['drupal'])) {
        $drupalConfig = $_REQUEST['drupal'];
    } else {
        $drupalConfig = array(
                              "server"   => "localhost",
                              "username" => "civicrm",
                              "password" => "",
                              "database" => "drupal",
                              );
    }
}

$loadGenerated = 0;
if ( isset($_REQUEST['loadGenerated'] ) ) {
    $loadGenerated = 1;
}

global $crmPath;
$crmPath = dirname ( dirname( $_SERVER['SCRIPT_FILENAME'] ) );

if ( $installType == 'drupal' ) {
    global $cmsPath;
    $cmsPath = dirname( dirname( dirname( dirname( $crmPath ) ) ) );
    $siteDir = getSiteDir( $_SERVER['SCRIPT_FILENAME'] );
    $alreadyInstalled = file_exists( $cmsPath  . DIRECTORY_SEPARATOR .
                                     'sites'   . DIRECTORY_SEPARATOR .
                                     $siteDir  . DIRECTORY_SEPARATOR .
                                     'civicrm.settings.php' );
} elseif ( $installType == 'standalone' ) {
    $alreadyInstalled = file_exists( $crmPath     . DIRECTORY_SEPARATOR .
                                     'standalone' . DIRECTORY_SEPARATOR .
                                     'civicrm.settings.php' );
}

// Exit with error if CiviCRM has already been installed.
if ($alreadyInstalled ) {
    $errorTitle = "Oops! CiviCRM is Already Installed";
    if ( $installType == 'drupal' ) {
        $errorMsg = "CiviCRM has already been installed in this Drupal site. <ul><li>To <strong>start over</strong>, you must delete or rename the existing CiviCRM settings file - <strong>civicrm.settings.php</strong> - from <strong>[your Drupal root directory]/sites/default</strong>.</li><li>To <strong>upgrade an existing installation</strong>, refer to the online <a href='http://wiki.civicrm.org/confluence//x/mQ8' target='_blank' title='Opens Installation Documentation in a new window.'>Installation Guide</a>.</li></ul>";
    } elseif ( $installType == 'standalone' ) {
        $errorMsg = "Standalone CiviCRM has already been installed. <ul><li>To <strong>start over</strong>, you must delete or rename the existing CiviCRM settings file - <strong>civicrm.settings.php</strong> - from <strong>[your CiviCRM root directory]/standalone</strong>.</li><li>To <strong>upgrade an existing installation</strong>, refer to the online <a href='http://wiki.civicrm.org/confluence//x/mQ8' target='_blank' title='Opens Installation Documentation in a new window.'>Installation Guide</a>.</li></ul>";
    }
    errorDisplayPage( $errorTitle, $errorMsg );
}

$versionFile = $crmPath . DIRECTORY_SEPARATOR . 'civicrm-version.txt';
if(file_exists($versionFile)) {
    $civicrm_version = file_get_contents($versionFile);
} else {
	$civicrm_version = 'unknown';
}

if ( $installType == 'drupal' ) {
    // Ensure that they have downloaded the correct version of CiviCRM
    if ( ( strpos( $civicrm_version, 'PHP5'   ) === false) ||
         ( strpos( $civicrm_version, 'Drupal' ) === false ) ) {
        $errorTitle = "Oops! Incorrect CiviCRM Version";
        $errorMsg = "This installer can only be used for the Drupal PHP5 version of CiviCRM. Refer to the online <a href='http://wiki.civicrm.org/confluence//x/mQ8' target='_blank' title='Opens Installation Documentation in a new window.'>Installation Guide</a> for information about installing CiviCRM on PHP4 servers OR installing CiviCRM for Joomla!";
        errorDisplayPage( $errorTitle, $errorMsg );
    }
    
    $drupalVersionFile = implode(DIRECTORY_SEPARATOR, array($cmsPath, 'modules', 'system', 'system.module'));
    
    if ( file_exists( $drupalVersionFile ) ) {
        require_once $drupalVersionFile;
    }
    
    if ( !defined('VERSION') or version_compare(VERSION, '6.0') < 0 ) {
        $errorTitle = "Oops! Incorrect Drupal Version";
        $errorMsg = "This version of CiviCRM can only be used with Drupal 6.x. Please ensure that '$drupalVersionFile' exists if you are running Drupal 6.0 and over. Refer to the online <a href='http://wiki.civicrm.org/confluence//x/mQ8' target='_blank' title='Opens Installation Documentation in a new window.'>Installation Guide</a> for information about installing CiviCRM.";
        errorDisplayPage( $errorTitle, $errorMsg );
    }
}

// Check requirements
$req = new InstallRequirements();
$req->check();

if($req->hasErrors()) {
	$hasErrorOtherThanDatabase = true;
}

if($databaseConfig) {
	$dbReq = new InstallRequirements();
	$dbReq->checkdatabase($databaseConfig, 'CiviCRM');
    if ( $installType == 'drupal' ) {
        $dbReq->checkdatabase($drupalConfig, 'Drupal');
    }
}

// Actual processor
if(isset($_REQUEST['go']) && !$req->hasErrors() && !$dbReq->hasErrors()) {
	// Confirm before reinstalling
	if(!isset($_REQUEST['force_reinstall']) && $alreadyInstalled) {
		include('template.html');
	} else {
		$inst = new Installer();
		$inst->install($_REQUEST);
	}

    // Show the config form
} else {
	include('template.html');	
}

/**
 * This class checks requirements
 * Each of the requireXXX functions takes an argument which gives a user description of the test.  It's an array
 * of 3 parts:
 *  $description[0] - The test catetgory
 *  $description[1] - The test title
 *  $description[2] - The test error to show, if it goes wrong
 */
 
class InstallRequirements {
	var $errors, $warnings, $tests;
	
	/**
	 * Just check that the database configuration is okay
	 */
	function checkdatabase($databaseConfig, $dbName) {
        if($this->requireFunction('mysql_connect', array("PHP Configuration", "MySQL support", "MySQL support not included in PHP."))) {
            $this->requireMySQLServer($databaseConfig['server'], array("MySQL $dbName Configuration", "Does the server exist", 
                                                                       "Can't find the a MySQL server on '$databaseConfig[server]'", $databaseConfig['server']));
            if($this->requireMysqlConnection($databaseConfig['server'], $databaseConfig['username'], $databaseConfig['password'], 
                                             array("MySQL $dbName Configuration", "Are the access credentials correct", "That username/password doesn't work"))) {
                @$this->requireMySQLVersion("5.0", array("MySQL $dbName Configuration", "MySQL version at least 5.0", "MySQL version 5.0 is required, you only have ", "MySQL " . mysql_get_server_info()));
            }
            $onlyRequire = ( $dbName == 'Drupal' ) ? true : false;
            $this->requireDatabaseOrCreatePermissions($databaseConfig['server'],
                                                      $databaseConfig['username'], 
                                                      $databaseConfig['password'], 
                                                      $databaseConfig['database'], 
                                                      array("MySQL $dbName Configuration",
                                                            "Can I access/create the database",
                                                            "I can't create new databases and the database '$databaseConfig[database]' doesn't exist"),
                                                      $onlyRequire );
            if ( $dbName != 'Drupal' ) {
                $this->requireMySQLInnoDB($databaseConfig['server'],
                                          $databaseConfig['username'],
                                          $databaseConfig['password'],
                                          $databaseConfig['database'], 
                                          array("MySQL $dbName Configuration",
                                                "Can I access/create InnoDB tables in the database",
                                                "Unable to create InnoDB tables. MySQL InnoDB support is required for CiviCRM but is either not available or not enabled in this MySQL database server." ) );
            }
        }
	}
	
	
	/**
	 * Check everything except the database
	 */
	function check() {
        global $crmPath, $installType;

		$this->errors = null;
		
        $this->requirePHPVersion('5.2.1', array("PHP Configuration", "PHP5 installed", null, "PHP version " . phpversion()));

		// Check that we can identify the root folder successfully
		$this->requireFile($crmPath . DIRECTORY_SEPARATOR . 'README.txt',
                           array("File permissions",
                                 "Does the webserver know where files are stored?", 
                                 "The webserver isn't letting me identify where files are stored.",
                                 $this->getBaseDir()
                                 ),
                           true );		
        
        $requiredDirectories = array( 'CRM', 'packages', 'templates', 'js', 'api', 'i', 'sql' );
        foreach ( $requiredDirectories as $dir ) {
            $this->requireFile( $crmPath . DIRECTORY_SEPARATOR . $dir, array("File permissions", "$dir folder exists", "There is no $dir folder" ), true );
        }

        if ( $installType == 'standalone' ) {
            // make sure that we can write to standalone and standalone/files 
            $writableDirectories = array( 'standalone' );
            foreach ( $writableDirectories as $dir ) {
                $this->requireWriteable( $crmPath . DIRECTORY_SEPARATOR . $dir,
                                         array("File permissions", "Is the $dir folder writeable?", null ),
                                         true );
            }
        }

        if ( $installType == 'drupal' ) {
            global $cmsPath;
            $siteDir = getSiteDir( $_SERVER['SCRIPT_FILENAME'] );

            // make sure that we can write to sites/default and files/
            $writableDirectories = array( 'sites' . DIRECTORY_SEPARATOR . $siteDir . DIRECTORY_SEPARATOR . 'files', 
                                          'sites' . DIRECTORY_SEPARATOR . $siteDir );
            foreach ( $writableDirectories as $dir ) {
                $this->requireWriteable( $cmsPath . DIRECTORY_SEPARATOR . $dir,
                                         array("File permissions", "Is the $dir folder writeable?", null ),
                                         true );
            }
        }

		// Check for rewriting
		$webserver = strip_tags(trim($_SERVER['SERVER_SIGNATURE']));
		if($webserver == '') {
			$webserver = "I can't tell what webserver you are running";
		}
		
		// Check for $_SERVER configuration
		$this->requireServerVariables(array('SCRIPT_NAME','HTTP_HOST','SCRIPT_FILENAME'), array("Webserver config", "Recognised webserver", "You seem to be using an unsupported webserver.  The server variables SCRIPT_NAME, HTTP_HOST, SCRIPT_FILENAME need to be set."));
		
		// Check for MySQL support
		$this->requireFunction('mysql_connect', array("PHP Configuration", "MySQL support", "MySQL support not included in PHP."));
		
		// Check memory allocation
		$this->requireMemory(32*1024*1024, 64*1024*1024, array("PHP Configuration", "Memory allocated (PHP config option 'memory_limit')", "CiviCRM needs a minimum of 32M allocated to PHP, but recommends 64M.", ini_get("memory_limit")));
		
		return $this->errors;
	}
	
	function suggestPHPSetting($settingName, $settingValues, $testDetails) {
		$this->testing($testDetails);
		
		$val = ini_get($settingName);
		if(!in_array($val, $settingValues) && $val != $settingValues) {
			$testDetails[2] = "$settingName is set to '$val' in php.ini.  $testDetails[2]";
			$this->warning($testDetails);
		}
	}
	
	function requireMemory($min, $recommended, $testDetails) {
		$this->testing($testDetails);
		$mem = $this->getPHPMemory();

		if($mem < $min && $mem > 0) {
			$testDetails[2] .= " You only have " . ini_get("memory_limit") . " allocated";
			$this->error($testDetails);
		} else if($mem < $recommended && $mem > 0) {
			$testDetails[2] .= " You only have " . ini_get("memory_limit") . " allocated";
			$this->warning($testDetails);
		} elseif($mem == 0) {
			$testDetails[2] .= " We can't determine how much memory you have allocated. Install only if you're sure you've allocated at least 20 MB.";
			$this->warning($testDetails);
		}
	}
	
	function getPHPMemory() {
		$memString = ini_get("memory_limit");

		switch(strtolower(substr($memString,-1))) {
        case "k":
            return round(substr($memString,0,-1)*1024);

        case "m":
            return round(substr($memString,0,-1)*1024*1024);
			
        case "g":
            return round(substr($memString,0,-1)*1024*1024*1024);
			
        default:
            return round($memString);
		}
	}
	
	function listErrors() {
		if($this->errors) {
			echo "<p>The following problems are preventing me from installing CiviCRM:</p>";
			foreach($this->errors as $error) {
				echo "<li>" . htmlentities($error) . "</li>";
			}
		}
	}
	
	function showTable($section = null) {
		if($section) {
			$tests = $this->tests[$section];
			echo "<table class=\"testResults\" width=\"100%\">";
			foreach($tests as $test => $result) {
				echo "<tr class=\"$result[0]\"><td>$test</td><td>" . nl2br(htmlentities($result[1])) . "</td></tr>";
			}
			echo "</table>";
			
		} else {
			foreach($this->tests as $section => $tests) {
				echo "<h3>$section</h3>";
				echo "<table class=\"testResults\" width=\"100%\">";
				
				foreach($tests as $test => $result) {
					echo "<tr class=\"$result[0]\"><td>$test</td><td>" . nl2br(htmlentities($result[1])) . "</td></tr>";
				}
				echo "</table>";
			}		
		}
	}
	
	function requireFunction($funcName, $testDetails) {
		$this->testing($testDetails);
		if(!function_exists($funcName)) $this->error($testDetails);
		else return true;
	}
	
	function requirePHPVersion($version, $testDetails) {

		$this->testing($testDetails);
		
		list($reqA, $reqB, $reqC) = explode('.', $version);
		list($a, $b, $c) = explode('.', phpversion());
		$c = ereg_replace('-.*$','',$c);
		
		if($a > $reqA) return true;
		if($a == $reqA && $b > $reqB) return true;
		if($a == $reqA && $b == $reqB && $c >= $reqC) return true;

		if(!$testDetails[2]) {
			if($a < $reqA) {
				$testDetails[2] = "You need PHP version $version or later, only $a.$b.$c is installed.  Unfortunately PHP$a and PHP$reqA have some incompatabilities, so if you are on a your web-host may need to move you to a different server.   Some software doesn't work with PHP5 and so upgrading a shared server could be problematic.";
			} else {
				$testDetails[2] = "You need PHP version $version or later, only $a.$b.$c is installed.  Please upgrade your server, or ask your web-host to do so.";
			}
		}
	
		$this->error($testDetails);
	}
	
	function requireFile($filename, $testDetails, $absolute = false) {
		$this->testing($testDetails);
        if ( ! $absolute ) {
            $filename = $this->getBaseDir() . $filename;
        }
		if(!file_exists($filename)) {
			$testDetails[2] .= " (file '$filename' not found)";
			$this->error($testDetails);
		}
	}
	function requireNoFile($filename, $testDetails) {
		$this->testing($testDetails);
		$filename = $this->getBaseDir() . $filename;
		if(file_exists($filename)) {
			$testDetails[2] .= " (file '$filename' found)";
			$this->error($testDetails);
		}
	}
	function moveFileOutOfTheWay($filename, $testDetails) {
		$this->testing($testDetails);
		$filename = $this->getBaseDir() . $filename;
		if(file_exists($filename)) {
			if(file_exists("$filename.bak")) rm("$filename.bak");
			rename($filename, "$filename.bak");
		}
	}
	
	function requireWriteable($filename, $testDetails, $absolute = false) {
		$this->testing($testDetails);
        if ( ! $absolute ) {
            $filename = $this->getBaseDir() . $filename;
        }
		
		if(!is_writeable($filename)) {
            $name = null;
            if ( function_exists( 'posix_getpwuid' ) ) {
                $user = posix_getpwuid(posix_geteuid());
                $name = '- ' . $user['name'] . ' -';
            }

            if ( ! isset( $testDetails[2] ) ) {
                $testDetails[2] = null;
            }
			$testDetails[2] .= "The user account used by your web-server $name needs to be granted write access to the following directory in order to configure the CiviCRM settings file:\n$filename";
			$this->error($testDetails);
		}
	}
	function requireApacheModule($moduleName, $testDetails) {
		$this->testing($testDetails);
		if(!in_array($moduleName, apache_get_modules())) $this->error($testDetails);
	}
		
	function requireMysqlConnection($server, $username, $password, $testDetails) {
		$this->testing($testDetails);
		$conn = @mysql_connect($server, $username, $password);

		if($conn) {
			return true;
		} else {
			$testDetails[2] .= ": " . mysql_error();
			$this->error($testDetails);
		}
	}
	
	function requireMySQLServer($server, $testDetails) {
		$this->testing($testDetails);
		$conn = @mysql_connect($server, null, null);

		if($conn || mysql_errno() < 2000) {
			return true;
		} else {
			$testDetails[2] .= ": " . mysql_error();
			$this->error($testDetails);
		}
	}
	
	function requireMySQLVersion($version, $testDetails) {
		$this->testing($testDetails);
		
		if(!mysql_get_server_info()) {
			$testDetails[2] = 'Cannot determine the version of MySQL installed. Please ensure at least version 4.1 is installed.';
			$this->warning($testDetails);
		} else {
			list($majorRequested, $minorRequested) = explode('.', $version);
			list($majorHas, $minorHas) = explode('.', mysql_get_server_info());
			
			if(($majorHas > $majorRequested) || ($majorHas == $majorRequested && $minorHas >= $minorRequested)) {
				return true;
			} else {
				$testDetails[2] .= "{$majorHas}.{$minorHas}.";
				$this->error($testDetails);
			}
		}
	}

    function requireMySQLInnoDB( $server, $username, $password, $database, $testDetails) {
		$this->testing($testDetails);
		$conn = @mysql_connect($server, $username, $password);
		if ( ! $conn ) {
            $testDetails[2] .= ' Could not determine if mysql has innodb support. Assuming no';
            $this->error($testDetails);
            return;
        }

        $result = mysql_query( "SHOW variables like 'have_innodb'", $conn );
        if ( $result ) {
            $values = mysql_fetch_row( $result );
            if ( strtolower( $values[1] ) != 'yes' ) {
                $this->error($testDetails);
            } else {
                $testDetails[3] = 'MySQL server does have innodb support';
            }
        } else {
            $testDetails[2] .= ' Could not determine if mysql has innodb support. Assuming no';
        }
    }

	function requireDatabaseOrCreatePermissions($server, $username, $password, $database, $testDetails, $onlyRequire = false) {
		$this->testing($testDetails);
		$conn = @mysql_connect($server, $username, $password);

		$okay = null;
		if(@mysql_select_db($database)) {
			$okay = "Database '$database' exists";
        } else if ( $onlyRequire ) {
            $testDetails[2] = 'The database does not exist';
            $this->error($testDetails);
            return;
        } else {
			if(@mysql_query("CREATE DATABASE testing123")) {
				mysql_query("DROP DATABASE testing123");
				$okay = "Able to create a new database";

			} else {
				$testDetails[2] .= " (user '$username' doesn't have CREATE DATABASE permissions.)";
				$this->error($testDetails);
				return;
			}
		}
		
		if($okay) {
			$testDetails[3] = $okay;
			$this->testing($testDetails);
		}

	}
	
	function requireServerVariables($varNames, $errorMessage) {
		//$this->testing($testDetails);
		foreach($varNames as $varName) {
			if(!$_SERVER[$varName]) $missing[] = '$_SERVER[' . $varName . ']';
		}
		if(!isset($missing)) {
			return true;
		} else {
			$testDetails[2] .= " (the following PHP variables are missing: " . implode(", ", $missing) . ")";
			$this->error($testDetails);
		}
	}
	
	function isRunningApache($testDetails) {
		$this->testing($testDetails);
		if(function_exists('apache_get_modules') || stristr($_SERVER['SERVER_SIGNATURE'], 'Apache'))
			return true;
		
		$this->warning($testDetails);
		return false;
	}


	function getBaseDir() {
		return dirname($_SERVER['SCRIPT_FILENAME']) . '/';
	}
	
	function testing($testDetails) {
		if(!$testDetails) return;
		
		$section = $testDetails[0];
		$test = $testDetails[1];
		
		$message = "OK";
		if(isset($testDetails[3])) $message .= " ($testDetails[3])";

		$this->tests[$section][$test] = array("good", $message);
	}
	
	function error($testDetails) {
		$section = $testDetails[0];
		$test = $testDetails[1];

		$this->tests[$section][$test] = array("error", $testDetails[2]);
		$this->errors[] = $testDetails;

	}
	function warning($testDetails) {
		$section = $testDetails[0];
		$test = $testDetails[1];


		$this->tests[$section][$test] = array("warning", $testDetails[2]);
		$this->warnings[] = $testDetails;
	}
	
	function hasErrors() {
		return sizeof($this->errors);
	}
	function hasWarnings() {
		return sizeof($this->warnings);
	}
	
}

class Installer extends InstallRequirements {
    function createDatabaseIfNotExists( $server, $username, $password, $database ) {
		$conn = @mysql_connect($server, $username, $password);
		
		if(@mysql_select_db($database)) {
            // skip if database already present
            return;
		}
        
        if (@mysql_query("CREATE DATABASE $database")) {
        } else {
            $errorTitle = "Oops! Could not create Database $database";
            $errorMsg = "We encountered an error when attempting to create the database. Please check your mysql server permissions and the database name and try again.";
            errorDisplayPage( $errorTitle, $errorMsg );
        }
    }

	function install($config) {
            ?>
            <h1>Installing CiviCRM...</h1>
                 <p>I am now running through the installation steps (this should take a few minutes)</p>
                 <p>If you receive a fatal error, refresh this page to continue the installation
                 <?php
                 flush();

		// Load the sapphire runtime
		echo "<li>Building database schema and setup files...</li>";
		flush();

        // create database if does not exists
        $this->createDatabaseIfNotExists( $config['mysql']['server'],
                                          $config['mysql']['username'],
                                          $config['mysql']['password'],
                                          $config['mysql']['database'] );
		// Build database
        require_once 'civicrm.php';
        civicrm_main( $config );

        if(! $this->errors) {
            global $installType;

            if ( $installType == 'drupal' ) {
                $drupalURL     = civicrm_cms_base( );
                echo "<p>Installed CiviCRM successfully.  I will now try and direct you to 
					<a href=\"$drupalURL\">Drupal Home</a> to confirm that the installation was successful.</p>
					<script>setTimeout(function() { window.location.href = '$drupalURL'; }, 1000);</script>
					";
            } elseif ( $installType == 'standalone' ) {
                $standaloneURL = civicrm_cms_base( ) . 'standalone/';
                echo "<p>Installed CiviCRM successfully.  I will now try and direct you to 
					<a href=\"$standaloneURL\">Standalone Home</a> to confirm that the installation was successful.</p>
					<script>setTimeout(function() { window.location.href = '$standaloneURL'; }, 1000);</script>
					";
            }
        }
		
		return $this->errors;
	}
	
}

function getSiteDir( $str ) {
    $pos1    = strpos($_SERVER['SCRIPT_FILENAME'], '/sites/') + 7;
    $pos2    = strpos($_SERVER['SCRIPT_FILENAME'], '/modules/');
    $siteDir = substr($_SERVER['SCRIPT_FILENAME'], strpos($_SERVER['SCRIPT_FILENAME'], '/sites/') + 7, ($pos2 - $pos1));
    if ( preg_match('/^[a-zA-Z0-9_.]+$/', $siteDir) && ($siteDir != 'all') ) {
        return $siteDir;
    }
    return 'default';
}

function errorDisplayPage( $errorTitle, $errorMsg ) {
    include('error.html');
    exit();
}

