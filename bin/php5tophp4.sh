#!/bin/bash -v

echo Start of code conversion from php5 to php4....
php converter.php

php converter.php ../modules/civicrm.module > ../../civicrm/modules/civicrm.module

php converter.php ../../civicrm/CRM/Utils/Type.php > /tmp/Type.php
mv /tmp/Type.php ../../civicrm/CRM/Utils/Type.php

php converter.php ../../civicrm/CRM/Contact/Task.php > /tmp/Task.php
mv /tmp/Task.php ../../civicrm/CRM/Contact/Task.php

php converter.php ../../civicrm/CRM/Contact/Form/Location.php > /tmp/Location.php
mv /tmp/Location.php ../../civicrm/CRM/Contact/Form/Location.php

php converter.php ../../civicrm/CRM/Import/Parser.php > /tmp/Parser.php
mv /tmp/Parser.php ../../civicrm/CRM/Import/Parser.php

echo End of code conversion from php5 to php4....

src=..
dst=../../civicrm
rsyncOptions="-avC --exclude=svn"
rsync="rsync $rsyncOptions"

for code in css i js l10n packages PEAR templates bin; do
  echo $code
  [ -d $src/$code ] && $rsync $src/$code $dst
done

cd ../..
tar czf civicrm.tgz civicrm

