#!/bin/sh
for j in extern; do
  cd ../$j;
  for i in `find . -name \*.php`; do
    echo $i;
    perl -pi -e 's/CiviCRM version 1.7/CiviCRM version 1.8/' $i;
  done
done


