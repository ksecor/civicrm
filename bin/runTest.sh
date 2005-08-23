# Script for Running all the Test at a Time

cd ../test/
# Running Unit Tests
php UnitTests.php > ../bin/UnitTestResult

cd ../bin/
# running script for maxq generated scripts
./test_sandbox.sh 2>maxqError 1>maxqSuccess
# running stress test
./runStressTest.sh
