#!/bin/sh

bin=`dirname $0`
potdir="$bin/../l10n/pot/LC_MESSAGES"

echo ' * extracting core strings'
$bin/extractor.php core > $potdir/civicrm-core.pot

echo ' * extracting modules strings'
$bin/extractor.php modules > $potdir/civicrm-modules.full.pot

echo ' * extracting helpfiles strings'
$bin/extractor.php helpfiles > $potdir/civicrm-helpfiles.full.pot

echo ' * building the proper civicrm-modules.pot file'
msgcomm $potdir/civicrm-core.pot $potdir/civicrm-modules.full.pot > $potdir/civicrm-common.pot
msgcomm -u $potdir/civicrm-modules.full.pot $potdir/civicrm-common.pot > $potdir/civicrm-modules.pot

echo ' * building the proper civicrm-helpfiles.pot file'
msgcomm $potdir/civicrm-core.pot $potdir/civicrm-helpfiles.full.pot > $potdir/civicrm-common.pot
msgcomm -u $potdir/civicrm-common.pot $potdir/civicrm-helpfiles.full.pot > $potdir/civicrm-helpfiles.no-core.pot
msgcomm $potdir/civicrm-modules.pot $potdir/civicrm-helpfiles.no-core.pot > $potdir/civicrm-common.pot
msgcomm -u $potdir/civicrm-helpfiles.no-core.pot $potdir/civicrm-common.pot > $potdir/civicrm-helpfiles.pot

echo ' * cleanup'
rm $potdir/civicrm-modules.*.pot $potdir/civicrm-helpfiles.*.pot $potdir/civicrm-common.pot
