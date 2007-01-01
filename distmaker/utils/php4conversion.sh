#!/bin/sh

P=`dirname $0`
CFFILE=$P/../distmaker.conf

if [ ! -f $CFFILE ] ; then	
	echo "NO DISTMAKER.CONF FILE!"
	exit 1
else
	for l in `cat $CFFILE`; do export $l; done
fi


echo;echo Start of code conversion from php5 to php4....;echo;
$DM_PHP $P/converter.php

rsyncOptions="-avC --exclude=svn --ignore-existing"
rsync="$DM_RSYNC $rsyncOptions"
for code in css i js l10n packages PEAR templates bin sql joomla drupal; do
  echo $code
  [ -d $DM_SOURCEDIR/$code ] && $rsync $DM_SOURCEDIR/$code $DM_GENFILESDIR
done

# we first copy all the drupal files before we convert the civicrm module
$DM_PHP $P/converter.php $DM_SOURCEDIR/drupal/civicrm.module > $DM_GENFILESDIR/drupal/civicrm.module
$DM_PHP $P/converter.php $DM_SOURCEDIR/drupal/api.php > $DM_GENFILESDIR/drupal/api.php

echo;echo End of code conversion from php5 to php4....;echo;
