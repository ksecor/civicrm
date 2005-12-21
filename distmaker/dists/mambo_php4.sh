#!/bin/bash

# This script assumes
# that DAOs are generated
# and all the necessary conversions had place!

P=`dirname $0`
CFFILE=$P/../distmaker.conf

if [ ! -f $CFFILE ] ; then
	echo "NO DISTMAKER.CONF FILE!"
	exit 1
else
	for l in `cat $CFFILE`; do export $l; done
fi

RSYNCOPTIONS="-avC --exclude=svn"
RSYNCCOMMAND="rsync $RSYNCOPTIONS"
SRC=$DM_SOURCEDIR
TRG=$DM_TMPDIR/civicrm

# make sure and clean up before
if [ -d $TRG ] ; then
	rm -rf $TRG/*
fi

# copy generated files first
for E in CRM api modules; do
	echo $E
	[ -d $DM_GENFILESDIR/$E ] && $RSYNCCOMMAND $DM_GENFILESDIR/$E $TRG
done

# copy all the rest of the stuff
for CODE in css i js l10n packages PEAR templates bin mambo; do
  echo $CODE
  [ -d $SRC/$CODE ] && $RSYNCCOMMAND $SRC/$CODE $TRG
done

# delete any setup.sh or setup.php4.sh if present
if [ -d $TRG/bin ] ; then
  rm -f $TRG/bin/setup.sh
  rm -f $TRG/bin/setup.php4.sh
  rm -f $TRG/bin/setup.bat
fi

# delete current config.inc.php
rm -f $TRG/modules/config.inc.php $TRG/mambo/config.inc.php

# copy sample config file
cp $SRC/modules/config.inc.php.sample $TRG/modules/

# copy selected sqls
if [ ! -d $TRG/sql ] ; then
	mkdir $TRG/sql
fi
for F in $SRC/sql/civicrm_*.mysql; do
	cp $F $TRG/sql
done

# delete any setup.sh or setup.php4.sh if present
if [ -d $TRG/bin ] ; then
  rm -f $TRG/bin/setup.sh
  rm -f $TRG/bin/setup.php4.sh
fi

# copy docs
cp $SRC/license.txt $TRG
cp $SRC/affero_gpl.txt $TRG
cp $SRC/gpl.txt $TRG
cp $SRC/Readme.txt $TRG

# final touch
REV=`svnversion -n $SRC`
echo "1.3.$REV Mambo PHP4" > $TRG/civicrm-version.txt


# gen zip file
cd $DM_TMPDIR;

mkdir com_civicrm
mkdir com_civicrm/civicrm

cp -r -p civicrm/* com_civicrm/civicrm

$DM_PHP5PATH/php $DM_SOURCEDIR/distmaker/utils/mamboxml.php

cp -r com_civicrm/civicrm/mambo/* com_civicrm
cp -r com_civicrm/civicrm/modules/config.main.php com_civicrm

zip -r -9 $DM_TARGETDIR/civicrm-mambo-php4-v1.3BETA-rev$REV.zip com_civicrm

# clean up
rm -rf com_civicrm
rm -rf $TRG
