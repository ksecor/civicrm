<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Import/Parser.php';

require_once 'api/crm.php';

/**
 * class to parse contribution csv files
 */
class CRM_Contribute_Import_Parser_Contribution extends CRM_Contribute_Import_Parser {

    protected $_mapperKeys;

    private $_contactIdIndex;
    private $_totalAmountIndex;
    private $_receiveDateIndex;
    private $_contributionTypeIndex;

    /**
     * Array of succesfully imported contribution id's
     *
     * @array
     */
    protected $_newContributions;

    /**
     * class constructor
     */
    function __construct( &$mapperKeys ) {
        parent::__construct();
        $this->_mapperKeys =& $mapperKeys;
    }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function init( ) {
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $fields =& CRM_Contribute_BAO_Contribution::importableFields( );

        foreach ($fields as $name => $field) {
            $this->addField( $name, $field['title'], $field['type'], $field['headerPattern'], $field['dataPattern']);
        }

        $this->_newContributions = array();

        $this->setActiveFields( $this->_mapperKeys );

        // FIXME: we should do this in one place together with Form/MapField.php
        $this->_contactIdIndex        = -1;
        $this->_totalAmountIndex      = -1;
        $this->_receiveDateIndex      = -1;
        $this->_contributionTypeIndex = -1;

        $index = 0;
        foreach ( $this->_mapperKeys as $key ) {
            switch ($key) {
            case 'contact_id':
                $this->_contactIdIndex        = $index;
                break;
            case 'total_amount':
                $this->_totalAmountIndex      = $index;
                break;
            case 'receive_date':
                $this->_receiveDateIndex      = $index;
                break;
            case 'contribution_type':
                $this->_contributionTypeIndex = $index;
                break;
            }
            $index++;
        }
    }

    /**
     * handle the values in mapField mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean
     * @access public
     */
    function mapField( &$values ) {
        return CRM_Contribute_Import_Parser::VALID;
    }


    /**
     * handle the values in preview mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function preview( &$values ) {
        return $this->summary($values);
    }

    /**
     * handle the values in summary mode
     *
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function summary( &$values ) {
        $erroneousField = null;
        $response = $this->setActiveFieldValues( $values, $erroneousField );
        if ($response != CRM_Contribute_Import_Parser::VALID) {
            array_unshift($values, ts('Invalid field value: %1', array(1 => $this->_activeFields[$erroneousField]->_title)));
            return CRM_Contribute_Import_Parser::ERROR;
        }
        $errorRequired = false;
        if ($this->_contactIdIndex        < 0 or
            $this->_totalAmountIndex      < 0 or
            $this->_receiveDateIndex      < 0 or
            $this->_contributionTypeIndex < 0) {
            $errorRequired = true;
        }
        if ($errorRequired) {
            array_unshift($values, ts('Missing required fields'));
            return CRM_Contribute_Import_Parser::ERROR;
        }
        return CRM_Contribute_Import_Parser::VALID;
    }

    /**
     * handle the values in import mode
     *
     * @param int $onDuplicate the code for what action to take on duplicates
     * @param array $values the array of values belonging to this line
     *
     * @return boolean      the result of this processing
     * @access public
     */
    function import( $onDuplicate, &$values) {
        // first make sure this is a valid line
        $response = $this->summary( $values );
        if ( $response != CRM_Contribute_Import_Parser::VALID ) {
            return $response;
        }

        $params =& $this->getActiveFieldParams( );
        $formatted = array();
        
        static $indieFields = null;
        if ($indieFields == null) {
            require_once('CRM/Contribute/DAO/Contribution.php');
            $tempIndieFields =& CRM_Contribute_DAO_Contribution::import();
            $indieFields = $tempIndieFields;
        }

        foreach ($params as $key => $field) {
            if ($field == null || $field === '') {
                continue;
            }
            $value = array($key => $field);
            _crm_add_formatted_contrib_param($value, $formatted);
        }
        $newContribution = crm_create_contribution_formatted( $formatted, $onDuplicate );

        if ( is_a( $newContribution, CRM_Core_Error ) ) {
            foreach ($newContribution->_errors[0]['params'] as $cid) {
                $contributionId = $cid;
            }
        } else {
            $contributionId = $newContribution->id;
        }
        
        //dupe checking
        if ( is_a( $newContribution, CRM_Core_Error ) ) 
        {    
            $code = $newContribution->_errors[0]['code'];
            if ($code == CRM_Core_Error::DUPLICATE_CONTRIBUTION) {
                $urls = array( );
            
                foreach ($newContribution->_errors[0]['params'] as $cid) {
                    $urls[] = CRM_Utils_System::url('civicrm/contribution/view',
                                                    'reset=1&cid=' . $cid, true);
                }
                
                $url_string = implode("\n", $urls);
                array_unshift($values, $url_string); 
                
                /* Params only had one id, so shift it out */
                $contributionId = array_shift($newContribution->_errors[0]['params']);
            
                if ($onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_UPDATE) {
                    $newContribution = crm_update_contribution_formatted($contributionId, $formatted, true);
                } else if ($onDuplicate == CRM_Contribute_Import_Parser::DUPLICATE_FILL) {
                    $newContribution = crm_update_contribution_formatted($contributionId, $formatted, false);
                } // else skip does nothing and just returns an error code.
            
                if ($newContribution && ! is_a($newContribution, CRM_Core_Error)) {
                    $this->_newContributions[] = $newContribution->id;
                }
                //CRM-262 No Duplicate Checking  
                if ($onDuplicate != CRM_Contribute_Import_Parser::DUPLICATE_SKIP) {
                    return CRM_Contribute_Import_Parser::DUPLICATE; 
                }
                return CRM_Contribute_Import_Parser::VALID;
            } else { 
                /* Not a dupe, so we had an error */
                array_unshift($values, $newContribution->_errors[0]['message']);
                return CRM_Contribute_Import_Parser::ERROR;
            }
        }
        
        if ($newContribution && ! is_a($newContribution, CRM_Core_Error)) {
            $this->_newContributions[] = $newContribution->id;
        }
        return CRM_Contribute_Import_Parser::VALID;
    }
   
    /**
     * Get the array of succesfully imported contribution id's
     *
     * @return array
     * @access public
     */
    function &getImportedContributions() {
        return $this->_newContributions;
    }
   
    /**
     * Get the array of succesfully imported related contact id's
     *
     * @return array
     * @access public
     */
#   function &getRelatedImportedContacts() {
#       return $this->_newRelatedContacts;
#   }

    /**
     * the initializer code, called before the processing
     *
     * @return void
     * @access public
     */
    function fini( ) {
    }

}

?>
