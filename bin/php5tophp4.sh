#!/bin/bash 

echo Start of code conversion from php5 to php4....

php converter.php

php converter.php ../modules/civicrm.module > ../php4/modules/civicrm.module.php4

php converter.php ../php4/CRM/Utils/Type.php > /tmp/Type.php

mv /tmp/Type.php ../php4/CRM/Utils/Type.php

echo End of code conversion from php5 to php4....

cd ../php4

./createlinks.sh
