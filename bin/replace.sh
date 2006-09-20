#!/bin/sh
for j in js; do
  cd ../$j;
  for i in `find . -name \*.js`; do
    echo $i;
    perl -pi -e 's|lobo\@yahoo.com|lobo\@civicrm\.org|g' $i;
  done
done
