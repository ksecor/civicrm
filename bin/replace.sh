#!/bin/sh
for j in js templates; do
  cd ../$j;
  for i in `find . -name \*.js`; do
    echo $i;
    perl -pi -e 's/CiviCRM version 1.6/CiviCRM version 1.7/' $i;
    perl -pi -e 's/-2006/-2007/' $i;   
  done
done


