#!/bin/sh

bad=0
if [ "$1" = "" ]; then
 bad=1
elif [ "$2" = "" ]; then
 bad=1
elif [ "$3" = "" ]; then
 bad=1
elif [ "$4" = "" ]; then
 bad=1
fi

if [ "$bad" -eq 1 ]; then
  echo "Usage: reload [url of civicrm homepage] [database username with drop/create privilege] [database password] [civicrm database name]"
else
  echo "drop database $4; create database $4; use $4; source ../../../../sql/civicrm.mysql; source ../../../../sql/civicrm_data.mysql; source physicianhealthbc.mysql;" | mysql -u $2 --password=$3

# This would work, except the firewall prevents it. You need to visit the homepage from outside, then run the php line below.
#  wget -O - $1
#  php physicianhealthbc.php $2 $3 $4
fi
