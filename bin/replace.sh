#!/bin/sh
for j in bin xml js extern; do
  cd ../$j;
  for i in `find . -name \*.php`; do
    echo $i;
    perl -pi -e 's|Copyright \(c\) 2005 Social Source Foundation|copyright CiviCRM LLC (c) 2004-2006|g' $i;
    perl -pi -e 's|Copyright \(c\) 2005 Donald A. Lobo|copyright CiviCRM LLC (c) 2004-2006|g' $i;
    perl -pi -e 's|copyright Donald A. Lobo 01/15/2005|copyright CiviCRM LLC (c) 2004-2006|g' $i;
  done
done
