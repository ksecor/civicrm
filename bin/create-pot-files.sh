#!/bin/sh

bin=`dirname $0`
potdir="$bin/../l10n/pot/LC_MESSAGES"

echo ' * extracting core strings'
$bin/extractor.php core > $potdir/civicrm-core.full.pot

echo ' * extracting modules strings'
$bin/extractor.php modules > $potdir/civicrm-modules.full.pot

echo ' * building civicrm-{common,core,modules}.pot files'
msgcomm $potdir/civicrm-core.full.pot $potdir/civicrm-modules.full.pot > $potdir/civicrm-common.pot
msgcomm -u $potdir/civicrm-core.full.pot $potdir/civicrm-common.pot > $potdir/civicrm-core.pot
msgcomm -u $potdir/civicrm-modules.full.pot $potdir/civicrm-common.pot > $potdir/civicrm-modules.pot

echo ' * cleanup'
rm $potdir/civicrm-core.full.pot $potdir/civicrm-modules.full.pot
