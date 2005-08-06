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

class test_RSTest_Result
{
    private $_recordSetSize;
    private $_doIt;

    // Following constant is used for setting the step for generating the dataset.
    private $_stepOfDS;
    // Following array is used to store the timings for all the steps of generating the dataset.
    private $_genDataset;
    
    // Following constant is used for setting the no of records to be inserted into the database.
    // Value should be multiple of 10.
    private $_insertRecord;
    // Following constant is used for setting the step for inserting the contact. 
    private $_stepOfInsert;
    // Following array is used to store the timings for all the steps of inserting the contact.
    private $_insertContact;
    
    // Following constant is used for setting the no of records to be updated from the database. 
    private $_updateRecord;
    // Following constant is used for setting the starting record from which update should start. 
    private $_startRecord;
    // Following constant is used for setting the step for updaing the contacts.
    private $_stepOfUpdate;
    // Following array is used to store the timings for all the steps of updations. 
    private $_updateContact;
    
    // Following constant is used for setting the no of contact for which relationships needs to be entered
    private $_insertRel;
    // Following constant is used for setting the starting contact from which the relationships needs to be entered.
    private $_startRel;
    // Following constant is used for setting the step for inserting relationships.
    private $_stepOfInsertRel;
    // Following array is used to store the timings for all the steps of updations. 
    private $_insertRelTime;
    
    // Following constant is used for setting the no of Contacts which needs to be added to a Group. 
    private $_addToGroup;
    // Following constant is used for setting the starting contact from which Contacts needs to be added to a Group.
    private $_startOfAdd;
    // Following constant is used for setting the step for adding Contact to a Group.
    private $_stepOfAddToGroup;
    // Following array is used to store the timings for all the steps of adding Contact to a Group. 
    private $_addToGroupTime;

    // Following constant is used for setting the no of Contacts which needs to be added to a Group. 
    private $_deleteContact;
    // Following constant is used for setting the starting contact from which Contacts needs to be added to a Group.
    private $_startOfDelete;
    // Following constant is used for setting the step for adding Contact to a Group.
    private $_stepOfDeleteContact;
    // Following array is used to store the timings for all the steps of adding Contact to a Group. 
    private $_deleteContactTime;

    // Following array is used to store the timings for Partial Name Search.
    private $_partialNameSearchTime;
    // Following variable is used to store the Count of Contacts found from the Partial Name Search.
    private $_searchCountPN;
    // Following variable is used to store the Criteria for Partial Name Search.
    private $_searchCriteriaPN;

    // Following array is used to store the timings for Partial Name Search.
    private $_groupSearchTime;
    // Following variable is used to store the Count of Contacts found from the Partial Name Search.
    private $_searchCountG;
    // Following variable is used to store the Criteria for Partial Name Search.
    private $_searchCriteriaG;
    
    private function _set($doIt, $genDataset, $insertContact, $updateContact, $insertRel, $addToGroup, $deleteContact, $partialNameSearch, $groupSearch)
    {
        $this->_doIt = $doIt;
        
        $this->_recordSetSize = $genDataset['size'];

        $this->_stepOfDS = $genDataset['step'];

        $this->_genDataset = $genDataset['time'];
        
        $this->_insertRecord = $insertContact['size'];

        $this->_stepOfInsert = $insertContact['step'];

        $this->_insertContact = $insertContact['time'];

        $this->_updateRecord = $updateContact['size'];

        $this->_startRecord = $updateContact['start'];

        $this->_stepOfUpdate = $updateContact['step'];

        $this->_updateContact = $updateContact['time'];

        $this->_insertRel = $insertRel['size'];
     
        $this->_startRel = $insertRel['start'];
     
        $this->_stepOfInsertRel = $insertRel['step'];
     
        $this->_insertRelTime = $insertRel['time'];
        
        $this->_addToGroup = $addToGroup['size'];
    
        $this->_startOfAdd = $addToGroup['start'];
    
        $this->_stepOfAddToGroup = $addToGroup['step'];
    
        $this->_addToGroupTime = $addToGroup['time'];
        
        $this->_deleteContact = $deleteContact['size'];

        $this->_startOfDelete = $deleteContact['start'];

        $this->_stepOfDeleteContact = $deleteContact['step'];

        $this->_deleteContactTime = $deleteContact['time'];
        
        $this->_searchCountPN = $partialNameSearch['count'];
    
        $this->_searchCriteriaPN = $partialNameSearch['criteria'];
        
        $this->_partialNameSearchTime = $partialNameSearch['time'];
        
        $this->_searchCountG = $groupSearch['count'];
        
        $this->_searchCriteriaG = $groupSearch['criteria'];
        
        $this->_groupSearchTime = $groupSearch['time'];
    }

    /**
     * Creating Log.
     *
     * This function is used for Creating Log of the Stress Test.
     * 
     * @return   void
     * @access   private
     */    
    private function _createLog()
    {
        if (!(is_dir('./LOG'))) {
            mkdir('LOG');
        }
        $file_name = "LOG/stressTest.LOG." . date("YmdHis");
        
        $file_pointer = fopen($file_name, "w");
        
        $string = "\n Stress Test Started \n";
        
        if (!(empty($this->_genDataset))) {
            $string .= "\n**********************************************************************************\n";
            $string .= "Recordset of Size " . ($this->_recordSetSize / 1000) . " K is Generated. Records were generated through the step of " . $this->_stepOfDS . " Contacts \n";
            for ($ig=0; $ig<count($this->_genDataset); $ig++) {
                $string .= "Time taken for step " . ($kg = $ig + 1) . " : " . $this->_genDataset[$ig] . " seconds\n";
            }
            $string .= "**********************************************************************************\n";
        }
        
        if (!(empty($this->_insertContact))) {
            $string .= "\n**********************************************************************************\n";
            $string .= $this->_insertRecord . " Contact(s) Inserted into the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsert . " Contacts \n";
            
            for ($ii=0; $ii<count($this->_insertContact); $ii++) {
                $string .= "Time taken for step " . ($ki = $ii + 1) . " : " . $this->_insertContact[$ii] . " seconds\n";
            }
            $string .= "**********************************************************************************\n";
        }

        if (!(empty($this->_insertRelTime))) {
            $string .= "\n**********************************************************************************\n";
            $string .= "Relationships entered for Contact No. " . $this->_startRel . " To Contact No. " . ($this->_startRel + $this->_insertRel) . " From the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsertRel . " Contacts \n";
            
            for ($ir=0; $ir<count($this->_insertRelTime); $ir++) {
                $string .= "Time taken for step " . ($kr = $ir + 1) . " : " . $this->_insertRelTime[$ir] . " seconds\n";
            }
            $string .= "**********************************************************************************\n";
        }
        
        if (!(empty($this->_updateContact))) {
            $string .= "\n**********************************************************************************\n";
            $string .= "Contact No. " . $this->_startRecord . " To Contact No. " . ($this->_startRecord + $this->_updateRecord) . " Updated from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfUpdate . " Contacts \n";
            
            for ($iu=0; $iu<count($this->_updateContact); $iu++) {
                $string .= "Time taken for step " . ($ku = $iu + 1) . " : " . $this->_updateContact[$iu] . " seconds\n";
            }
            $string .= "**********************************************************************************\n";
        }
        
        if (!(empty($this->_addToGroupTime))) {
            $string .= "\n**********************************************************************************\n";
            $string .= "Contact No. " . $this->_startOfAdd . " To Contact No. " . ($this->_startOfAdd + $this->_addToGroup) . " Added to Groups from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfAddToGroup . " Contacts \n";
            
            for ($iag=0; $iag<count($this->_addToGroupTime); $iag++) {
                $string .= "Time taken for step " . ($kag = $iag + 1) . " : " . $this->_addToGroupTime[$iag] . " seconds\n";
            }
            $string .= "**********************************************************************************\n";
        }
        
        if (!(empty($this->_deleteContactTime))) {
            $string .= "\n**********************************************************************************\n";
            $string .= "Contact No. " . $this->_startOfDelete . " To Contact No. " . ($this->_startOfDelete + $this->_deleteContact) . " Deleted from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfDeleteContact . " Contacts \n";
            
            for ($id=0; $id<count($this->_deleteContactTime); $id++) {
                $string .= "Time taken for step " . ($kd = $id + 1) . " : " . $this->_deleteContactTime[$id] . " seconds\n";
            }
            $string .= "**********************************************************************************\n";
        }
        
        if (!(empty($this->_partialNameSearchTime))) {
            $string .= "\n**********************************************************************************\n";
            $string .= "Partial Name Search Carried out on the dataset of size " . ($this->_recordSetSize / 1000) . " K \n";
            $string .= "Criteria for the Search : \n";
            $string .= "------------------------------------------------------\n";
            foreach ($this->_searchCriteriaPN as $criteriaKeyPN => $criteriaValPN) {
                $string .= "{$criteriaKeyPN} : {$criteriaValPN}\n";
            }
            $string .= "------------------------------------------------------\n";
            $string .= "Total '{$this->_searchCountPN}' Contacts found.\n";
            $string .= "And Time Taken for Search : {$this->_partialNameSearchTime} seconds\n";
            $string .= "**********************************************************************************\n";
        }

        if (!(empty($this->_groupSearchTime))) {
            $string .= "\n**********************************************************************************\n";
            $string .= "Group Search Carried out on the dataset of size " . ($this->_recordSetSize / 1000) . " K \n";
            $string .= "Criteria for the Search : \n";
            $string .= "------------------------------------------------------\n";
            foreach ($this->_searchCriteriaG as $criteriaKeyG => $criteriaValG) {
                $string .= "{$criteriaKeyG} : {$criteriaValG} \n";
            }
            $string .= "------------------------------------------------------\n";
            $string .= "Total '{$this->_searchCountG}' Contacts found.\n";
            $string .= "And Time Taken for Search : {$this->_groupSearchTime} seconds\n";
            $string .= "**********************************************************************************\n";
        }
        
        fwrite($file_pointer, $string);
                
        fclose($file_pointer);
        echo "\n**********************************************************************************\n";
        echo " Results Successfully written to the file : " . getcwd() . "/" .$file_name. "\n";
        echo "**********************************************************************************\n";
    }

    /**
     * Printing Results.
     *
     * This function is used for Printing the Results from the Stress Test.
     * 
     * @return   void
     * @access   private
     */
    private function _printResult()
    {
        if (!(empty($this->_genDataset))) {
            echo "\n**********************************************************************************\n";
            echo "Recordset of Size " . ($this->_recordSetSize / 1000) . " K is Generated. Records were generated through the step of " . $this->_stepOfDS . " Contacts \n";
            for ($ig=0; $ig<count($this->_genDataset); $ig++) {
                echo "Time taken for step " . ($kg = $ig + 1) . " : " . $this->_genDataset[$ig] . " seconds\n";
            }
            echo "**********************************************************************************\n";
        }

        if (!(empty($this->_insertContact))) {
            echo "\n**********************************************************************************\n";
            echo $this->_insertRecord . " Contact(s) Inserted into the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsert . " Contacts \n";
            
            for ($ii=0; $ii<count($this->_insertContact); $ii++) {
                echo "Time taken for step " . ($ki = $ii + 1) . " : " . $this->_insertContact[$ii] . " seconds\n";
            }
            echo "**********************************************************************************\n";
        }

        if (!(empty($this->_insertRelTime))) {
            echo "\n**********************************************************************************\n";
            echo "Relationships entered for Contact No. " . $this->_startRel . " To Contact No. " . ($this->_startRel + $this->_insertRel) . " From the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsertRel . " Contacts \n";
            
            for ($ir=0; $ir<count($this->_insertRelTime); $ir++) {
                echo "Time taken for step " . ($kr = $ir + 1) . " : " . $this->_insertRelTime[$ir] . " seconds\n";
            }
            echo "**********************************************************************************\n";
        }
        
        if (!(empty($this->_updateContact))) {
            echo "\n**********************************************************************************\n";
            echo "Contact No. " . $this->_startRecord . " To Contact No. " . ($this->_startRecord + $this->_updateRecord) . " Updated from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfUpdate . " Contacts \n";
            
            for ($iu=0; $iu<count($this->_updateContact); $iu++) {
                echo "Time taken for step " . ($ku = $iu + 1) . " : " . $this->_updateContact[$iu] . " seconds\n";
            }
            echo "**********************************************************************************\n";
        }
        
        if (!(empty($this->_addToGroupTime))) {
            echo "\n**********************************************************************************\n";
            echo "Contact No. " . $this->_startOfAdd . " To Contact No. " . ($this->_startOfAdd + $this->_addToGroup) . " Added to Groups from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfAddToGroup . " Contacts \n";
            
            for ($iag=0; $iag<count($this->_addToGroupTime); $iag++) {
                echo "Time taken for step " . ($kag = $iag + 1) . " : " . $this->_addToGroupTime[$iag] . " seconds\n";
            }
            echo "**********************************************************************************\n";
        }

        if (!(empty($this->_deleteContactTime))) {
            echo "\n**********************************************************************************\n";
            echo "Contact No. " . $this->_startOfDelete . " To Contact No. " . ($this->_startOfDelete + $this->_deleteContact) . " Deleted from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfDeleteContact . " Contacts \n";
            
            for ($id=0; $id<count($this->_deleteContactTime); $id++) {
                echo "Time taken for step " . ($kd = $id + 1) . " : " . $this->_deleteContactTime[$id] . " seconds\n";
            }
            echo "**********************************************************************************\n";
        }
        
        if (!(empty($this->_partialNameSearchTime))) {
            echo "\n**********************************************************************************\n";
            echo "Partial Name Search Carried out on the dataset of size " . ($this->_recordSetSize / 1000) . " K \n";
            echo "Criteria for the Search : \n";
            echo "------------------------------------------------------\n";
            foreach ($this->_searchCriteriaPN as $criteriaKey => $criteriaVal) {
                echo " {$criteriaKey} : {$criteriaVal} ";
            }
            echo "------------------------------------------------------\n";
            echo "Total '{$this->_searchCountPN}' Contacts found.\n";
            echo "And Time Taken for Search : {$this->_partialNameSearchTime} seconds\n";
            echo "**********************************************************************************\n";
        }
        
        if (!(empty($this->_groupSearchTime))) {
            echo "\n**********************************************************************************\n";
            echo "Group Search Carried out on the dataset of size " . ($this->_recordSetSize / 1000) . " K \n";
            echo "Criteria for the Search : \n";
            echo "------------------------------------------------------\n";
            foreach ($this->_searchCriteriaG as $criteriaKeyG => $criteriaValG) {
                echo " {$criteriaKeyG} : {$criteriaValG} \n";
            }
            echo "------------------------------------------------------\n";
            echo "Total '{$this->_searchCountG}' Contacts found.\n";
            echo "And Time Taken for Search : {$this->_groupSearchTime} seconds\n";
            echo "**********************************************************************************\n";
        }
        
        echo "\n";
    }

    /**
     * Results for the Stress Test.
     *
     * This function is used for hadling Results from the Stress Test.
     * User will be having choice to decide how they want to see Results from the Stress Test.
     * 
     * @return   void
     * @access   public
     */
    function run($doIt , $genDataset , $insertContact, $updateContact, $insertRel, $addToGroup, $deleteContact, $partialNameSearch, $groupSearch)
    {
        $this->_set($doIt, $genDataset, $insertContact, $updateContact, $insertRel, $addToGroup, $deleteContact, $partialNameSearch, $groupSearch);
        if ($this->_doIt) {
            echo "\n**********************************************************************************\n";
            fwrite(STDOUT, "Options for Stress Testing Results\n");
            $results = array ('C' => 'Create Log File of the Results from the Stress Test',
                              'D' => 'Display the Results from the Stress Test on the Terminal'
                              );
            foreach ($results as $val => $desc) {
                fwrite(STDOUT, "\n" . $val . " : " . $desc . "\n");
            }
            echo "\n**********************************************************************************\n";
            
            fwrite(STDOUT, "Enter Your Option : \t");
            do {
                $select = strtoupper(fgetc(STDIN));
            } while (trim($select) == '');
            
            //if ((array_key_exists($select, $results)) || (array_key_exists(strtolower($select), array_change_key_case($results, CASE_LOWER))) ) {
            if (array_key_exists($select, $results)) {
                switch (strtolower($select)) {
                case 'c':
                    $this->_createLog();
                    break;
                case 'd':
                    $this->_printResult();
                    break;
                }
            }
        }        
    }
}
?>