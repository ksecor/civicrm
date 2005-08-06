<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */
require_once 'Common.php';
require_once 'GenDataset.php';
require_once 'InsertContact.php';
require_once 'InsertRel.php';
require_once 'UpdateContact.php';
require_once 'AddContactToGroup.php';
require_once 'DelContact.php';
require_once 'PartialNameSearch.php';
require_once 'GroupSearch.php';
require_once 'Result.php';

class test_RSTest_Run
{
    private $_recordSetSize;
    private $_doIt;

    // This constant is used to set size fo dataset. 
    // The value entered is multiple of 1000 or 1k
    // Ex. if the constant value is 10 then dataset size will be (1000 * 10)
    private $_sizeOfDS      = 3;
    // Following constant is used for setting the step for generating the dataset.
    private $_stepOfDS      = 500;
    // Following array is used to store the timings for all the steps of generating the dataset.
    private $_genDataset    = array();
    
    // Following constant is used for setting the no of records to be inserted into the database.
    // Value should be multiple of 10.
    private $_insertRecord  = 100;
    // Following constant is used for setting the step for inserting the contact. 
    private $_stepOfInsert  = 10;
    // Following array is used to store the timings for all the steps of inserting the contact.
    private $_insertContact = array();
    
    // Following constant is used for setting the no of records to be updated from the database. 
    private $_updateRecord  = 1000;
    // Following constant is used for setting the starting record from which update should start. 
    private $_startRecord   = 500;
    // Following constant is used for setting the step for updaing the contacts.
    private $_stepOfUpdate  = 500;
    // Following array is used to store the timings for all the steps of updations. 
    private $_updateContact = array();
    
    // Following constant is used for setting the no of contact for which relationships needs to be entered
    private $_insertRel        = 1500;
    // Following constant is used for setting the starting contact from which the relationships needs to be entered.
    private $_startRel         = 0;
    // Following constant is used for setting the step for inserting relationships.
    private $_stepOfInsertRel  = 500;
    // Following array is used to store the timings for all the steps of updations. 
    private $_insertRelTime    = array();
    
    // Following constant is used for setting the no of Contacts which needs to be added to a Group. 
    private $_addToGroup       = 1500;
    // Following constant is used for setting the starting contact from which Contacts needs to be added to a Group.
    private $_startOfAdd       = 0;
    // Following constant is used for setting the step for adding Contact to a Group.
    private $_stepOfAddToGroup = 500;
    // Following array is used to store the timings for all the steps of adding Contact to a Group. 
    private $_addToGroupTime   = array();

    // Following constant is used for setting the no of Contacts which needs to be added to a Group. 
    private $_deleteContact       = 500;
    // Following constant is used for setting the starting contact from which Contacts needs to be added to a Group.
    private $_startOfDelete       = 500;
    // Following constant is used for setting the step for adding Contact to a Group.
    private $_stepOfDeleteContact = 500;
    // Following array is used to store the timings for all the steps of adding Contact to a Group. 
    private $_deleteContactTime   = array();
    
    // Following array is used to store the timings for Partial Name Search.
    private $_partialNameSearchTime = array();
    // Following variable is used to store the result from the partial name search
    private $_searchResultPN        = array();

    // Following array is used to store the timings for Partial Name Search.
    private $_groupSearchTime = array();
    // Following variable is used to store the result from the partial name search
    private $_searchResultG   = array();

    private $_startTimeG;
    private $_endTimeG;
    
    private $_startTimeIC;
    private $_endTimeIC;
    
    private $_startTimeUC;
    private $_endTimeUC;
    
    private $_startTimeIR;
    private $_endTimeIR;

    private $_startTimeAG;
    private $_endTimeAG;

    private $_startTimeDC;
    private $_endTimeDC;
    
    private $_startTimePNS;
    private $_endTimePNS;

    private $_startTimeGS;
    private $_endTimeGS;

    /**
     * Call to the Common.php
     *
     * This function is used for fixing the Recordset Size 
     * for Stress Testing.
     *
     * @return   void
     * @access   private
     *
     */
    function _callCommon()
    {
        $objCommon            =& new test_RSTest_Common();
        $this->_recordSetSize = $objCommon->recordsetSize($this->_sizeOfDS);
    }

    /**
     * Call to the GenDataset.php
     *
     * This function is used for Generating Dataset for Stress Testing.
     * Dataset is generated through Steps. 
     * In each Step fixed size of Recordset gets generated. 
     * 
     * @return   void
     * @access   private
     *
     */
    function _callGenDataset()
    {
        $startID = 0;
        echo "\n Data Generation started. \n";
        for ($i=0; $i<($this->_recordSetSize / $this->_stepOfDS); $i++) {
            $objGenDataset       =& new test_RSTest_GenDataset($this->_stepOfDS);
            $this->_startTimeG   = microtime(true);
            $objGenDataset->run($startID);
            $this->_endTimeG     = microtime(true);
            $this->_genDataset[$i] = $this->_endTimeG - $this->_startTimeG;
            $startID = $startID + $this->_stepOfDS;
        }
        echo "\n Data Generation Successfully Completed.\n";
    }
    
    /**
     * Call to the InsertContact.php
     *
     * This function is used for Inserting Contact in the already generated Dataset.
     * Contacts are Inserted through Steps. 
     * In each Step fixed number of Contacts gets Inserted. 
     * 
     * @return   void
     * @access   private
     */
    function _callInsertContact()
    {
        $startID = 0;
        echo "\n Contacts Insertion started. \n";
        for ($i=0; $i<($this->_insertRecord / $this->_stepOfInsert); $i++) {
            for ($tmp=0; $tmp<$this->_stepOfInsert; $tmp++) {
                echo ".";
                ob_flush();
                flush();
            }
            if (!($i)) {
                $setDomain = true;
            }
            $objInsertContact  =& new test_RSTest_InsertContact($this->_stepOfInsert);
            $this->_startTimeIC = microtime(true);
            $objInsertContact->run($this->_recordSetSize, $startID);
            $this->_endTimeIC   = microtime(true);
            $this->_insertContact[$i] = $this->_endTimeIC - $this->_startTimeIC;
            $startID = $startID + $this->_stepOfInsert;
        }
        echo "\n Contacts Successfully Inserted.\n";
    }
    
    /**
     * Call to the InsertRel.php
     *
     * This function is used for Inserting Relationship amongst already generated Contacts.
     * Relationships are Inserted through Steps. 
     * In each Step fixed number of Relationships gets Inserted. 
     * 
     * @return   void
     * @access   private
     */
    function _callInsertRel()
    {
        if (($this->_startRel + $this->_insertRel) <= ($this->_sizeOfDS * 1000)) {
            echo "\n Relationships Insertion started. \n";
            $startID = $_startRel;
            for ($i=0; $i<($this->_insertRel / $this->_stepOfInsertRel); $i++) {
                for ($tmp=0; $tmp<$this->_stepOfDS; $tmp++) {
                    echo ".";
                    ob_flush();
                    flush();
                }
                $objInsertRel  =& new test_RSTest_InsertRel();
                $this->_startTimeIR = microtime(true);
                $objInsertRel->run($startID, $this->_stepOfInsertRel);
                $this->_endTimeIR   = microtime(true);
                $this->_insertRelTime[$i] = $this->_endTimeIR - $this->_startTimeIR;
                $startID = $startID + $this->_stepOfInsertRel;
            }
            echo "\n Relationships Successfully Inserted.\n";
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of Contacts for which Relationships are to be inserted..!!! \n";
            echo "**********************************************************************************\n";
        }
    }
    
    /**
     * Call to the UpdateContact.php
     *
     * This function is used for Updaing already generated Contacts.
     * Contacts are Updated through Steps. 
     * In each Step fixed number of Contacts gets Updated. 
     * 
     * @return   void
     * @access   private
     */
    function _callUpdateContact()
    {
        if (($this->_startRecord + $this->_updateRecord) <= ($this->_sizeOfDS * 1000)) {
            echo "\n Updating Contacts . \n";
            $startID = $this->_startRecord;
            for ($i=0; $i<($this->_updateRecord / $this->_stepOfUpdate); $i++) {
                $objUpdateContact   =& new test_RSTest_UpdateContact();
                $this->_startTimeUC = microtime(true);
                $objUpdateContact->run($startID, $this->_stepOfUpdate);
                $this->_endTimeUC   = microtime(true);
                $this->_updateContact[$i] = $this->_endTimeUC - $this->_startTimeUC;
                $startID = $startID + $this->_stepOfUpdate;
            }
            echo "\n Contacts Successfully Updated.\n";
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of records need to be Updated..!!! \n";
            echo "**********************************************************************************\n";
        }
    }
    
    /**
     * Call to the AddContactToGroup.php
     *
     * This function is used for Adding already generated Contacts to Groups.
     * Contacts are Added to Groups through Steps. 
     * In each Step fixed number of Contacts gets Added to Groups. 
     * 
     * @return   void
     * @access   private
     */
    function _callAddContactToGroup()
    {
        if (($this->_startOfAdd + $this->_addToGroup) <= ($this->_sizeOfDS * 1000)) {
            echo "\n Adding Contacts to Group. \n";
            $startID = $this->_startOfAdd;
            for ($i=0; $i<($this->_addToGroup / $this->_stepOfAddToGroup); $i++) {
                $objAddContactToGroup   =& new test_RSTest_AddContactToGroup();
                $this->_startTimeAG = microtime(true);
                $objAddContactToGroup->run($startID, $this->_stepOfAddToGroup);
                $this->_endTimeAG   = microtime(true);
                $this->_addToGroupTime[$i] = $this->_endTimeAG - $this->_startTimeAG;
                $startID = $startID + $this->_stepOfAddToGroup;
            }
            echo "\n Contacts Successfully Added to Groups.\n";
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of Contacts need to be added to Groups..!!! \n";
            echo "**********************************************************************************\n";
        }
    }
    
    /**
     * Call to the DeleteContact.php
     *
     * This function is used for Deleting Contacts.
     * Contacts are Deleted through Steps. 
     * In each Step fixed number of Contacts gets Deleted. 
     * 
     * @return   void
     * @access   private
     */
    function _callDeleteContact()
    {
        if (($this->_startOfDelete + $this->_deleteContact) <= ($this->_sizeOfDS * 1000)) {
            echo "\n Deleting Contacts . \n";
            $startID = $this->_startOfDelete;
            for ($i=0; $i<($this->_deleteContact / $this->_stepOfDeleteContact); $i++) {
                $objDeleteContact   =& new test_RSTest_DelContact();
                $this->_startTimeDC = microtime(true);
                $objDeleteContact->run($startID, $this->_stepOfDeleteContact);
                $this->_endTimeDC   = microtime(true);
                $this->_deleteContactTime[$i] = $this->_endTimeDC - $this->_startTimeDC;
                $startID = $startID + $this->_stepOfDeleteContact;
            } 
            echo "\n Contacts Successfully Deleted.\n";
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of Contacts need to be Deleted..!!! \n";
            echo "**********************************************************************************\n";
        }
    }

    /**
     * Call to the PartialNameSearch.php
     *
     * This function is used for Searching Contacts.
     * 
     * @return   void
     * @access   private
     */
    private function _callPartialNameSearch()
    {
        $objPartialNameSearch         = new test_RSTest_PartialNameSearch();
        $this->_startTimePNS          = microtime(true);
        $this->_searchResultPN        = $objPartialNameSearch->run();
        $this->_endTimePNS            = microtime(true);
        $this->_partialNameSearchTime = $this->_endTimePNS - $this->_startTimePNS;
    }

    private function _callGroupSearch()
    {
        $objGroupSearch         = new test_RSTest_GroupSearch();
        $this->_startTimeGS     = microtime(true);
        $this->_searchResultG   = $objGroupSearch->run();
        $this->_endTimeGS       = microtime(true);
        $this->_groupSearchTime = $this->_endTimeGS - $this->_startTimeGS;
    }
    
    /**
     * Call to the Result.php
     *
     * This function is used for handling Results from the Stress Test.
     *
     * @return   void
     * @access   private
     */
    function _callResult()
    {
        $genDataset = array('size'  => $this->_recordSetSize,
                            'step'  => $this->_stepOfDS,
                            'time'  => $this->_genDataset,
                            );
        
        $insertContact = array('size'  => $this->_insertRecord,
                               'step'  => $this->_stepOfInsert,
                               'time'  => $this->_insertContact,
                               );
        
        $updateContact = array('size'  => $this->_updateRecord,
                               'step'  => $this->_stepOfUpdate,
                               'start' => $this->_startRecord,
                               'time'  => $this->_updateContact,
                               );
        
        $insertRel = array('size'  => $this->_insertRel,
                           'step'  => $this->_stepOfInsertRel,
                           'start' => $this->_startRel,
                           'time'  => $this->_insertRelTime,
                           );
        
        $addToGroup = array('size'  => $this->_addToGroup,
                            'step'  => $this->_stepOfAddToGroup,
                            'start' => $this->_startOfAdd,
                            'time'  => $this->_addToGroupTime,
                            );

        $deleteContact = array('size'  => $this->_deleteContact,
                               'step'  => $this->_stepOfDeleteContact,
                               'start' => $this->_startOfDelete,
                               'time'  => $this->_deleteContactTime,
                               );
        
        $partialNameSearch = array('count'    => $this->_searchResultPN['count'],
                                   'criteria' => $this->_searchResultPN['criteria'],
                                   'time'     => $this->_partialNameSearchTime
                                   );
        
        $groupSearch = array('count'    => $this->_searchResultG['count'],
                             'criteria' => $this->_searchResultG['criteria'],
                             'time'     => $this->_groupSearchTime
                             );

        $objResult = new test_RSTest_Result();
        $objResult->run($this->_doIt, $genDataset, $insertContact, $updateContact, $insertRel, $addToGroup, $deleteContact, $partialNameSearch, $groupSearch);
    }
    
    /**
     * Running the Stress Test.
     *
     * This function is used for Running the Stress Test.
     * User will be having choice to decide which Stress Test to Run.
     * 
     * @return   void
     * @access   public
     */
    function run()
    {
        $this->_doIt = 0;
        echo "\n**********************************************************************************\n";
        fwrite(STDOUT, "Options for Stress Testing \n");
        $options = array ('L' => 'All Operations will be done for Stress Test except Searching Options. The Operations are - Inserting Contact, Updating Contact, Insert Relationship, Adding Contact to Group and Deleting Contacts',
                          'I' => 'Inserting Contacts',
                          'U' => 'Updating Contacts',
                          'R' => 'Inserting Relationship',
                          'A' => 'Adding Contacts to Group',
                          'D' => 'Delete Contacts',
                          'P' => 'Partial Name Search. (Before Searching, fresh Dataset will be Generated.)',
                          'G' => 'Group Search. (Before Searching, fresh Dataset will be Generated.)'
                          );
        foreach ($options as $val => $desc) {
            fwrite(STDOUT, "\n" . $val . " : " . $desc . "\n");
        }
        echo "\n**********************************************************************************\n";
        
        do {
            fwrite(STDOUT, "Enter Your Option : \t");
            $selection = strtoupper(fgetc(STDIN));
        } while (trim($selection) == '');

        //if ((array_key_exists($selection, $options)) || (array_key_exists(strtolower($selection), array_change_key_case($options, CASE_LOWER))) ) {
        if (array_key_exists($selection, $options)) {
            $this->_doIt = 1;
            echo "\nStress Test Started \n";
            $this->_callCommon();
            $this->_callGenDataset();
            switch (strtolower($selection)) {
            case 'l':
                $this->_callInsertContact();
                $this->_callUpdateContact();
                $this->_callInsertRel();
                $this->_callAddContactToGroup();
                $this->_callDeleteContact();
                break;
            case 'i':
                $this->_callInsertContact();
                break;
            case 'u':
                $this->_callUpdateContact();
                break;
            case 'r':
                $this->_callInsertRel();
                break;
            case 'a':
                $this->_callAddContactToGroup();
                break;
            case 'd':
                $this->_callDeleteContact();
                break;
            case 'p':
                $this->_callPartialNameSearch();
                break;
            case 'g':
                $this->_callGroupSearch();
                break;
            }
            $this->_callResult();
        } else {
            echo "\n**********************************************************************************\n";
            echo "Not a Valid Choice \n";
            echo "**********************************************************************************\n";
        }
    }
}

function user_access( $str ) {
    return true;
}

$objRun =& new test_RSTest_Run();

$objRun->run();

?>