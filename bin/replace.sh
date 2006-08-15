#!/bin/sh
cd ../CRM
for i in `find . -name \*.php`; do
  echo $i;
  perl -pi -e 's|version 1.4|version 1.5|g' $i
done
