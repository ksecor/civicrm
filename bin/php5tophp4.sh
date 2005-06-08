#!/bin/bash -v

export PHPPATH=/opt/bin


echo Start of code conversion from php5 to php4....
$PHPPATH/php converter.php

$PHPPATH/php converter.php ../modules/civicrm.module > ../../civicrm/modules/civicrm.module

$PHPPATH/php converter.php ../../civicrm/CRM/Utils/Type.php > /tmp/Type.php
mv /tmp/Type.php ../../civicrm/CRM/Utils/Type.php

$PHPPATH/php converter.php ../../civicrm/CRM/Contact/Task.php > /tmp/Task.php
mv /tmp/Task.php ../../civicrm/CRM/Contact/Task.php

$PHPPATH/php converter.php ../../civicrm/CRM/Contact/Form/Location.php > /tmp/Location.php
mv /tmp/Location.php ../../civicrm/CRM/Contact/Form/Location.php

$PHPPATH/php converter.php ../../civicrm/CRM/Import/Parser.php > /tmp/Parser.php
mv /tmp/Parser.php ../../civicrm/CRM/Import/Parser.php

echo End of code conversion from php5 to php4....

src=..
dst=../../civicrm
rsyncOptions="-avC --exclude=svn"
rsync="rsync $rsyncOptions"

for code in css i js l10n packages PEAR templates bin sql mambo; do
  echo $code
  [ -d $src/$code ] && $rsync $src/$code $dst
done

cp ../license.txt ../../civicrm
cp ../affero_gpl.txt ../../civicrm

cd ../..
tar czf civicrm.tgz civicrm

