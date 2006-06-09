#!/bin/sh
cd ../CRM
for i in `find . -name \*.php`; do
  echo $i;
  perl -pi -e 's|CRM/DAO/|CRM/Core/DAO/|g' $i
  perl -pi -e 's|CRM_DAO|CRM_Core_DAO|g' $i
  perl -pi -e 's|CRM/BAO/|CRM/Core/BAO/|g' $i
  perl -pi -e 's|CRM_BAO|CRM_Core_BAO|g' $i
done
