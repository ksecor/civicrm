#!/bin/bash

for fileLink in css i js l10n packages PEAR templates
  do
  if test -L $fileLink   # check if link exists
      then
      # skip if link exists since it creates another symlink in the source directory
      # which breaks the drupal admin calls (goes into infinite loop trying to follow circular links)
      echo link $fileLink already exists. skipping it ...
  else
      echo creating link $fileLink ...
      ln -s ~/svn/crm/$fileLink $fileLink
  fi
done
