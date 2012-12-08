#! /bin/bash

testfile=$(ls -l | grep "Test.php" | awk '{print $8}')

for i in $testfile
do
	echo "cmd: phpunit $i"
	phpunit $i
	echo "======================================================="
done
