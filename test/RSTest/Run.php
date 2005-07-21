<?php
require_once 'Common.php';
require_once 'GenDataset.php';
require_once 'InsertContact.php';
require_once 'InsertRel.php';
require_once 'UpdateContact.php';
require_once 'AddContactToGroup.php';
require_once 'DelContact.php';

class test_RSTest_Run
{
    private $_recordSetSize;
    private $_flag;

    // This constant is used to set size fo dataset. 
    // The value entered is multiple of 1000 or 1k
    // Ex. if the constant value is 10 then dataset size will be (1000 * 10)
    private $_sizeOfDS      = 2;
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
    private $_insertRel        = 1000;
    // Following constant is used for setting the starting contact from which the relationships needs to be entered.
    private $_startRel         = 1000;
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
    private $_deleteContact       = 1000;
    // Following constant is used for setting the starting contact from which Contacts needs to be added to a Group.
    private $_startOfDelete       = 500;
    // Following constant is used for setting the step for adding Contact to a Group.
    private $_stepOfDeleteContact = 500;
    // Following array is used to store the timings for all the steps of adding Contact to a Group. 
    private $_deleteContactTime   = array();
    
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
    
    function callCommon()
    {
        $objCommon            = new test_RSTest_Common();
        $this->_recordSetSize = $objCommon->recordsetSize($this->_sizeOfDS);
    }

    function callGenDataset()
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
    
    function callInsertContact()
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
            $objInsertContact  = new test_RSTest_InsertContact($this->_stepOfInsert);
            $this->_startTimeIC = microtime(true);
            $objInsertContact->run($this->_recordSetSize, $startID);
            $this->_endTimeIC   = microtime(true);
            $this->_insertContact[$i] = $this->_endTimeIC - $this->_startTimeIC;
            $startID = $startID + $this->_stepOfInsert;
        }
        echo "\n Contacts Successfully Inserted.\n";
    }
    
    function callInsertRel()
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
                $objInsertRel  = new test_RSTest_InsertRel();
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
    
    function callUpdateContact()
    {
        if (($this->_startRecord + $this->_updateRecord) <= ($this->_sizeOfDS * 1000)) {
            echo "\n Updating Contacts . \n";
            $startID = $this->_startRecord;
            for ($i=0; $i<($this->_updateRecord / $this->_stepOfUpdate); $i++) {
                $objUpdateContact   = new test_RSTest_UpdateContact();
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
    
    function callAddContactToGroup()
    {
        if (($this->_startOfAdd + $this->_addToGroup) <= ($this->_sizeOfDS * 1000)) {
            echo "\n Adding Contacts to Group. \n";
            $startID = $this->_startOfAdd;
            for ($i=0; $i<($this->_addToGroup / $this->_stepOfAddToGroup); $i++) {
                $objAddContactToGroup   = new test_RSTest_AddContactToGroup();
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
    
    function callDeleteContact()
    {
        if (($this->_startOfDelete + $this->_deleteContact) <= ($this->_sizeOfDS * 1000)) {
            echo "\n Deleting Contacts . \n";
            $startID = $this->_startOfDelete;
            for ($i=0; $i<($this->_deleteContact / $this->_stepOfDeleteContact); $i++) {
                $objDeleteContact   = new test_RSTest_DelContact();
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

    function run()
    {
        $this->_flag = 0;
        echo "\n**********************************************************************************\n";
        fwrite(STDOUT, "Options for Stress Testing \n");
        $options = array ('A' => 'All Operations will be done for Stress Test i.e. Inserting Contact, Updating Contact, Insert Relationship, Adding Contact to Group and Deleting Contacts',
                          'I' => 'Inserting Contacts',
                          'U' => 'Updating Contacts',
                          'R' => 'Inserting Relationship',
                          'G' => 'Adding Contacts to Group',
                          'D' => 'Delete Contacts'
                          );
        foreach ($options as $val => $desc) {
            fwrite(STDOUT, "\n" . $val . " : " . $desc . "\n");
        }
        echo "\n**********************************************************************************\n";
        
        do {
            fwrite(STDOUT, "Enter Your Option : \t");

            $selection = fgetc(STDIN);

        } while (trim ($selection == ''));

        if ((array_key_exists($selection, $options)) || (array_key_exists(strtolower($selection), array_change_key_case($options, CASE_LOWER))) ) {
            $this->_flag = 1;
            echo "\nStress Test Started \n";
            $this->callCommon();
            $this->callGenDataset();
            switch (strtolower($selection)) {
            case 'a':
                $this->callInsertContact();
                $this->callUpdateContact();
                $this->callInsertRel();
                $this->callAddContactToGroup();
                $this->callDeleteContact();
                break;
            case 'i':
                $this->callInsertContact();
                break;
            case 'u':
                $this->callUpdateContact();
                break;
            case 'r':
                $this->callInsertRel();
                break;
            case 'g':
                $this->callAddContactToGroup();
                break;
            case 'd':
                $this->callDeleteContact();
                break;
            }
        } else {
            echo "\n**********************************************************************************\n";
            echo "Not a Valid Choice \n";
            echo "**********************************************************************************\n";
        }
    }
    /*
    function result()
    {
        if ($this->_flag) {
            echo "\n**********************************************************************************\n";
            fwrite(STDOUT, "Options for Stress Testing Results\n");
            $results = array ('C' => 'Create Log File of the Results from the Stress Test',
                              'D' => 'Display the Results from the Stress Test on the Terminal'
                              );
            foreach ($results as $val => $desc) {
                fwrite(STDOUT, "\n" . $val . " : " . $desc . "\n");
            }
            echo "\n**********************************************************************************\n";
            
            do {
                fwrite(STDOUT, "Enter Your Option : \t");
                $select = fgets(STDIN);
                echo " You have selected $select \n";
            } while (trim ($select == ''));
            
            if ((array_key_exists($select, $results)) || (array_key_exists(strtolower($select), array_change_key_case($results, CASE_LOWER))) ) {
                switch (strtolower($select)) {
                case 'c':
                    $this->_createLog();
                    break;
                case 'd':
                    $this->_printResult();
                    break;
                }
            }
        } else {
            echo "flag not set";
        }
        
        
    }
    */
    function _createLog()
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
        
        fwrite($file_pointer, $string);
                
        fclose($file_pointer);
        echo "\n**********************************************************************************\n";
        echo " Results Successfully written to the file : " . getcwd() . "/" .$file_name. "\n";
        echo "**********************************************************************************\n";
    }

    function _printResult()
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
        
        echo "\n";
    }
}

$objRun =& new test_RSTest_Run();

$objRun->run();
//$objRun->result();
$objRun->_createLog();
$objRun->_printResult();
?>