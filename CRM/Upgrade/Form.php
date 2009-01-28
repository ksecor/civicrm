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

require_once 'CRM/Core/Form.php';

class CRM_Upgrade_Form extends CRM_Core_Form {

    protected $_config;

    public    $latestVersion;

    function __construct( $state = null,
                          $action = CRM_Core_Action::NONE,
                          $method = 'post',
                          $name = null ) {
        $this->_config =& CRM_Core_Config::singleton( );
        $this->latestVersion = CRM_Utils_System::version();

        parent::__construct( $state, $action, $method, $name );
    }

    function checkSQLConstraints( &$constraints ) {
        $pass = $fail = 0;
        foreach ( $constraints as $constraint ) {
            if ( $this->checkSQLConstraint( $constraint ) ) {
                $pass++;
            } else {
                $fail++;
            }
            return array( $pass, $fail );
        }
    }
    
    function checkSQLConstraint( $constraint ) {
        // check constraint here
        return true;
    }
    
    function source( $fileName, $isQueryString = false ) {
        require_once 'CRM/Utils/File.php';

        CRM_Utils_File::sourceSQLFile( $this->_config->dsn,
                                       $fileName, null, $isQueryString );
    }
    
    function preProcess( ) {
        CRM_Utils_System::setTitle( $this->getTitle() );
        if ( ! $this->verifyPreDBState( $errorMessage ) ) {
            if (! isset($errorMessage)) {
                $errorMessage = 'pre-condition failed for current upgrade step';
            }
            CRM_Core_Error::fatal( $errorMessage );
        }
        $this->assign( 'recentlyViewed', false );
    }
    
    function buildQuickForm( ) {
        $this->addDefaultButtons( $this->getButtonTitle( ),
                                  'next',
                                  null,
                                  true );
    }
    
    function getTitle( ) {
        return ts( 'Title not Set' );
    }
    
    function getFieldsetTitle( ) {
        return ts( '' );
    }
    
    function getButtonTitle( ) {
        return ts( 'Continue' );
    }
    
    function getTemplateFileName( ) {
        $this->assign( 'title',
                       $this->getFieldsetTitle( ) );
        $this->assign( 'message',
                       $this->getTemplateMessage( ) );
        return 'CRM/Upgrade/Base.tpl';
    }
    
    function postProcess( ) {
        $this->upgrade( );
        
        if ( ! $this->verifyPostDBState( $errorMessage ) ) {
            if (! isset($errorMessage)) {
                $errorMessage = 'post-condition failed for current upgrade step';
            }
            CRM_Core_Error::fatal( $errorMessage );
        }
    }

    function runQuery( $query ) {
        return CRM_Core_DAO::executeQuery( $query,
                                           CRM_Core_DAO::$_nullArray );
    }

    function setVersion( $version ) {
        $query = "
UPDATE civicrm_domain
SET    version = '$version'
";
        return $this->runQuery( $query );
    }

    function checkVersion( $version ) {
        $domainID = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Domain',
                                                 $version, 'id',
                                                 'version' );
        return $domainID ? true : false; 
    }

    function getRevisionSequence( ) {
        $revList  = array();
        $sqlDir   = implode( DIRECTORY_SEPARATOR, 
                           array(dirname(__FILE__), 'Incremental', 'sql') );
        $sqlFiles = scandir($sqlDir);

        $sqlFilePattern = '/^(\d{1,2}\.\d{1,2}\.(\d{1,2}|\w{4,7}))\.(my)?sql(\.tpl)?$/i';
        foreach ($sqlFiles as $file) {
            if ( preg_match($sqlFilePattern, $file, $matches) ) {
                if ( ! in_array($matches[1], $revList) ) {
                    $revList[] = $matches[1];
                }
            }
        }

        // sample test list
/*         $revList = array('2.1.0', '2.2.beta2', '2.2.beta1', '2.2.alpha1', */
/*                          '2.2.alpha3', '2.2.0', '2.2.2', '2.1.alpha1', '2.1.3'); */

        usort($revList, 'version_compare');
        return $revList;
    }

    function processLocales( $tplFile ) {
        $config =& CRM_Core_Config::singleton();
        $smarty = new Smarty;
        $smarty->compile_dir = $config->templateCompileDir;
        
        $domain =& new CRM_Core_DAO_Domain();
        $domain->find(true);
        $multilingual = (bool) $domain->locales;
        $locales      = explode(CRM_Core_DAO::VALUE_SEPARATOR, $domain->locales);
        $smarty->assign('multilingual', $multilingual);
        $smarty->assign('locales',      $locales);
        
        // we didn't call CRM_Core_BAO_Setting::retrieve(), so we need to set $dbLocale by hand
        if ($multilingual) {
            global $dbLocale;
            $dbLocale = "_{$config->lcMessages}";
        }
        
        $this->source( $smarty->fetch($tplFile), true );

        if ( $multilingual ) {
            CRM_Core_I18n_Schema::rebuildMultilingualSchema($locales);
        }
        
        return $multilingual;
    }

    function processSQL( $rev ) {
        $sqlFile = implode( DIRECTORY_SEPARATOR, 
                            array(dirname(__FILE__), 'Incremental', 
                                  'sql', $rev . '.mysql') );
        $tplFile = "$sqlFile.tpl";

        if ( file_exists( $tplFile ) ) {
            $this->processLocales( $tplFile );
        } else {
            if ( ! file_exists($sqlFile) ) {
                CRM_Core_Error::fatal("sqlfile - $rev.mysql not found.");
            }
            $this->source( $sqlFile );
        }
    }
}


