<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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


require_once 'Mail/mime.php';

require_once 'CRM/Contact/BAO/SavedSearch.php';
require_once 'CRM/Contact/BAO/Query.php';
require_once 'CRM/Contact/BAO/Group.php';

require_once 'CRM/Mailing/DAO/Mailing.php';
require_once 'CRM/Mailing/DAO/Group.php';
require_once 'CRM/Mailing/Event/BAO/Queue.php';
require_once 'CRM/Mailing/Event/BAO/Delivered.php';
require_once 'CRM/Mailing/Event/BAO/Bounce.php';
require_once 'CRM/Mailing/BAO/TrackableURL.php';
require_once 'CRM/Mailing/BAO/Component.php';
require_once 'CRM/Mailing/BAO/Spool.php';

class CRM_Mailing_BAO_Mailing extends CRM_Mailing_DAO_Mailing {

    /**
     * An array that holds the complete templates
     * including any headers or footers that need to be prepended
     * or appended to the body
     */
    private $preparedTemplates = null;

    /**
     * An array that holds the complete templates
     * including any headers or footers that need to be prepended
     * or appended to the body
     */
    private $templates = null;

    /**
     * An array that holds the tokens that are specifically found in our text and html bodies
     */
    private $tokens = null;

    /**
     * The header associated with this mailing
     */
    private $header = null;

    /**
     * The footer associated with this mailing
     */
    private $footer = null;


    /**
     * The HTML content of the message
     */
    private $html = null;

    /**
     * The text content of the message
     */
    private $text = null;

    /**
     * Cached BAO for the domain
     */
    private $_domain = null;


    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Find all intended recipients of a mailing
     *
     * @param int  $job_id            Job ID
     * @param bool $includeDelivered  Whether to include the recipients who already got the mailing
     * @return object                 A DAO loaded with results of the form (email_id, contact_id)
     */
    function &getRecipientsObject($job_id, $includeDelivered = false) {
        $eq = self::getRecipients($job_id, $includeDelivered, $this->id);
        return $eq;
    }
    
    function &getRecipientsCount($job_id, $includeDelivered = false, $mailing_id = null) {
        $eq = self::getRecipients($job_id, $includeDelivered, $mailing_id);
        return $eq->N;
    }
    
    function &getRecipients($job_id, $includeDelivered = false, $mailing_id = null) {
        $mailingGroup =& new CRM_Mailing_DAO_Group();
        
        $mailing    = CRM_Mailing_BAO_Mailing::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $mg         = CRM_Mailing_DAO_Group::getTableName();
        $eq         = CRM_Mailing_Event_DAO_Queue::getTableName();
        $ed         = CRM_Mailing_Event_DAO_Delivered::getTableName();
        $eb         = CRM_Mailing_Event_DAO_Bounce::getTableName();
        
        $email      = CRM_Core_DAO_Email::getTableName();
        $contact    = CRM_Contact_DAO_Contact::getTableName();

        require_once 'CRM/Contact/DAO/Group.php';
        $group      = CRM_Contact_DAO_Group::getTableName();
        $g2contact  = CRM_Contact_DAO_GroupContact::getTableName();
      
        /* Create a temp table for contact exclusion */
        $mailingGroup->query(
            "CREATE TEMPORARY TABLE X_$job_id 
            (contact_id int primary key) 
            ENGINE=HEAP"
        );

        /* Add all the members of groups excluded from this mailing to the temp
         * table */
        $excludeSubGroup =
                    "INSERT INTO        X_$job_id (contact_id)
                    SELECT  DISTINCT    $g2contact.contact_id
                    FROM                $g2contact
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id AND $mg.entity_table = '$group'
                    WHERE
                                        $mg.mailing_id = {$mailing_id}
                        AND             $g2contact.status = 'Added'
                        AND             $mg.group_type = 'Exclude'";
        $mailingGroup->query($excludeSubGroup);
        
        /* Add all the (intended) recipients of an excluded prior mailing to
         * the temp table */
        $excludeSubMailing = 
                    "INSERT IGNORE INTO X_$job_id (contact_id)
                    SELECT  DISTINCT    $eq.contact_id
                    FROM                $eq
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $mg
                            ON          $job.mailing_id = $mg.entity_id AND $mg.entity_table = '$mailing'
                    WHERE
                                        $mg.mailing_id = {$mailing_id}
                        AND             $mg.group_type = 'Exclude'";
        $mailingGroup->query($excludeSubMailing);
        
        $ss =& new CRM_Core_DAO();
        $ss->query(
                "SELECT             $group.saved_search_id as saved_search_id
                FROM                $group
                INNER JOIN          $mg
                        ON          $mg.entity_id = $group.id
                WHERE               $mg.entity_table = '$group'
                    AND             $mg.group_type = 'Exclude'
                    AND             $mg.mailing_id = {$mailing_id}
                    AND             $group.saved_search_id IS NOT null");

        $whereTables = array( );
        while ($ss->fetch()) {
            /* run the saved search query and dump result contacts into the temp
             * table */
            $tables = array($contact => 1);
            $where =
                CRM_Contact_BAO_SavedSearch::whereClause( $ss->saved_search_id,
                                                          $tables,
                                                          $whereTables );
            $from = CRM_Contact_BAO_Query::fromClause($tables);
            $mailingGroup->query(
                    "INSERT IGNORE INTO X_$job_id (contact_id)
                    SELECT              contact_a.id
                                        $from
                    WHERE               $where");
        }

        /* Get all the group contacts we want to include */
        
        $mailingGroup->query(
            "CREATE TEMPORARY TABLE I_$job_id 
            (email_id int, contact_id int primary key)
            ENGINE=HEAP"
        );
        
        /* Get the group contacts, but only those which are not in the
         * exclusion temp table */

        /* Get the emails with no override */
        
        $query =    "REPLACE INTO       I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        $contact.id as contact_id
                    FROM                $email
                    INNER JOIN          $contact
                            ON          $email.contact_id = $contact.id
                    INNER JOIN          $g2contact
                            ON          $contact.id = $g2contact.contact_id
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                                AND     $mg.entity_table = '$group'
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $mg.group_type = 'Include'
                        AND             $g2contact.status = 'Added'
                        AND             $g2contact.email_id IS null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND          if($email.is_bulkmail,$email.is_bulkmail,$email.is_primary) = 1
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$mailing_id}
                        AND             X_$job_id.contact_id IS null";
        $mailingGroup->query($query);


        /* Query prior mailings */
        $mailingGroup->query(
                    "REPLACE INTO       I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        $contact.id as contact_id
                    FROM                $email
                    INNER JOIN          $contact
                            ON          $email.contact_id = $contact.id
                    INNER JOIN          $eq
                            ON          $eq.contact_id = $contact.id
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $mg
                            ON          $job.mailing_id = $mg.entity_id AND $mg.entity_table = '$mailing'
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE
                                        $mg.group_type = 'Include'
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$mailing_id}
                        AND             X_$job_id.contact_id IS null");

        /* Construct the saved-search queries */
        $ss->query("SELECT          $group.saved_search_id as saved_search_id
                    FROM            $group
                    INNER JOIN      $mg
                            ON      $mg.entity_id = $group.id
                                AND $mg.entity_table = '$group'
                    WHERE               
                                    $mg.group_type = 'Include'
                        AND         $mg.mailing_id = {$mailing_id}
                        AND         $group.saved_search_id IS NOT null");

        $whereTables = array( );
        while ($ss->fetch()) {
            $tables = array($contact => 1, $location => 1, $email => 1);
            $where = CRM_Contact_BAO_SavedSearch::whereClause(
                                                              $ss->saved_search_id,
                                                              $tables,
                                                              $whereTables
                                                              );
            $where = trim( $where );
            if ( $where ) {
                $where = " AND $where ";
            }
            $from = CRM_Contact_BAO_Query::fromClause($tables);
            $ssq = "INSERT IGNORE INTO  I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        contact_a.id as contact_id 
                    $from
                    LEFT JOIN           X_$job_id
                            ON          contact_a.id = X_$job_id.contact_id
                    WHERE           
                                        contact_a.do_not_email = 0
                        AND             contact_a.is_opt_out = 0
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                                        $where
                        AND             X_$job_id.contact_id IS null ";
            $mailingGroup->query($ssq);
        }
        
        /* Get the emails with only location override */
        $query =    "REPLACE INTO       I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as local_email_id,
                                        $contact.id as contact_id
                    FROM                $email
                    INNER JOIN          $contact
                            ON          $email.contact_id = $contact.id
                    INNER JOIN          $g2contact
                            ON          $contact.id = $g2contact.contact_id
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $mg.entity_table = '$group'
                        AND             $mg.group_type = 'Include'
                        AND             $g2contact.status = 'Added'
                        AND             $g2contact.email_id is null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$mailing_id}
                        AND             X_$job_id.contact_id IS null";
        $mailingGroup->query($query);
                    
        /* Get the emails with full override */
        $mailingGroup->query(
                    "REPLACE INTO       I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        $contact.id as contact_id
                    FROM                $email
                    INNER JOIN          $g2contact
                            ON          $email.id = $g2contact.email_id
                    INNER JOIN          $contact
                            ON          $contact.id = $g2contact.contact_id
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $mg.entity_table = '$group'
                        AND             $mg.group_type = 'Include'
                        AND             $g2contact.status = 'Added'
                        AND             $g2contact.email_id IS NOT null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$mailing_id}
                        AND             X_$job_id.contact_id IS null");
                        
        $results = array();

        $eq =& new CRM_Mailing_Event_BAO_Queue();
        
        $eq->query("SELECT contact_id, email_id 
                    FROM I_$job_id 
                    ORDER BY contact_id, email_id");
        
        /* Delete the temp table */
        $mailingGroup->reset();
        $mailingGroup->query("DROP TEMPORARY TABLE X_$job_id");
        $mailingGroup->query("DROP TEMPORARY TABLE I_$job_id");

        return $eq;
    }
    
    private function _getMailingGroupIds( $type = 'Include' ) {
        $mailingGroup =& new CRM_Mailing_DAO_Group();
        $group = CRM_Contact_DAO_Group::getTableName();
        if ( ! isset( $this->id ) ) {
            // we're just testing tokens, so return any group
            $query = "SELECT   id AS entity_id
                      FROM     $group
                      ORDER BY id
                      LIMIT 1";
        } else {
            $query = "SELECT entity_id
                      FROM   $mg
                      WHERE  mailing_id = {$this->id}
                      AND    group_type = '$type'
                      AND    entity_table = '$group'";
        }
        $mailingGroup->query( $query );
        
        $groupIds = array( );
        while ( $mailingGroup->fetch( ) ) {
            $groupIds[] = $mailingGroup->entity_id;
        }
        
        return $groupIds;
    }


    /**
     * 
     * Returns the regex patterns that are used for preparing the text and html templates
     *   
     * @access private
     * 
     **/
    private function &getPatterns($onlyHrefs = false) 
    {
        
        $patterns = array();
        
        $protos = '(https?|ftp)';
        $letters = '\w';
        $gunk = '\{\}/#~:.?+=&;%@!\-';
        $punc = '.:?\-';
        $any = "{$letters}{$gunk}{$punc}";
        if ( $onlyHrefs ) {
            $pattern = "\\bhref[ ]*=[ ]*([\"'])?(($protos:[$any]+?(?=[$punc]*[^$any]|$)))([\"'])?";
        } else {
            $pattern = "\\b($protos:[$any]+?(?=[$punc]*[^$any]|$))";
        }
        
        $patterns[] = $pattern;
        $patterns[] = '\\\\\{\w+\.\w+\\\\\}|\{\{\w+\.\w+\}\}';
        $patterns[] = '\{\w+\.\w+\}';
        
        $patterns = '{'.join('|',$patterns).'}im';
        
        return $patterns;
    }

    /**
     *  returns an array that denotes the type of token that we are dealing with
     *  we use the type later on when we are doing a token replcement lookup
     *
     *  @param string $token       The token for which we will be doing adata lookup
     *  
     *  @return array $funcStruct  An array that holds the token itself and the type.
     *                             the type will tell us which function to use for the data lookup
     *                             if we need to do a lookup at all
     */

    function &getDataFunc($token) 
    {
        $funcStruct = array('type' => null,'token' => $token);
        $matches = array();
        if ( preg_match('/^http/i',$token) && $this->url_tracking ) {
            // it is a url so we need to check to see if there are any tokens embedded
            // if so then call this function again to get the token dataFunc
            // and assign the type 'embedded'  so that the data retrieving function
            // will know what how to handle this token.
            if ( preg_match('/(\{\w+\.\w+\})/', $token, $matches) ) {
                $funcStruct['type'] = 'embedded_url';
                $preg_token = '/'.preg_quote($matches[1],'/').'/';
                $funcStruct['embed_parts'] = preg_split($preg_token,$token,2);
                $funcStruct['token'] = $this->getDataFunc($matches[1]);
            } else {
                $funcStruct['type'] = 'url';
            }
            
        } else if ( preg_match('/^\{(domain)\.(\w+)\}$/',$token, $matches) ) {

            $funcStruct['type'] = $matches[1];
            $funcStruct['token'] = $matches[2];
            
        } else if ( preg_match('/^\{(action)\.(\w+)\}$/',$token, $matches) ) {
          
            $funcStruct['type'] = $matches[1];
            $funcStruct['token'] = $matches[2];
            
        } else if ( preg_match('/^\{(mailing)\.(\w+)\}$/',$token,$matches) ) {
            
            $funcStruct['type'] = $matches[1];
            $funcStruct['token'] = $matches[2];
            
        } else if ( preg_match('/^\{(contact)\.(\w+)\}$/',$token, $matches) ) {
            
            $funcStruct['type'] = $matches[1];
            $funcStruct['token'] = $matches[2];
            
        } else if(preg_match('/\\\\\{(\w+\.\w+)\\\\\}|\{\{(\w+\.\w+)\}\}/', $token, $matches) ) {
            // we are an escaped token
            // so remove the escape chars
            $unescaped_token = preg_replace('/\{\{|\}\}|\\\\\{|\\\\\}/','',$matches[0]);
            $funcStruct['token'] = '{'.$unescaped_token.'}';
        }
        return $funcStruct;
    }

    /**
     * 
     * Prepares the text and html templates
     * for generating the emails and returns a copy of the
     * prepared templates
     *   
     * @access private
     * 
     **/
    private function getPreparedTemplates( )
    {
        if ( !$this->preparedTemplates ) {
            $patterns['html'] = $this->getPatterns(true);
            $patterns['text'] = $this->getPatterns();
            $templates = $this->getTemplates();
            
            $this->preparedTemplates = array();
            
            foreach (array('html','text') as $key) {
                if (!isset($templates[$key])) {
                    continue;
                }
                
                $matches = array();
                $tokens = array();
                $split_template = array();
                
                $email = $templates[$key];
                preg_match_all($patterns[$key],$email,$matches,PREG_PATTERN_ORDER);
                foreach ($matches[0] as $idx => $token) {
                    if (preg_match('/^href/i',$token)) {
                        $token = preg_replace('/^href[ ]*=[ ]*[\'"](.*?)[\'"]$/','$1',$token);
                    }
                    $preg_token = '/'.preg_quote($token,'/').'/im';
                    list($split_template[],$email) = preg_split($preg_token,$email,2);
                    array_push($tokens, $this->getDataFunc($token));
                }
                if ($email) {
                    $split_template[] = $email;
                }
                $this->preparedTemplates[$key]['template'] = $split_template;
                $this->preparedTemplates[$key]['tokens'] = $tokens;
            }
        }
        return($this->preparedTemplates);
    }

    /**
     * 
     *  Retrieve a ref to an array that holds the email and text templates for this email
     *  assembles the complete template including the header and footer
     *  that the user has uploaded or declared (if they have dome that)
     *  
     *  
     * @return array reference to an assoc array
     * @access private
     * 
     **/
    private function &getTemplates( )
    {
        require_once('CRM/Utils/String.php');
        if (!$this->templates) {
          $this->getHeaderFooter();
          $this->templates = array(  );
          
          if ( $this->body_text ) {
              $template = array();
              if ( $this->header ) {
                  $template[] = $this->header->body_text;
              }

              $template[] = $this->body_text;
              
              if ( $this->footer ) {
                  $template[] = $this->footer->body_text;
              }

              $this->templates['text'] = join("\n",$template);
          }

          if ( $this->body_html ) {
              
              $template = array();
              if ( $this->header ) {
                  $template[] = $this->header->body_html;
              }

              $template[] = $this->body_html;

              if ( $this->footer ) {
                  $template[] = $this->footer->body_html;
              }
              
              $this->templates['html'] = join("\n",$template);
    
              // this is where we create a text tepalte from the html template if the texttempalte did not exist
              // this way we ensure that every recipient will receive n email even if the pref is set to text and the
              // user uploads an html email only
              if ( !$this->body_text ) {
                  $this->templates['text'] = CRM_Utils_String::htmlToText( $this->templates['html'] );
              }
          }
      }
      return $this->templates;    
    }

    /**
     * 
     *  Retrieve a ref to an array that holds all of the tokens in the email body
     *  where the keys are the type of token and the values are ordinal arrays
     *  that hold the token names (even repeated tokens) in the order in which
     *  they appear in the body of the email.
     *  
     *  note: the real work is done in the _getTokens() function
     *  
     *  this function needs to have some sort of a body assigned
     *  either text or html for this to have any meaningful impact
     *  
     * @return array               reference to an assoc array
     * @access public
     * 
     **/
    public function &getTokens( ) 
    {
        if (!$this->tokens) {
            
            $this->tokens = array( 'html' => array(), 'text' => array() );
            
            if ($this->body_html) {
                $this->_getTokens('html');
            }
            
            if ($this->body_text) {
                $this->_getTokens('text');
            }
            
        }
        return $this->tokens;      
    }
    
    /**
     *
     *  _getTokens parses out all of the tokens that have been
     *  included in the html and text bodies of the email
     *  we get the tokens and then separate them into an
     *  internal structure named tokens that has the same
     *  form as the static tokens property(?) of the CRM_Utils_Token class.
     *  The difference is that there might be repeated token names as we want the
     *  structures to represent the order in which tokens were found from left to right, top to bottom.
     *
     *  
     * @param str $prop     name of the property that holds the text that we want to scan for tokens (html, text)
     * @access private
     * @return void
     */

    private function _getTokens( $prop ) 
    {
        $templates = $this->getTemplates();
        $matches = array();
        preg_match_all( '/(?<!\{|\\\\)\{(\w+\.\w+)\}(?!\})/',
                        $templates[$prop],
                        $matches,
                        PREG_PATTERN_ORDER);
        
        if ( $matches[1] ) {
            foreach ( $matches[1] as $token ) {
                list($type,$name) = split( '\.', $token, 2 );
                if ( $name ) {
                    if ( ! isset( $this->tokens[$prop][$type] ) ) {
                        $this->tokens[$prop][$type] = array( );
                    }
                    $this->tokens[$prop][$type][] = $name;
                }
            }
        }
    }

    /**
     * Generate an event queue for a test job 
     *
     * @params array $params contains form values
     * @return void
     * @access public
     */
    public function getTestRecipients($testParams) 
    {
        $session    =& CRM_Core_Session::singleton();
        
        if ($testParams['test_email']) {
            /* First, find out if the contact already exists */  
            $query = "
                  SELECT DISTINCT contact_a.id as contact_id 
                  FROM civicrm_contact contact_a 
                  LEFT JOIN civicrm_email      ON contact_a.id = civicrm_email.contact_id
                      WHERE LOWER(civicrm_email.email) = %1";
            
            $params = array( 1 => array( $testParams['test_email'], 'String' ) );
            $dao =& CRM_Core_DAO::executeQuery( $query, $params );
            $id = array( );
            // lets just use the first contact id we got
            if ( $dao->fetch( ) ) {
                $contact_id = $dao->contact_id;
            }
            $dao->free( );
            
            $userID = $session->get('userID');
            $params = array( 1 => array($testParams['test_email'], 'String' ) );
            
            if ( ! $contact_id ) {
                $query = "INSERT INTO   civicrm_email (contact_id, email) values ($userID,%1)"; 
                CRM_Core_DAO::executeQuery( $query, $params );
                $contact_id = $userID;
            } 
            $query = "SELECT        civicrm_email.id 
                      FROM civicrm_email
                      WHERE         civicrm_email.email = %1";
            
            $dao =& CRM_Core_DAO::executeQuery( $query, $params);
            if ($dao->fetch( ) ) {
                $email_id = $dao->id;
            }
            $dao->free( );
            $params = array(
                            'job_id'        => $testParams['job_id'],
                            'email_id'      => $email_id,
                            'contact_id'    => $contact_id
                            );
            CRM_Mailing_Event_BAO_Queue::create($params);  
        }
        
        if (array_key_exists($testParams['test_group'], CRM_Core_PseudoConstant::group())) {
            $group =& new CRM_Contact_DAO_Group();
            $group->id = $testParams['test_group'];
            $contacts = CRM_Contact_BAO_GroupContact::getGroupContacts($group);
            foreach ($contacts as $contact) {
                $query = 
                    "SELECT DISTINCT civicrm_email.id AS email_id, civicrm_email.is_primary as is_primary,
                                 civicrm_email.is_bulkmail as is_bulkmail
FROM civicrm_email
INNER JOIN civicrm_contact ON civicrm_email.contact_id = civicrm_contact.id
WHERE civicrm_email.is_bulkmail = 1
AND civicrm_contact.id = {$contact->contact_id}
AND civicrm_contact.do_not_email =0
AND civicrm_email.on_hold = 0
AND civicrm_contact.is_opt_out =0";
                $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray);
                if ($dao->fetch( ) ) {
                    $params = array(
                                    'job_id'        => $testParams['job_id'],
                                    'email_id'      => $dao->email_id,
                                    'contact_id'    => $contact->contact_id
                                    );
                    $queue = CRM_Mailing_Event_BAO_Queue::create($params);  
                } else {
                    $query = 
                    "SELECT DISTINCT civicrm_email.id AS email_id, civicrm_email.is_primary as is_primary,
                                 civicrm_email.is_bulkmail as is_bulkmail
FROM civicrm_email
INNER JOIN civicrm_contact ON civicrm_email.contact_id = civicrm_contact.id
WHERE civicrm_email.is_primary = 1
AND civicrm_contact.id = {$contact->contact_id}
AND civicrm_contact.do_not_email =0
AND civicrm_email.on_hold = 0
AND civicrm_contact.is_opt_out =0";
                    $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray);
                    if ($dao->fetch( ) ) {
                        $params = array(
                                        'job_id'        => $testParams['job_id'],
                                        'email_id'      => $dao->email_id,
                                        'contact_id'    => $contact->contact_id
                                        );
                        $queue = CRM_Mailing_Event_BAO_Queue::create($params);  
                    }                    
                }
            }
        }
    }
    /**
     * Retrieve the header and footer for this mailing
     *
     * @param void
     * @return void
     * @access private
     */
    private function getHeaderFooter() {
        if (!$this->header and $this->header_id) {
            $this->header =& new CRM_Mailing_BAO_Component();
            $this->header->id = $this->header_id;
            $this->header->find(true);
            $this->header->free( );
        }
        
        if (!$this->footer and $this->footer_id) {
            $this->footer =& new CRM_Mailing_BAO_Component();
            $this->footer->id = $this->footer_id;
            $this->footer->find(true);
            $this->footer->free( );
        }
    }



    /**
     * static wrapper for getting verp and urls
     *
     * @param int $job_id           ID of the Job associated with this message
     * @param int $event_queue_id   ID of the EventQueue
     * @param string $hash          Hash of the EventQueue
     * @param string $email         Destination address
     * @return (reference) array    array ref that hold array refs to the verp info and urls
     */
    static function getVerpAndUrls($job_id, $event_queue_id, $hash, $email){
        // create a skeleton object and set its properties that are required by getVerpAndUrlsAndHeaders()
        require_once 'CRM/Core/BAO/Domain.php';
        $config =& CRM_Core_Config::singleton();
        $bao =& new CRM_Mailing_BAO_Mailing();
        $bao->domain_id = $config->domainID();
        $bao->_domain =& CRM_Core_BAO_Domain::getDomainByID($bao->domain_id);
        $bao->from_name = $bao->from_email = $bao->subject = '';

        // use $bao's instance method to get verp and urls
        list($verp, $urls, $_) = $bao->getVerpAndUrlsAndHeaders($job_id, $event_queue_id, $hash, $email);
        return array($verp, $urls);
    }

    /**
     * get verp, urls and headers
     *
     * @param int $job_id           ID of the Job associated with this message
     * @param int $event_queue_id   ID of the EventQueue
     * @param string $hash          Hash of the EventQueue
     * @param string $email         Destination address
     * @return (reference) array    array ref that hold array refs to the verp info, urls, and headers
     * @access private
     */
    private function getVerpAndUrlsAndHeaders( $job_id, $event_queue_id, $hash, $email )
    {
        $config =& CRM_Core_Config::singleton( );
        /**
         * Inbound VERP keys:
         *  reply:          user replied to mailing
         *  bounce:         email address bounced
         *  unsubscribe:    contact opts out of all target lists for the mailing
         *  resubscribe:    contact opts back into all target lists for the mailing
         *  optOut:         contact unsubscribes from the domain
         */
        $verp = array( );
        foreach (array('reply', 'bounce', 'unsubscribe', 'resubscribe', 'optOut') as $key) {
            $verp[$key] = implode($config->verpSeparator,
                                  array(
                                        $key, 
                                        $this->domain_id,
                                        $job_id, 
                                        $event_queue_id,
                                        $hash
                                        )
                                  ) . '@' . $this->_domain->email_domain;
        }

        $urls = array(
                      'forward'         => CRM_Utils_System::url('civicrm/mailing/forward', 
                                                                 "reset=1&jid={$job_id}&qid={$event_queue_id}&h={$hash}",
                                                                 true),
                      'unsubscribeUrl' => CRM_Utils_System::url('civicrm/mailing/unsubscribe', 
                                                                "reset=1&jid={$job_id}&qid={$event_queue_id}&h={$hash}",
                                                                true), 
                      'resubscribeUrl' => CRM_Utils_System::url('civicrm/mailing/resubscribe', 
                                                                "reset=1&jid={$job_id}&qid={$event_queue_id}&h={$hash}",
                                                                true), 
                      'optOutUrl'      => CRM_Utils_System::url('civicrm/mailing/optout', 
                                                                "reset=1&jid={$job_id}&qid={$event_queue_id}&h={$hash}",
                                                                true), 
                      );

        $headers = array(
                         'Reply-To'  => CRM_Utils_Verp::encode($verp['reply'], $email),
                         'Return-Path' => CRM_Utils_Verp::encode($verp['bounce'], $email),
                         'From'      => "\"{$this->from_name}\" <{$this->from_email}>",
                         'Subject'   => $this->subject,
                         );
      
        return array( &$verp,&$urls,&$headers);
    }

    /**
     * Compose a message
     *
     * @param int $job_id           ID of the Job associated with this message
     * @param int $event_queue_id   ID of the EventQueue
     * @param string $hash          Hash of the EventQueue
     * @param string $contactId     ID of the Contact
     * @param string $email         Destination address
     * @param string $recipient     To: of the recipient
     * @param boolean $test         Is this mailing a test?
     * @return object               The mail object
     * @access public
     */
    public function &compose($job_id, $event_queue_id, $hash, $contactId, 
                             $email, &$recipient, $test = false, 
                             $contactDetails = null ) 
    {
        
        require_once 'api/Contact.php';
        require_once 'CRM/Utils/Token.php';
        $config =& CRM_Core_Config::singleton( );
        $knownTokens = $this->getTokens();
        
        if ($this->_domain == null) {
            require_once 'CRM/Core/BAO/Domain.php';
            $this->_domain =& CRM_Core_BAO_Domain::getDomainByID($this->domain_id);
        }

        list($verp,$urls,$headers) = $this->getVerpAndUrlsAndHeaders($job_id, $event_queue_id, $hash, $email);

        if ( $contactDetails ) {
            $contact = $contactDetails;
        } else {
            $params  = array( 'contact_id' => $contactId );
            $contact =& crm_fetch_contact( $params );
            if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                return null;
            }
        }

        $pTemplates = $this->getPreparedTemplates();
        $pEmails = array( );

        foreach( $pTemplates as $type => $pTemplate ) {
            $html = ($type == 'html') ? true : false;
            $pEmails[$type] = array( );
            $pEmail   =& $pEmails[$type];
            $template =& $pTemplates[$type]['template'];
            $tokens   =& $pTemplates[$type]['tokens'];
            $idx = 0;
            if ( !empty( $tokens ) ) { 
                foreach ($tokens as $idx => $token) {
                    $token_data = $this->getTokenData($token, $html, $contact, $verp, $urls, $event_queue_id);
                    array_push($pEmail, $template[$idx]);
                    array_push($pEmail, $token_data);
                }
            } else {
                array_push($pEmail, $template[$idx]);
            }
            
            if ( isset( $template[($idx + 1)] ) ) {
                array_push($pEmail, $template[($idx + 1)]);
            }
        }

        $html = null;
        if ( isset( $pEmails['html'] ) &&  is_array( $pEmails['html'] ) && count( $pEmails['html'] ) ) {
            $html = &$pEmails['html'];
        }
        
        $text = null;
        if ( isset( $pEmails['text'] ) && is_array( $pEmails['text'] ) && count( $pEmails['text'] ) ){
            $text = &$pEmails['text'];
        }
        
        // push the tracking url on to the html email if necessary
        if ($this->open_tracking && $html ) {
            array_push($html,"\n".'<img src="' . $config->userFrameworkResourceURL . 
                       "extern/open.php?q=$event_queue_id\" width='1' height='1' alt='' border='0'>");
        }
        
        $message =& new Mail_Mime("\n");
        
        if ($text && ( $test || $contact['preferred_mail_format'] == 'Text' ||
                       $contact['preferred_mail_format'] == 'Both' ||
                       ( $contact['preferred_mail_format'] == 'HTML' && !array_key_exists('html',$pEmails) ) ) ) {
            $message->setTxtBody( join( '', $text ) );
        }
        
        if ( $html && ( $test ||  ( $contact['preferred_mail_format'] == 'HTML' ||
                                    $contact['preferred_mail_format'] == 'Both') ) ) {
            $message->setHTMLBody( join( '', $html ) );
        }

        $message->_txtbody =  CRM_Utils_Token::replaceSubscribeInviteTokens($message->_txtbody);
        $message->_htmlbody =  CRM_Utils_Token::replaceSubscribeInviteTokens($message->_htmlbody);
        $recipient = "\"{$contact['display_name']}\" <$email>";
        $headers['To'] = $recipient;
        
        $mailMimeParams = array(
                                'text_encoding' => '8bit',
                                'html_encoding' => '8bit',
                                'head_charset'  => 'utf-8',
                                'text_charset'  => 'utf-8',
                                'html_charset'  => 'utf-8',
                                );
        
        $message->get($mailMimeParams);
        $message->headers($headers);
        
        // make sure we unset a lot of stuff
        unset( $verp );
        unset( $urls );
        unset( $params );
        unset( $contact );
        unset( $ids );

        return $message;
    }

    /**
     *
     *  getTokenData receives a token from an email
     *  and returns the appropriate data for the token
     *
     */
    private function getTokenData(&$token_a, $html = false, &$contact, &$verp, &$urls, $event_queue_id)
    {
        $type = $token_a['type'];
        $token = $token_a['token'];
        $data = $token;

        if ($type == 'embedded_url') {
            $embed_data = $this->getTokenData($token, $html = false, $contact, $verp, $urls, $event_queue_id);
            $url = join($token_a['embed_parts'],$embed_data);
            $data = CRM_Mailing_BAO_TrackableURL::getTrackerURL($url, $this->id, $event_queue_id);
            
        } else if ( $type == 'url' ) {
            $data = CRM_Mailing_BAO_TrackableURL::getTrackerURL($token, $this->id, $event_queue_id);
        } else if ( $type == 'mailing' ) {
          $data = CRM_Utils_Token::getMailingTokenReplacement($token, $this);
        } else if ( $type == 'contact' ) {
          $data = CRM_Utils_Token::getContactTokenReplacement($token, $contact);
        } else if ( $type == 'action' ) {
          $data = CRM_Utils_Token::getActionTokenReplacement($token, $verp, $urls, $html);
        } else if ( $type == 'domain' ) {
          $data = CRM_Utils_Token::getDomainTokenReplacement($token, $this->_domain, $html);         
        }
        return $data;
    }

    /**
     * Return a list of group names for this mailing.  Does not work with
     * prior-mailing targets.
     *
     * @return array        Names of groups receiving this mailing
     * @access public
     */
    public function &getGroupNames() {
        if (! isset($this->id)) {
            return array();
        }
        $mg =& new CRM_Mailing_DAO_Group();
        $mgtable = CRM_Mailing_DAO_Group::getTableName();
        $group = CRM_Contact_BAO_Group::getTableName();

        $mg->query("SELECT      $group.name as name FROM $mgtable 
                    INNER JOIN  $group ON $mgtable.entity_id = $group.id
                    WHERE       $mgtable.mailing_id = {$this->id}
                        AND     $mgtable.entity_table = '$group'
                        AND     $mgtable.group_type = 'Include'
                    ORDER BY    $group.name");

        $groups = array();
        while ($mg->fetch()) {
            $groups[] = $mg->name;
        }
        return $groups;
    }
    
    /**
     * function to add the mailings
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add( &$params, &$ids )
    {
        require_once 'CRM/Utils/Hook.php';
        
        if ( CRM_Utils_Array::value( 'mailing', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Mailing', $ids['mailing_id'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Mailing', null, $params ); 
        }
        
        $mailing =& new CRM_Mailing_DAO_Mailing( );
        $mailing->domain_id = CRM_Core_Config::domainID( );
        $mailing->id = CRM_Utils_Array::value( 'mailing_id', $ids );
        
        $mailing->copyValues( $params );
        $result = $mailing->save( );

        if ( CRM_Utils_Array::value( 'mailing', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Mailing', $mailing->id, $mailing );
        } else {
            CRM_Utils_Hook::post( 'create', 'Mailing', $mailing->id, $mailing );
        }

        return $result;
    }

    /**
     * Construct a new mailing object, along with job and mailing_group
     * objects, from the form values of the create mailing wizard.
     *
     * @params array $params        Form values
     * @return object $mailing      The new mailing object
     * @access public
     * @static
     */
    public static function create( &$params, &$ids ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $mailing = self::add($params, $ids);

        if( is_a( $mailing, 'CRM_Core_Error') ) {
            $transaction->rollback( );
            return $mailing;
        }
        
        require_once 'CRM/Contact/BAO/Group.php';
        /* Create the mailing group record */
        $mg =& new CRM_Mailing_DAO_Group();
        foreach( array( 'groups', 'mailings' ) as $entity ) {
            foreach( array( 'include', 'exclude' ) as $type ) {                
                if( is_array( $params[$entity][$type] ) ) {                    
                    foreach( $params[$entity][$type] as $entityId ) {
                        $mg->reset( );
                        $mg->mailing_id = $mailing->id;                        
                        $mg->entity_table   = ( $entity == 'groups' ) 
                                            ? CRM_Contact_BAO_Group::getTableName( )
                                            : CRM_Mailing_BAO_Mailing::getTableName( );
                        $mg->entity_id = $entityId;
                        $mg->group_type = $type;
                        $mg->save( );
                    }
                }
            }
        }
        $transaction->commit( );
        return $mailing;
    }


    /**
     * Generate a report.  Fetch event count information, mailing data, and job
     * status.
     *
     * @param int $id       The mailing id to report
     * @return array        Associative array of reporting data
     * @access public
     * @static
     */
    public static function &report($id) {
        $mailing_id = CRM_Utils_Type::escape($id, 'Integer');
        
        $mailing =& new CRM_Mailing_BAO_Mailing();

        require_once 'CRM/Mailing/Event/BAO/Opened.php';
        require_once 'CRM/Mailing/Event/BAO/Reply.php';
        require_once 'CRM/Mailing/Event/BAO/Unsubscribe.php';
        require_once 'CRM/Mailing/Event/BAO/Forward.php';
        require_once 'CRM/Mailing/Event/BAO/TrackableURLOpen.php';
        require_once 'CRM/Mailing/BAO/Spool.php';
        $t = array(
                'mailing'   => self::getTableName(),
                'mailing_group'  => CRM_Mailing_DAO_Group::getTableName(),
                'group'     => CRM_Contact_BAO_Group::getTableName(),
                'job'       => CRM_Mailing_BAO_Job::getTableName(),
                'queue'     => CRM_Mailing_Event_BAO_Queue::getTableName(),
                'delivered' => CRM_Mailing_Event_BAO_Delivered::getTableName(),
                'opened'    => CRM_Mailing_Event_BAO_Opened::getTableName(),
                'reply'     => CRM_Mailing_Event_BAO_Reply::getTableName(),
                'unsubscribe'   =>
                            CRM_Mailing_Event_BAO_Unsubscribe::getTableName(),
                'bounce'    => CRM_Mailing_Event_BAO_Bounce::getTableName(),
                'forward'   => CRM_Mailing_Event_BAO_Forward::getTableName(),
                'url'       => CRM_Mailing_BAO_TrackableURL::getTableName(),
                'urlopen'   =>
                    CRM_Mailing_Event_BAO_TrackableURLOpen::getTableName(),
                'component' =>  CRM_Mailing_BAO_Component::getTableName(),
                'spool'     => CRM_Mailing_BAO_Spool::getTableName() 
            );
        
        
        $report = array();
                
        /* FIXME: put some permissioning in here */
        /* Get the mailing info */
        $mailing->query("
            SELECT          {$t['mailing']}.*
            FROM            {$t['mailing']}
            WHERE           {$t['mailing']}.id = $mailing_id");
            
        $mailing->fetch();
        

        $report['mailing'] = array();
        foreach (array_keys(self::fields()) as $field) {
            $report['mailing'][$field] = $mailing->$field;
        }


        /* Get the component info */
        $query = array();
        
        $components = array(
                        'header'        => ts('Header'),
                        'footer'        => ts('Footer'),
                        'reply'         => ts('Reply'),
                        'unsubscribe'   => ts('Unsubscribe'),
                        'optout'        => ts('Opt-Out')
                    );
        foreach(array_keys($components) as $type) {
            $query[] = "SELECT          {$t['component']}.name as name,
                                        '$type' as type,
                                        {$t['component']}.id as id
                        FROM            {$t['component']}
                        INNER JOIN      {$t['mailing']}
                                ON      {$t['mailing']}.{$type}_id =
                                                {$t['component']}.id
                        WHERE           {$t['mailing']}.id = $mailing_id";
        }
        $q = '(' . implode(') UNION (', $query) . ')';
        $mailing->query($q);

        $report['component'] = array();
        while ($mailing->fetch()) {
            $report['component'][] = array(
                                    'type'  => $components[$mailing->type],
                                    'name'  => $mailing->name,
                                    'link'  =>
                                    CRM_Utils_System::url('civicrm/mailing/component', "reset=1&action=update&id={$mailing->id}"),
                                    );
        }
        
        /* Get the recipient group info */
        $mailing->query("
            SELECT          {$t['mailing_group']}.group_type as group_type,
                            {$t['group']}.id as group_id,
                            {$t['group']}.title as group_title,
                            {$t['mailing']}.id as mailing_id,
                            {$t['mailing']}.name as mailing_name
            FROM            {$t['mailing_group']}
            LEFT JOIN       {$t['group']}
                    ON      {$t['mailing_group']}.entity_id = {$t['group']}.id
                    AND     {$t['mailing_group']}.entity_table =
                                                                '{$t['group']}'
            LEFT JOIN       {$t['mailing']}
                    ON      {$t['mailing_group']}.entity_id =
                                                            {$t['mailing']}.id
                    AND     {$t['mailing_group']}.entity_table =
                                                            '{$t['mailing']}'

            WHERE           {$t['mailing_group']}.mailing_id = $mailing_id
            ");
        
        $report['group'] = array('include' => array(), 'exclude' => array());
        while ($mailing->fetch()) {
            $row = array();
            if (isset($mailing->group_id)) {
                $row['id'] = $mailing->group_id;
                $row['name'] = $mailing->group_title;
                $row['link'] = CRM_Utils_System::url('civicrm/group/search',
                            "reset=1&force=1&context=smog&gid={$row['id']}");
            } else {
                $row['id'] = $mailing->mailing_id;
                $row['name'] = $mailing->mailing_name;
                $row['mailing'] = true;
                $row['link'] = CRM_Utils_System::url('civicrm/mailing/report',
                                                    "mid={$row['id']}");
            }
            
            if ($mailing->group_type == 'Include') {
                $report['group']['include'][] = $row;
            } else {
                $report['group']['exclude'][] = $row;
            }
        }

        /* Get the event totals, grouped by job (retries) */
        $mailing->query("
            SELECT          {$t['job']}.*,
                            COUNT(DISTINCT {$t['queue']}.id) as queue,
                            COUNT(DISTINCT {$t['delivered']}.id) as delivered,
                            COUNT(DISTINCT {$t['reply']}.id) as reply,
                            COUNT(DISTINCT {$t['forward']}.id) as forward,
                            COUNT(DISTINCT {$t['bounce']}.id) as bounce,
                            COUNT(DISTINCT {$t['urlopen']}.id) as url,
                            COUNT(DISTINCT {$t['spool']}.id) as spool
            FROM            {$t['job']}
            LEFT JOIN       {$t['queue']}
                    ON      {$t['queue']}.job_id = {$t['job']}.id
            LEFT JOIN       {$t['reply']}
                    ON      {$t['reply']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['forward']}
                    ON      {$t['forward']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['bounce']}
                    ON      {$t['bounce']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['delivered']}
                    ON      {$t['delivered']}.event_queue_id = {$t['queue']}.id
                    AND     {$t['bounce']}.id IS null
            LEFT JOIN       {$t['urlopen']}
                    ON      {$t['urlopen']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['spool']}
                    ON      {$t['spool']}.job_id = {$t['job']}.id
            WHERE           {$t['job']}.mailing_id = $mailing_id
                    AND     {$t['job']}.is_test = 0
            GROUP BY        {$t['job']}.id");
        
        $report['jobs'] = array();
        $report['event_totals'] = array();
        while ($mailing->fetch()) {
            $row = array();
            foreach(array(  'queue', 'delivered', 'url', 'forward',
                            'reply', 'unsubscribe', 'bounce', 'spool') as $field) {
                if (isset( $mailing->$field )){
                    $row[$field] = $mailing->$field;
                }
                if (isset($report['event_totals'][$field])) {
                    $report['event_totals'][$field] += $mailing->$field;
                }
            }
            
            // compute open total seperately to discount duplicates
            // CRM-1258
            $row['opened'] = CRM_Mailing_Event_BAO_Opened::getTotalCount( $mailing_id, $mailing->id, true );
            if ( isset($report['event_totals']['opened']) ) {
                $report['event_totals']['opened'] += $row['opened'];
            }
            
            // compute unsub total seperately to discount duplicates
            // CRM-1783
            $row['unsubscribe'] = CRM_Mailing_Event_BAO_Unsubscribe::getTotalCount( $mailing_id, $mailing->id, true );
            if (isset($report['event_totals']['unsubscribe'])) {
                $report['event_totals']['unsubscribe'] += $row['unsubscribe'];
            }
            
            foreach(array_keys(CRM_Mailing_BAO_Job::fields()) as $field) {
                $row[$field] = $mailing->$field;
            }
            
            if ($mailing->queue) {
                $row['delivered_rate'] = (100.0 * $mailing->delivered ) /
                    $mailing->queue;
                $row['bounce_rate'] = (100.0 * $mailing->bounce ) /
                    $mailing->queue;
                $row['unsubscribe_rate'] = (100.0 * $row['unsubscribe'] ) /
                    $mailing->queue;
            } else {
                $row['delivered_rate'] = 0;
                $row['bounce_rate'] = 0;
                $row['unsubscribe_rate'] = 0;
            }
            
            $row['links'] = array(
                'clicks' => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=click&mid=$mailing_id&jid={$mailing->id}"
                ),
                'queue' =>  CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=queue&mid=$mailing_id&jid={$mailing->id}"
                ),
                'delivered' => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=delivered&mid=$mailing_id&jid={$mailing->id}"
                ),
                'bounce'    => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=bounce&mid=$mailing_id&jid={$mailing->id}"
                ),
                'unsubscribe'   => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=unsubscribe&mid=$mailing_id&jid={$mailing->id}"
                ),
                'forward'       => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=forward&mid=$mailing_id&jid={$mailing->id}"
                ),
                'reply'         => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=reply&mid=$mailing_id&jid={$mailing->id}"
                ),
                'opened'        => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=opened&mid=$mailing_id&jid={$mailing->id}"
                ),
            );

        foreach (array('scheduled_date', 'start_date', 'end_date') as $key) {
                $row[$key] = CRM_Utils_Date::customFormat($row[$key]);
            }
            $report['jobs'][] = $row;
        }

        if (CRM_Utils_Array::value('queue',$report['event_totals'] )) {
            $report['event_totals']['delivered_rate'] = (100.0 * $report['event_totals']['delivered']) / $report['event_totals']['queue'];
            $report['event_totals']['bounce_rate'] = (100.0 * $report['event_totals']['bounce']) / $report['event_totals']['queue'];
            $report['event_totals']['unsubscribe_rate'] = (100.0 * $report['event_totals']['unsubscribe']) / $report['event_totals']['queue'];
        } else {
            $report['event_totals']['delivered_rate'] = 0;
            $report['event_totals']['bounce_rate'] = 0;
            $report['event_totals']['unsubscribe_rate'] = 0;
        }

        /* Get the click-through totals, grouped by URL */
        $mailing->query("
            SELECT      {$t['url']}.url,
                        {$t['url']}.id,
                        COUNT({$t['urlopen']}.id) as clicks,
                        COUNT(DISTINCT {$t['queue']}.id) as unique_clicks
            FROM        {$t['url']}
            LEFT JOIN   {$t['urlopen']}
                    ON  {$t['urlopen']}.trackable_url_id = {$t['url']}.id
            LEFT JOIN  {$t['queue']}
                    ON  {$t['urlopen']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN  {$t['job']}
                    ON  {$t['queue']}.job_id = {$t['job']}.id
            WHERE       {$t['url']}.mailing_id = $mailing_id
                    AND {$t['job']}.is_test = 0
            GROUP BY    {$t['url']}.id");
       
        $report['click_through'] = array();
        
        while ($mailing->fetch()) {
            $report['click_through'][] = array(
                                    'url' => $mailing->url,
                                    'link' =>
                                    CRM_Utils_System::url(
                    'civicrm/mailing/report/event',
                    "reset=1&event=click&mid=$mailing_id&uid={$mailing->id}"),
                                    'link_unique' =>
                                    CRM_Utils_System::url(
                    'civicrm/mailing/report/event',
                    "reset=1&event=click&mid=$mailing_id&uid={$mailing->id}&distinct=1"),
                                    'clicks' => $mailing->clicks,
                                    'unique' => $mailing->unique_clicks,
                                    'rate'   => CRM_Utils_Array::value('delivered',$report['event_totals']) ? (100.0 * $mailing->unique_clicks) / $report['event_totals']['delivered'] : 0
                                );
        }

        $report['event_totals']['links'] = array(
            'clicks' => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=click&mid=$mailing_id"
            ),
            'clicks_unique' => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=click&mid=$mailing_id&distinct=1"
            ),
            'queue' =>  CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=queue&mid=$mailing_id"
            ),
            'delivered' => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=delivered&mid=$mailing_id"
            ),
            'bounce'    => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=bounce&mid=$mailing_id"
            ),
            'unsubscribe'   => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=unsubscribe&mid=$mailing_id"
            ),
            'forward'         => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=forward&mid=$mailing_id"
            ),
            'reply'         => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=reply&mid=$mailing_id"
            ),
            'opened'        => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=opened&mid=$mailing_id"
            ),
        );

        return $report;
    }

    /**
     * Get the count of mailings 
     *
     * @param
     * @return int              Count
     * @access public
     */
    public function getCount() {
        $this->selectAdd();
        $this->selectAdd('COUNT(id) as count');
        
        $session =& CRM_Core_Session::singleton();
        $this->domain_id = $session->get('domainID');
        
        $this->find(true);
        
        return $this->count;
    }


    static function checkPermission( $id ) {
        if ( ! $id ) {
            return;
        }

        $mailingIDs =& CRM_Mailing_BAO_Mailing::mailingACLIDs( );
        if ( ! in_array( $id,
                         $mailingIDs ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this mailing report' ) );
        }
        return;
    }

    static function mailingACL( ) {
        $mailingACL = " ( 0 ) ";

        $mailingIDs =& self::mailingACLIDs( );
        if ( ! empty( $mailingIDs ) ) {
            $mailingIDs = implode( ',', $mailingIDs );
            $mailingACL = " civicrm_mailing.id IN ( $mailingIDs ) ";
        }
        return $mailingACL;
    }

    static function &mailingACLIDs( ) {
        $mailingIDs = array( );

        // get all the groups that this user can access
        // if they dont have universal access
        $groups   = CRM_Core_PseudoConstant::group( );
        if ( ! empty( $groups ) ) {
            $groupIDs = implode( ',',
                                 array_keys( $groups ) );
            // get all the mailings that are in this subset of groups
            $query = "
SELECT DISTINCT( m.id ) as id
  FROM civicrm_mailing m,
       civicrm_mailing_group g
 WHERE g.mailing_id   = m.id
   AND g.entity_table = 'civicrm_group'
   AND g.entity_id IN ( $groupIDs )";
            $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            $mailingIDs = array( );
            while ( $dao->fetch( ) ) {
                $mailingIDs[] = $dao->id;
            }
        }

        return $mailingIDs;
    }

    /**
     * Get the rows for a browse operation
     *
     * @param int $offset       The row number to start from
     * @param int $rowCount     The nmber of rows to return
     * @param string $sort      The sql string that describes the sort order
     * 
     * @return array            The rows
     * @access public
     */
    public function &getRows($offset, $rowCount, $sort, $additionalClause = null, $additionalParams = null ) {
        $mailing    = self::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $group      = CRM_Mailing_DAO_Group::getTableName( );
        $session    =& CRM_Core_Session::singleton();
        $domain_id  = $session->get('domainID');

        $mailingACL = self::mailingACL( );

        $query = "
            SELECT      $mailing.id,
                        $mailing.name, 
                        $job.status, 
                        MIN($job.scheduled_date) as scheduled_date, 
                        MIN($job.start_date) as start_date,
                        MAX($job.end_date) as end_date
            FROM        $mailing 
                        LEFT JOIN $job ON ( $job.mailing_id = $mailing.id AND $job.is_test = 0)
            WHERE       $mailing.domain_id = $domain_id
              AND       $mailingACL $additionalClause
            GROUP BY    $mailing.id ";
        
        if ($sort) {
            $orderBy = trim( $sort->orderBy() );
            if ( ! empty( $orderBy ) ) {
                $query .= " ORDER BY $orderBy";
            }
        }

        if ($rowCount) {
            $query .= " LIMIT $offset, $rowCount ";
        }

        if ( ! $additionalParams ) {
            $additionalParams = array( );
        }

        $dao = CRM_Core_DAO::executeQuery( $query, $additionalParams );

        
        $rows = array();
        while ($dao->fetch()) {
            $rows[] = array(
                            'id'            => $dao->id,                            
                            'name'          => $dao->name, 
                            'status'        => CRM_Mailing_BAO_Job::status($dao->status), 
                            'scheduled'     => CRM_Utils_Date::customFormat($dao->scheduled_date),
                            'scheduled_iso' => $dao->scheduled_date,
                            'start'         => CRM_Utils_Date::customFormat($dao->start_date), 
                            'end'           => CRM_Utils_Date::customFormat($dao->end_date)
                            );
        }
        return $rows;
    }


    /**
     * compose the url to show details of activityHistory for CiviMail
     *
     * @param int $id
     *
     * @static
     * @access public
     */

    static function showEmailDetails( $id )
    {
        return CRM_Utils_System::url('civicrm/mailing/report', "mid=$id");
        
    }

     /**
     * Delete Mails and all its associated records
     * 
     * @param  int  $id id of the mail to delete
     *
     * @return void
     * @access public
     * @static
     */
    public static function del($id) {
        if ( empty( $id ) ) {
            CRM_Core_Error::fatal( );
        }

        $dao = & new CRM_Mailing_DAO_Mailing();
        $dao->id = $id;
        $dao->delete( );
        
        CRM_Core_Session::setStatus(ts('Selected mailing has been deleted.'));
    }
    
    /**
     * Delete Jobss and all its associated records 
     * related to test Mailings
     *
     * @param  int  $id id of the Job to delete
     *
     * @return void
     * @access public
     * @static
     */
    public static function delJob($id) {
        if ( empty( $id ) ) {
            CRM_Core_Error::fatal( );
        }

        $dao     = new CRM_Mailing_BAO_Job();
        $dao->id = $id;
        $dao->delete();
    }

    function getReturnProperties( ) {
        $tokens =& $this->getTokens( );

        $properties = array( );
        if ( isset( $tokens['html'] ) &&
             isset( $tokens['html']['contact'] ) ) {
            $properties = array_merge( $properties, $tokens['html']['contact'] );
        }

        if ( isset( $tokens['text'] ) &&
             isset( $tokens['text']['contact'] ) ) {
            $properties = array_merge( $properties, $tokens['text']['contact'] );
        }

        $returnProperties = array( );
        $returnProperties['display_name'] = 
            $returnProperties['contact_id'] = $returnProperties['preferred_mail_format'] = 1;

        foreach ( $properties as $p ) {
            $returnProperties[$p] = 1;
        }

        return $returnProperties;
    }

}

?>
