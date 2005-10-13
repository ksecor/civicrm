#!/bin/bash

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

$DM_PHP5PATH/php $P/converter.php $DM_SOURCEDIR/modules/civicrm.module > $DM_GENFILESDIR/modules/civicrm.module

#$DM_PHP5PATH/php $P/converter.php $DM_GENFILESDIR/CRM/Utils/Type.php > $DM_TMPDIR/Type.php
# mv $DM_TMPDIR/Type.php $DM_GENFILESDIR/CRM/Utils/Type.php

# $DM_PHP5PATH/php $P/converter.php $DM_GENFILESDIR/CRM/Contact/Task.php > $DM_TMPDIR/Task.php
# mv $DM_TMPDIR/Task.php $DM_GENFILESDIR/CRM/Contact/Task.php

# $DM_PHP5PATH/php $P/converter.php $DM_GENFILESDIR/CRM/Contact/Form/Location.php > $DM_TMPDIR/Location.php
# mv $DM_TMPDIR/Location.php $DM_GENFILESDIR/CRM/Contact/Form/Location.php

# $DM_PHP5PATH/php $P/converter.php $DM_GENFILESDIR/CRM/Import/Parser.php > $DM_TMPDIR/Parser.php
# mv $DM_TMPDIR/Parser.php $DM_GENFILESDIR/CRM/Import/Parser.php

rsyncOptions="-avC --exclude=svn"
rsync="rsync $rsyncOptions"
for code in css i js l10n packages PEAR templates bin sql mambo; do
  echo $code
  [ -d $DM_SOURCEDIR/$code ] && $rsync $DM_SOURCEDIR/$code $DM_GENFILESDIR
done

echo;echo End of code conversion from php5 to php4....;echo;
