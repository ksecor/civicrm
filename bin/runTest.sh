#!/bin/sh
# Script for Running all the Tests one after other

# Where are we called from?
P=`dirname $0`

# Current dir
ORIGPWD=`pwd`

# File for Storing Log Of UnitTest.
logUT=UnitTestResult

# File for Storing Log Of Successes from maxQ Tests.
logSMT=maxqSuccess

# File for Storing Log Of Errors from maxQ Tests.
logEMT=maxqError

# Function to Create Log Folder if it does not Exists.
create_log()
{
    cd $ORIGPWD/../
    
    PATH4LOG=`pwd` 
    
    if [ ! -d "LOG" ] ; then 
	mkdir LOG
    fi
}

# Function to Run Unit Tests.
run_UnitTest()
{
    cd $ORIGPWD/../test
    # Running Unit Tests
    php UnitTests.php > $ORIGPWD/../LOG/$logUT
}

# Function to Run maxQ Tests.
run_maxQScript()
{
    cd $ORIGPWD/
    # running script for maxq generated scripts
    ./test_sandbox.sh 2>$ORIGPWD/../LOG/$logEMT 1>$ORIGPWD/../LOG/$logSMT
}

# Function to Run Stress Test.
run_stressTest()
{
    cd $ORIGPWD/
    # running stress test
    ./runStressTest.sh
}

# Function used for Displaying Menu.
display_menu()
{
    clear
    echo
    echo " *********************** Testing with Different Tests *********************** "
    echo 
    echo "Options available: "
    echo "  U   - Carry out Unit Tests"
    echo "  M   - Carry out Testing with maxq scripts"
    echo "  S   - Carry out Stress Tests"
    echo "  All - Carry out all the above mentioned Tests i.e. Unit Tests, maxQ Tests, Stress Test"
    echo
    echo
}

# Main Execution Starts Here.

create_log

display_menu
echo "Enter Your Option: "
read option
echo;

# Following Case Structure is used for Executing Menuing System.
case $option in
    # Unit Tests
    "U" | "u" )
	echo "Running Unit Tests"; echo;
	run_UnitTest
	echo "Unit Tests Successfully Completed. Log stored in the File : " $PATH4LOG/LOG/$logUT; echo;
	;;
    
    # maxQ Tests
    "M" | "m" )
	echo "Running maxQ generated Scripts"; echo;
	echo "(This will take few Minutes)"; echo;
	run_maxQScript
	echo "maxQ Tests Successfully Completed. Log stored in the File : " $PATH4LOG/LOG/$logEMT " and " $PATH4LOG/LOG/$logSMT; echo;
	;;
    
    # Stress Tests
    "S" | "s" )
	echo "Running Stress Tests"; echo;
	run_stressTest
	echo "Stress Tests Successfully Completed."; echo;
	;;
    
    # All the Tests will be Executed one after other 
    "All" | "all" )
	echo "Running all three Tests i.e. Unit Tests, maxQ Tests and Stress Test "; echo;
	echo "Running Unit Tests"; echo;
	run_UnitTest
	echo "Unit Tests Successfully Completed. Log stored in the File : " $PATH4LOG/$logUT; echo;
	echo "Running maxQ generated Scripts"; echo;
	echo "(This will take few Minutes)"; echo;
	run_maxQScript
	echo "maxQ Tests Successfully Completed. Log stored in the File : " $PATH4LOG/$logEMT " and " $PATH4LOG/$logSMT; echo;
	echo "Running Stress Tests"; echo;
	run_stressTest
	echo "Stress Tests Successfully Completed."; echo;
	;;
esac
