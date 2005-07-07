<?php
require_once 'Common.php';
require_once 'GenDataset.php';
require_once 'InsertContact.php';


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
    private $_noOfRecord    = 100;
    private $_stepOfInsert  = 10;
    private $_insertContact = array();
    
    private $_recordSetSize;

    private $_startTimeG;
    private $_endTimeG;

    private $_startTimeI;
    private $_endTimeI;

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
        for ($i=0; $i<($this->_noOfRecord / $this->_stepOfInsert); $i++) {
            $objInsertContact  = new test_RSTest_InsertContact($this->_stepOfInsert);
            $this->_startTimeI = microtime(true);
            $objInsertContact->run($this->_recordSetSize, $startID);
            $this->_endTimeI   = microtime(true);
            $this->_insertContact[$i] = $this->_endTimeI - $this->_startTimeI;
            $startID = $startID + $this->_stepOfInsert;
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
        echo $this->_noOfRecord . " Contact(s) Inserted into the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsert . " contacts \n";
        
        for ($ii=0; $ii<count($this->_insertContact); $ii++) {
            echo "Time taken for step " . ($ki = $ii + 1) . " : " . $this->_insertContact[$ii] . " seconds\n";
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
$objRun->printResult();

?>