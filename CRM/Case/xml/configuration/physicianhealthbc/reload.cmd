@echo off
if "%1"=="" GOTO bad
if "%2"=="" GOTO bad
if "%3"=="" GOTO bad
if "%4"=="" GOTO bad

mysql -u %2 --password=%3 < reload.mysql
wget -O - %1
php physicianhealthbc.php %2 %3 %4
goto end

:bad
echo Usage: reload [url of civicrm homepage] [username] [password] [civicrm database name]

:end