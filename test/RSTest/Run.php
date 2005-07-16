<?php
require_once 'Common.php';
require_once 'GenDataset.php';
require_once 'InsertContact.php';
require_once 'InsertRel.php';
require_once 'UpdateContact.php';

class test_RSTest_Run
{
    // This constant is used to set size fo dataset. 
    // The value entered is multiple of 1000 or 1k
    // Ex. if the constant value is 10 then dataset size will be (1000 * 10)
    private $_sizeOfDS      = 2;
    private $_stepOfDS      = 500;
    private $_genDataset    = array();
    
    // Following constant is used for setting the no of records to be inserted into the database.
    // Value should be multiple of 10.
    private $_insertRecord  = 50;
    private $_stepOfInsert  = 10;
    private $_insertContact = array();
    
    // Following constant is used for setting the no of records to be updated from the database. 
    private $_updateRecord  = 1000;
    // Following constant is used for setting the starting record from which update should start. 
    private $_startRecord   = 500;
    private $_stepOfUpdate  = 500;
    private $_updateContact = array();

    private $_recordSetSize;

    private $_startTimeG;
    private $_endTimeG;

    private $_startTimeR;
    private $_endTimeR;

    private $_startTimeIC;
    private $_endTimeIC;

    private $_startTimeUC;
    private $_endTimeUC;

    function callCommon()
    {
        $objCommon            = new test_RSTest_Common();
        $this->_recordSetSize = $objCommon->recordsetSize($this->_sizeOfDS);
    }

    function callGenDataset()
    {
        $startID = 0;
        for ($i=0; $i<($this->_recordSetSize / $this->_stepOfDS); $i++) {
            $objGenDataset       =& new test_RSTest_GenDataset($this->_stepOfDS);
            $this->_startTimeG   = microtime(true);
            $objGenDataset->run($startID);
            $this->_endTimeG     = microtime(true);
            $this->_genDataset[$i] = $this->_endTimeG - $this->_startTimeG;
            $startID = $startID + $this->_stepOfDS;
        }
    }
    
    function callInsertContact()
    {
        $startID = 0;
        for ($i=0; $i<($this->_insertRecord / $this->_stepOfInsert); $i++) {
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
    }
    /*
    function callInsertRel()
    {
        $objInsertRel  = new test_RSTest_InsertRel();
        $this->_startTimeR = microtime(true);
        $objInsertRel->run();
        $this->_endTimeR   = microtime(true);
    }
    */
    function callUpdateContact()
    {
        if (($this->_updateRecord + $this->_stepOfUpdate) <= ($this->_sizeOfDS * 1000)) {
            $startID = $this->_startRecord;
            for ($i=0; $i<($this->_updateRecord / $this->_stepOfUpdate); $i++) {
                $objUpdateContact   = new test_RSTest_UpdateContact($this->_stepOfUpdate);
                $this->_startTimeUC = microtime(true);
                $objUpdateContact->run($startID, $this->_stepOfUpdate);
                $this->_endTimeUC   = microtime(true);
                $this->_updateContact[$i] = $this->_endTimeUC - $this->_startTimeUC;
                $startID = $startID + $this->_stepOfUpdate;
            }
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of records asked to Update..!!! \n";
            echo "**********************************************************************************\n";
        }
    }

    function printResult()
    {
        echo "\n**********************************************************************************\n";
        echo "Recordset of Size " . ($this->_recordSetSize / 1000) . " K is Generated. Records were generated through the step of " . $this->_stepOfDS . " contacts \n";
        for ($ig=0; $ig<count($this->_genDataset); $ig++) {
            echo "Time taken for step " . ($kg = $ig + 1) . " : " . $this->_genDataset[$ig] . " seconds\n";
        }
        echo "**********************************************************************************\n";
                
        echo "\n**********************************************************************************\n";
        echo $this->_insertRecord . " Contact(s) Inserted into the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsert . " contacts \n";
        
        for ($ii=0; $ii<count($this->_insertContact); $ii++) {
            echo "Time taken for step " . ($ki = $ii + 1) . " : " . $this->_insertContact[$ii] . " seconds\n";
        }
        echo "**********************************************************************************\n";
        /*
        echo "\n**********************************************************************************\n";
        echo "Time taken for inserting relationships : " . ($this->_endTimeR - $this->_startTimeR) . " seconds. \n";
        echo "**********************************************************************************\n";
        */
        echo "\n**********************************************************************************\n";
        echo "Contact No. " . $this->_startRecord . "To Contact No. " . $this->_updateRecord . " Updated from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfUpdate . " contacts \n";
        
        for ($iu=0; $iu<count($this->_updateContact); $iu++) {
            echo "Time taken for step " . ($ku = $iu + 1) . " : " . $this->_updateContact[$iu] . " seconds\n";
        }
        echo "**********************************************************************************\n";
        
        echo "\n";
    }
}

$objRun =& new test_RSTest_Run();
echo "************************************************************************ \n";
echo "Stress Test Started \n";
echo "************************************************************************ \n";
$objRun->callCommon();
$objRun->callGenDataset();
$objRun->callInsertContact();
//$objRun->callInsertRel();
$objRun->callUpdateContact();
$objRun->printResult();

?>