#!/bin/bash

for fileLink in css i js l10n packages PEAR templates
do
  echo creating link $fileLink ...
  ln -s ~/svn/crm/$fileLink $fileLink
done

#     ln -s ../../crm/css css	
#     ln -s ../../crm/i i
#     ln -s ../../crm/js js
#     ln -s ../../crm/l10n l10n
#     ln -s ../../crm/packages packages
#     ln -s ../../crm/PEAR PEAR
#     ln -s ../../crm/templates templates
