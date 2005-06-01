#!/bin/bash -v

cd ../xml
php GenCode.php

cd ../sql
mysql -u civicrm -pMt!Everest civicrm < Contacts.sql
mysql -u civicrm -pMt!Everest civicrm < GeneratedData.sql

# to generate a new data file do the foll:
# mysql -u civicrm -pMt\!Everest civicrm < Contacts.sql
# mysql -u civicrm -pMt\!Everest civicrm < FixedData.sql
# php GenerateContactData.php
# mysqldump -t -u civicrm -pMt\!Everest civicrm  > GeneratedData.sql

