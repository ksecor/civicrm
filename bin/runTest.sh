#!/bin/sh
# Script for Running all the Tests one after other

# Where are we called from?
P=`dirname $0`

# Current dir
ORIGPWD=`pwd`

# File for Storing Log Of UnitTest.
logUT=UnitTestResult
logSRT=SeleniumTestResult

###########################################################
##
##  Create log for the tests
##
###########################################################

# Method to Create Log Folder if it does not Exists.
create_log()
{
    cd $ORIGPWD/../test/
    
    PATH4LOG=`pwd` 
    
    if [ ! -d "Result" ] ; then 
	mkdir Result
    fi
}

###########################################################
##
##  Following methods are used to run the different tests
##
###########################################################

# Method to Run Unit Tests.
run_UnitTest()
{
    cd $ORIGPWD/../test
    # Running Unit Tests
    php UnitTests.php > $PATH4LOG/Result/$logUT
}

# Method to Run Selenium Ruby Tests.
run_seleniumTest()
{
    sub_menu
    
    echo "Enter Your Option: "
    read option

    cd $ORIGPWD/../test/selenium-ruby/CRM
    # Running Selenium (ruby) Tests
    ruby ruby_unit_tests.rb $option
}

# Method to Run Stress Test.
run_stressTest()
{
    cd $ORIGPWD/
    # running stress test
    ./runStressTest.sh
}

###########################################################
##
##  Menu system for different purpos
##
###########################################################

main_menu()
{
    clear
    echo
    echo " *********************** Select Method for Test *********************** "
    echo 
    echo "Options available: "
    echo "  UT   - Carry out Unit Tests"
    echo "  ST   - Carry out Stress Tests"
    echo "  SRT  - Carry out Selenium (Ruby) Tests"
    echo "  All  - Carry out all the above mentioned Tests i.e. Unit Tests, Stress Test, Selenium Test"
    echo
    echo
}

sub_menu()
{
    clear
    
    echo
    echo " *********************** Select the Option *********************** "
    echo 
    echo "Options available: "
    echo "  CON-I   : Contact Individual"
    echo "  CON-H   : Contact Household"
    echo "  CON-O   : Contact Organization"
    echo "  ADM-C   : Administer - Configuration Section"
    echo "  ADM-S   : Administer - Setup Section"
    echo "  ADM-CC  : Administer - CiviContribute"
    echo "  ADM-CM  : Administer - CiviMember"
    echo "  SRC-BS  : Find Contact - Basic Search"
    echo "  SRC-AS  : Advanced Search"
    echo "  SRC-SB  : Search Builder"
    #echo "  IMP-C   : Import - Contacts"
    #echo "  IMP-AH  : Import - Activity History"
    echo "  CCT-FC  : CiviContribute - Find Contribution"
    #echo "  CCT-IC  : CiviContribute - Import Contribution"
    echo "  CMB-FM  : CiviMember - Find Memberships"
    #echo "  CMB-IM  : CiviMember - Import Memberships"
    echo "  CML     : CiviMail"
}

###########################################################
##
##  Main execution method.
##
##  All test scripts will run usnig this method
##
###########################################################

run_option()
{
    # Following Case Structure is used for Executing Menuing System.
    case $1 in
    # Unit Tests
	"UT" | "ut" | "Ut")
	    echo "Running Unit Tests"; echo;
	    run_UnitTest
	    echo "Unit Tests Successfully Completed. Log stored in the File : " $PATH4LOG/Result/$logUT; echo;
	    echo " **************************************************************************** ";
	    ;;
	
    # Stress Tests
	"ST" | "st" | "St")
	    echo "Running Stress Tests"; echo;
	    run_stressTest
	    echo "Stress Tests Successfully Completed."; echo;
	    echo " **************************************************************************** ";
	    ;;
	
    # Stress Tests
	"SRT" | "srt" | "Srt")
	    echo "Running Selenium (Ruby) Tests"; echo;
	    run_seleniumTest
	    #echo "Selenium (Ruby) Testing Successfully Completed. Log stored in the File : " $PATH4LOG/Result/$logSRT; echo;
	    echo " **************************************************************************** ";
	    ;;
        
    # All the Tests will be Executed one after other 
	"All" | "all" )
	    echo "Running all three Tests i.e. Unit Tests, Web Tests, maxQ Tests and Stress Test "; echo;
	    echo "Running Unit Tests"; echo;
	    run_UnitTest
	    echo "Unit Tests Successfully Completed. Log stored in the File : " $PATH4LOG/Result/$logUT; echo;
	    echo "Running Stress Tests"; echo;
	    run_stressTest
	    echo "Stress Tests Successfully Completed."; echo;
	    echo " **************************************************************************** ";
	    echo "Running Selenium (ruby) Tests"; echo;
	    run_seleniumTest
	    #echo "Selenium (Ruby) Testing Successfully Completed. Log stored in the File : " $PATH4LOG/Result/$logSRT; echo;
	    echo " **************************************************************************** ";
	    ;;
	*)
	    echo "You have ntered Invalid Option."; echo;
	    exit
	    ;;
    esac
}

###########################################################
##
##  Start of the script.
##
###########################################################

start()
{
create_log

main_menu
echo "Enter Your Option: "
read option
run_option $option
echo;

}

###########################################################
##
##  Call to start of the script
##
###########################################################

start