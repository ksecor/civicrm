#!/bin/bash -v

cd ../xml
php GenCode.php

cd ../sql
mysql -u civicrm -pMt!Everest civicrm < Contacts.sql
mysql -u civicrm -pMt!Everest civicrm < FixedData.sql

php GenerateContactData.php

