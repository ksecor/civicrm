#!/bin/bash -v

cd ../sql
mysql -u civicrm -pMt!Everest civicrm < Contacts.sql
mysql -u civicrm -pMt!Everest civicrm < GeneratedData.sql

