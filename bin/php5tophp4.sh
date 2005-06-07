#!/bin/bash -v

echo Start of code conversion from php5 to php4....

php converter.php

php converter.php ../modules/civicrm.module > ../../crm.php4/modules/civicrm.module.php4

php converter.php ../../crm.php4/CRM/Utils/Type.php > /tmp/Type.php

mv /tmp/Type.php ../../crm.php4/CRM/Utils/Type.php

php converter.php ../../crm.php4/CRM/Contact/Task.php > /tmp/Task.php

mv /tmp/Task.php ../../crm.php4/CRM/Contact/Task.php

php converter.php ../../crm.php4/CRM/Contact/Form/Location.php > /tmp/Location.php

mv /tmp/Location.php ../../crm.php4/CRM/Contact/Form/Location.php

php converter.php ../../crm.php4/CRM/Import/Parser.php > /tmp/Parser.php

mv /tmp/Parser.php ../../crm.php4/CRM/Import/Parser.php

echo End of code conversion from php5 to php4....

./php4links.sh
