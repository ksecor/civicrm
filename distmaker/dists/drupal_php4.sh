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

# copy generated files first
for E in CRM api drupal bin extern; do
	echo $E
	[ -d $DM_GENFILESDIR/$E ] && $RSYNCCOMMAND $DM_GENFILESDIR/$E $TRG
done

# copy all the rest of the stuff
for CODE in css i js l10n packages PEAR templates joomla; do
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

# delete any setup.sh or setup.php4.sh if present
if [ -d $TRG/bin ] ; then
  rm -f $TRG/bin/setup.sh
  rm -f $TRG/bin/setup.php4.sh
fi

# remove Quest
find $TRG -name 'Quest' -exec rm -r {} \;

# copy docs
cp $SRC/gpl.txt $TRG 
cp $SRC/affero_gpl.txt $TRG
cp $SRC/README.txt $TRG
cp $SRC/civicrm.config.php $TRG
cp $SRC/civicrm.settings.php.sample $TRG

# final touch
echo "$DM_VERSION Drupal PHP4" > $TRG/civicrm-version.txt


# gen tarball
cd $TRG/..
tar czf $DM_TARGETDIR/civicrm-$DM_VERSION-drupal-php4.tar.gz --exclude l10n civicrm

# clean up
rm -rf $TRG
