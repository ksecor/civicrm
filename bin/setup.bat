cd ..\xml
php GenCode.php

cd ..\sql
mysql -u civicrm -p -e "\. Contacts.sql"
mysql -u civicrm -p -e "\. FixedData.sql"

php GenerateContactData.php

