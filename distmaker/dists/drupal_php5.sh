#!/bin/sh

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
RSYNCCOMMAND="$DM_RSYNC $RSYNCOPTIONS"
SRC=$DM_SOURCEDIR
TRG=$DM_TMPDIR/civicrm

# make sure and clean up before
if [ -d $TRG ] ; then
	rm -rf $TRG/*
fi

# copy all the stuff
for CODE in css i js l10n packages PEAR templates bin joomla CRM api drupal extern; do
  echo $CODE
  [ -d $SRC/$CODE ] && $RSYNCCOMMAND $SRC/$CODE $TRG
done

# delete any setup.sh or setup.php4.sh if present
if [ -d $TRG/bin ] ; then
  rm -f $TRG/bin/setup.sh
  rm -f $TRG/bin/setup.php4.sh
  rm -f $TRG/bin/setup.bat
fi


# copy selected sqls
if [ ! -d $TRG/sql ] ; then
	mkdir $TRG/sql
fi

for F in $SRC/sql/civicrm_*.mysql; do 
	cp $F $TRG/sql
done

# remove Quest
find $TRG -name 'Quest' -exec rm -r {} \;

# copy docs
cp $SRC/affero_gpl.txt $TRG
cp $SRC/gpl.txt $TRG 
cp $SRC/README.txt $TRG
cp $SRC/civicrm.config.php $TRG
cp $SRC/civicrm.settings.php.sample $TRG

# final touch
echo "$DM_VERSION Drupal PHP5" > $TRG/civicrm-version.txt


# gen tarball
cd $TRG/..
tar czf $DM_TARGETDIR/civicrm-$DM_VERSION-drupal-php5.tar.gz --exclude l10n civicrm
tar czf $DM_TARGETDIR/civicrm-$DM_VERSION-l10n.tar.gz --include l10n civicrm

# clean up
rm -rf $TRG
