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
	. $CFFILE
fi

RSYNCOPTIONS="-avC --exclude=svn"
RSYNCCOMMAND="$DM_RSYNC $RSYNCOPTIONS"
SRC=$DM_SOURCEDIR
TRG=$DM_TMPDIR/civicrm

# make sure and clean up before
if [ -d $TRG ] ; then
	rm -rf $TRG/*
fi

# copy all the rest of the stuff
for CODE in css i install js l10n packages PEAR templates bin joomla CRM api drupal extern Reports; do
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
for F in $SRC/sql/civicrm*.mysql; do
	cp $F $TRG/sql
done

# remove Quest
find $TRG -depth -name 'Quest' -exec rm -r {} \;

# delete SimpleTest
if [ -d $TRG/packages/SimpleTest ] ; then
  rm -rf $TRG/packages/SimpleTest
fi
if [ -d $TRG/packages/drupal ] ; then
  rm -rf $TRG/packages/drupal
fi

# delete UFPDF's stuff not required on installations
if [ -d $TRG/packages/ufpdf/ttf2ufm-src ] ; then
  rm -rf $TRG/packages/ufpdf/ttf2ufm-src
fi

# copy docs
cp $SRC/agpl-3.0.txt $TRG
cp $SRC/gpl.txt $TRG
cp $SRC/README.txt $TRG
cp $SRC/civicrm.config.php $TRG

# final touch
echo "$DM_VERSION Joomla PHP5" > $TRG/civicrm-version.txt

# gen zip file
cd $DM_TMPDIR;

mkdir com_civicrm
mkdir com_civicrm/admin
mkdir com_civicrm/site
mkdir com_civicrm/admin/civicrm

# cp -r -p civicrm/* com_civicrm/admin/civicrm

$DM_PHP $DM_SOURCEDIR/distmaker/utils/joomlaxml.php $DM_SOURCEDIR com_civicrm $DM_VERSION

# copying back end code to admin folder
cp civicrm/joomla/admin/admin.civicrm.php        com_civicrm/admin
cp civicrm/joomla/admin/configure.php            com_civicrm/admin
cp civicrm/joomla/admin/install.civicrm.php      com_civicrm/admin
cp civicrm/joomla/admin/toolbar.civicrm.php      com_civicrm/admin
cp civicrm/joomla/admin/toolbar.civicrm.html.php com_civicrm/admin
cp civicrm/joomla/admin/uninstall.civicrm.php    com_civicrm/admin

# copying front end code
cp civicrm/joomla/site/civicrm.html.php      com_civicrm/site
cp civicrm/joomla/site/civicrm.php           com_civicrm/site
cp -r civicrm/joomla/site/views              com_civicrm/site

$DM_ZIP -q -r -9 com_civicrm/admin/civicrm.zip civicrm -x '*/l10n/*' -x '*/sql/civicrm_*.??_??.mysql'

# remove civicrm directory
rm -rf com_civicrm/admin/civicrm

# also create an empty shell civicrm directory for the folder install
mkdir com_civicrm/admin/civicrm

$DM_ZIP -q -r -9 $DM_TARGETDIR/civicrm-$DM_VERSION-joomla.zip com_civicrm -x '*/l10n/*' -x '*/sql/civicrm_*.??_??.mysql'

# clean up
rm -rf com_civicrm
rm -rf $TRG
