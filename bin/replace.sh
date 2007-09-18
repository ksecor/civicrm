#!/bin/sh
for j in extern CRM api test js; do
  cd ../$j;
  for i in `find . -name \*.js`; do
    echo $i;
    perl -pi -e 's/CiviCRM version 1.9/CiviCRM version 2.0/' $i;
  done
done


