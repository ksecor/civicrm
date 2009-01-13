@echo off
if "%1"=="" GOTO bad
if "%2"=="" GOTO bad
if "%3"=="" GOTO bad
if "%4"=="" GOTO bad

echo drop database %4; create database %4; use %4; source ../../../../sql/civicrm.mysql; source ../../../../sql/civicrm_data.mysql; source physicianhealthbc.mysql; | mysql -u %2 --password=%3
wget -O - %1
php physicianhealthbc.php %2 %3 %4
goto end

:bad
echo.
echo Usage: reload [url of civicrm homepage] [database username with drop/create privilege] [database password] [civicrm database name]
echo.

:end
