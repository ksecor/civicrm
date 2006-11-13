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
$DM_PHP5PATH/php $P/converter.php

#[ ! -d $DM_GENFILESDIR/modules ] && mkdir $DM_GENFILESDIR/modules
#$DM_PHP5PATH/php $P/converter.php $DM_SOURCEDIR/modules/civicrm.module > $DM_GENFILESDIR/modules/civicrm.module

[ ! -d $DM_GENFILESDIR/drupal ] && mkdir $DM_GENFILESDIR/drupal
$DM_PHP5PATH/php $P/converter.php $DM_SOURCEDIR/drupal/civicrm.module > $DM_GENFILESDIR/drupal/civicrm.module

rsyncOptions="-avC --exclude=svn --ignore-existing"
rsync="`which rsync` $rsyncOptions"
for code in css i js l10n packages PEAR templates bin sql joomla; do
  echo $code
  [ -d $DM_SOURCEDIR/$code ] && $rsync $DM_SOURCEDIR/$code $DM_GENFILESDIR
done

echo;echo End of code conversion from php5 to php4....;echo;
