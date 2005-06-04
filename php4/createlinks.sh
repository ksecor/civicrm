#!/bin/bash

for fileLink in css i js l10n packages PEAR templates
do
  echo creating link $fileLink ...
  ln -s ~/svn/crm/$fileLink $fileLink
done

cd modules
ln -s civicrm.module.hide civicrm.module
