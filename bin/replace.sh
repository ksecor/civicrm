#!/bin/sh
for j in extern CRM api test; do
  cd ../$j;
  for i in `find . -name \*.php`; do
    echo $i;
    perl -pi -e 's/CiviCRM version 1.8/CiviCRM version 1.9/' $i;
  done
done


