#!/bin/bash -v

cd ../xml
php GenCode.php

cd ../sql
mysql -u crm -pMt!Everest < Contacts.sql
mysql -u crm -pMt!Everest crm < FixedData.sql

php GenerateContactData.php

