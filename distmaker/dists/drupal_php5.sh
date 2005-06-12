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

# copy all the stuff
for CODE in css i js l10n packages PEAR templates CRM api modules; do
  echo $CODE
  [ -d $SRC/$CODE ] && $RSYNCCOMMAND $SRC/$CODE $TRG
done

# copy selected sqls
if [ ! -d $TRG/sql ] ; then
	mkdir $TRG/sql
fi
for F in Contacts.sql FixedData.sql GeneratedData.sql; do 
	cp $SRC/sql/$F $TRG/sql
done

# copy docs
cp $SRC/license.txt $TRG
cp $SRC/affero_gpl.txt $TRG

# final touch
SNPDATE=`date +%Y%m%d%H%M`
echo "CiviCRM ver. snp$SNPDATE snapshot for Drupal on PHP5" > $TRG/version.txt


# gen tarball
cd $TRG/..
tar czf $DM_TARGETDIR/civicrm-drupal-php5-snp$SNPDATE.tgz civicrm

# clean up
rm -rf $TRG