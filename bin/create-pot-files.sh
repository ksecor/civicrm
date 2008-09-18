#!/bin/bash

bin=`dirname $0`
potdir="$bin/../l10n/pot/LC_MESSAGES"
xmldir="$bin/../xml/templates"

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

echo ' * building countries.pot'
echo '# Copyright CiviCRM LLC (c) 2004-2007
# This file is distributed under the same license as the CiviCRM package.
# If you contribute heavily to a translation and deem your work copyrightable,
# make sure you license it to CiviCRM LLC under Academic Free License 3.0.
msgid ""
msgstr ""
"Project-Id-Version: CiviCRM 2.1\n"
"POT-Creation-Date: 2007-04-18 07:35+0200\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\n"
"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n"
"Language-Team: CiviCRM Translators <civicrm-translators@lists.civicrm.org>\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
' > $potdir/countries.pot
grep ^INSERT xml/templates/civicrm_country.tpl | cut -d\" -f4 - | while read country; do echo -e "msgid \"$country\"\nmsgstr \"\"\n"; done >> $potdir/countries.pot

echo ' * building provinces.pot'
echo '# Copyright CiviCRM LLC (c) 2004-2007
# This file is distributed under the same license as the CiviCRM package.
# If you contribute heavily to a translation and deem your work copyrightable,
# make sure you license it to CiviCRM LLC under Academic Free License 3.0.
msgid ""
msgstr ""
"Project-Id-Version: CiviCRM 2.1\n"
"POT-Creation-Date: 2007-04-18 07:35+0200\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\n"
"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n"
"Language-Team: CiviCRM Translators <civicrm-translators@lists.civicrm.org>\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
' > $potdir/provinces.pot
grep '^(' xml/templates/civicrm_state_province.tpl | cut -d\" -f4 - | while read province; do echo -e "msgid \"$province\"\nmsgstr \"\"\n"; done >> $potdir/provinces.pot
