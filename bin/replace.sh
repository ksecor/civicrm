#!/bin/sh
for i in `find . -name '*.js' -or -name '*.module' -or -name '*.php' -or -name '*.tpl' -or -name '*.txt'`; do
  echo $i;
  perl -pi -e 's/CiviCRM version .\../CiviCRM version 2.1/' $i;
done
