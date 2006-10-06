<?php
error_reporting(E_ALL|E_STRICT);
require_once 'Selenium.php';
try {
    $selenium = new Selenium("*firefox", "http://pear.php.net/");
    $result = $selenium->start();
    $selenium->open("http://pear.php.net/packages.php");
    if ($selenium->getTitle() == "PEAR :: Package Browser :: Top Level") {
        print "equal\n";
    } else {
        print "not equal\n";
    }

    $selenium->type("q", "PEAR");
    $selenium->submit("//form");
    $selenium->waitForPageToLoad();
    if ($selenium->getTitle() == "PEAR :: Search: PEAR") {
        print "equal\n";
    } else {
        print "not equal\n";
    }
    $selenium->stop();
} catch (Selenium_Exception $e) {
    echo $e;
}

/* With simpletest */
/*
// To see this example, you need to have Simpletest library.
// You can't run simpletest with E_STRICT
require_once 'Selenium.php';
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';

class Example extends UnitTestCase
{
    function setUp()
    {
        $this->selenium = new Selenium("*firefox", "http://pear.php.net/");
        $result = $this->selenium->start();
    }
    function tearDown()
    {
        $this->selenium->stop();
    }

    function testPEARSearch()
    {
        $this->selenium->open("http://pear.php.net/packages.php");
        $this->assertEqual("PEAR :: Package Browser :: Top Level", $this->selenium->getTitle());
        $this->selenium->type("q", "PEAR");
        $this->selenium->submit("//form");
        $this->selenium->waitForPageToLoad(1000);
        $this->assertEqual("PEAR :: Search: PEAR", $this->selenium->getTitle());
    }
}
$test = new Example();
$test->run(new TextReporter());
 */
/* With PHP_Unit2
// To see this example, you need to have PHPUnit library.
error_reporting(E_ALL|E_STRICT);
require_once 'Selenium.php';
require_once 'PHPUnit2/Framework/TestCase.php';

class Example extends PHPUnit2_Framework_TestCase
{
    function __construct($name)
    {
        parent::__construct($name);
    }
    function setUp()
    {
        $this->selenium = new Selenium("*firefox", "http://pear.php.net/");
            // XXX pear does not work E_STRICT because of HTTP_Request
            // the options are "curl", "pear", "native"
            // $this->selenium->setDriver("pear");
        $result = $this->selenium->start();
    }
    function tearDown()
    {
        $this->selenium->stop();
    }

    function testPEARSearch()
    {
        $this->selenium->open("http://pear.php.net/packages.php");
        $this->assertEquals("PEAR :: Package Browser :: Top Level", $this->selenium->getTitle());
        $this->selenium->type("q", "PEAR");
        $this->selenium->submit("//form");
        $this->selenium->waitForPageToLoad(1000);
        $this->assertEquals("PEAR :: Search: PEAR", $this->selenium->getTitle());
    }
}
 */
?>

