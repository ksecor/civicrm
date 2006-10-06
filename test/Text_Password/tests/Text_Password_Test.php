<?php
// +------------------------------------------------------------------------+
// | PEAR :: Text_Password                                                  |
// +------------------------------------------------------------------------+
// | Copyright (c) 2004 Martin Jansen                                       |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
//
// $Id: Text_Password_Test.php,v 1.2 2004/10/31 22:40:50 mj Exp $
//

require_once "PHPUnit.php";
require_once "PHPUnit/TestCase.php";
require_once "PHPUnit/TestSuite.php";
require_once "Text/Password.php";

/**
 * Unit test suite for the Text_Password package
 *
 * @author  Martin Jansen <mj@php.net>
 * @extends PHPUnit_TestCase
 * @version $Id: Text_Password_Test.php,v 1.2 2004/10/31 22:40:50 mj Exp $
 */
class Text_Password_Test extends PHPUnit_TestCase {

    function testCreate()
    {
        $password = Text_Password::create();
        $this->assertTrue(strlen($password) == 10);
    }

    function testCreateWithLength()
    {
        $password = Text_Password::create(15);
        $this->assertTrue(strlen($password) == 15);
    }
    
    function testCreateMultiple()
    {
        $passwords = Text_Password::createMultiple(3);
        $this->_testCreateMultiple($passwords, 3, 10);
    }

    function testCreateMultipleWithLength()
    {
        $passwords = Text_Password::createMultiple(3, 15);
        $this->_testCreateMultiple($passwords, 3, 15);        
    }

    function testCreateNumericWithLength()
    {
        $password = Text_Password::create(8, 'unpronounceable', 'numeric');

        $this->assertRegExp("/^[0-9]{8}$/", $password);
    }

    function testCreateFromABCWithLength()
    {
        $password = Text_Password::create(8, 'unpronounceable', 'a,b,c');
        $this->assertRegExp("/^[abc]{8}$/i", $password);
    }

    function testCreateAlphabeticWithLength()
    {
        $password = Text_Password::create(8, 'unpronounceable', 'alphabetic');

        $this->assertRegExp("/^[a-z]{8}$/i", $password);
    }

    function testTimeToBruteForce()
    {
        $password = Text_Password::create(5, 'unpronounceable');
        $result = $this->_TimeToBruteForce($pass);

        $this->assertTrue($result['combination'] == 1);
        $this->assertTrue($result['max'] == 0.00025);
        $this->assertTrue($result['min'] == 0.000125);
    }

    // {{{ Test cases for creating passwords based on a given login string

    function testCreateFromLoginReverse()
    {
        $this->assertEquals("eoj", Text_Password::createFromLogin("joe", "reverse"));
    }

    function testCreateFromLoginShuffle()
    {
        $this->assertTrue(strlen(Text_Password::createFromLogin("hello world", "shuffle")) == strlen("hello world"));
    }

    function testCreateFromLoginRotX()
    {
        $this->assertEquals("tyo", Text_Password::createFromLogin("joe", "rotx", 10));
    }
    
    function testCreateFromLoginRot13()
    {
        $this->assertEquals("wbr", Text_Password::createFromLogin("joe", "rot13"));
    }

    function testCreateFromLoginRotXplusplus()
    {
        $this->assertEquals("syp", Text_Password::createFromLogin("joe", "rotx++", 9));
    }

    function testCreateFromLoginRotXminusminus()
    {
        $this->assertEquals("swl", Text_Password::createFromLogin("joe", "rotx--", 9));
    }

    function testCreateFromLoginXOR()
    {
        $this->assertEquals("oj`", Text_Password::createFromLogin("joe", "xor", 5));
    }

    function testCreateFromLoginASCIIRotX()
    {
        $this->assertEquals("otj", Text_Password::createFromLogin("joe", "ascii_rotx", 5));
    }

    function testCreateFromLoginASCIIRotXplusplus()
    {
        $this->assertEquals("oul", Text_Password::createFromLogin("joe", "ascii_rotx++", 5));
    }

    function testCreateFromLoginASCIIRotXminusminus()
    {
        $this->assertEquals("uyn", Text_Password::createFromLogin("joe", "ascii_rotx--", 11));
    }

    /**
     * Unit test for bug #2605
     *
     * Actually this method does not implement a real unit test, but 
     * instead it is there to make sure that no warning is produced
     * by PHP.
     *
     * @link http://pear.php.net/bugs/bug.php?id=2605
     */
    function testBugReport2605()
    {
        $password = Text_Password::create(7, 'unpronounceable', '1,3,a,Q,~,[,f');
        $this->assertTrue(strlen($password) == 7);
    }

    // }}}
    // {{{ private helper methods

    function _testCreateMultiple($passwords, $count, $length)
    {
        $this->assertType("array", $passwords);
        $this->assertTrue(count($passwords) == $count);

        foreach ($passwords as $password) {
            $this->assertTrue(strlen($password) == $length);
        }        
    }

    function _timeToBruteForce($password, $nbr = 0, $cmbPerSeconde = 4000)
    {
        global $_Text_Password_NumberOfPossibleCharacters;

        $nbr = ($nbr == 0) ? $_Text_Password_NumberOfPossibleCharacters : $nbr;
        $cmb = pow($nbr, strlen($password));
        $time_max = $cmb / $cmbPerSeconde;
        $time_min = ($cmb / $cmbPerSeconde) / 2;

        return array("combination" => $cmb,
                     "max"         => $time_max,
                     "min"         => $time_min);
    }

    // }}}
}

$suite = new PHPUnit_TestSuite('Text_Password_Test');
$result = PHPUnit::run($suite);
echo $result->toString();
