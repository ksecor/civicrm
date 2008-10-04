#!/bin/bash

bin=`dirname $0`
root="$bin/.."
potdir="$root/l10n/pot/LC_MESSAGES"
tempfile=`tempfile`



# build POT headers
echo "# Copyright CiviCRM LLC (c) 2004-2008
# This file is distributed under the same license as the CiviCRM package.
# If you contribute heavily to a translation and deem your work copyrightable,
# make sure you license it to CiviCRM LLC under Academic Free License 3.0.
msgid \"\"
msgstr \"\"
\"Project-Id-Version: CiviCRM 2.1\n\"
\"POT-Creation-Date: `date +'%F %R%z'`\n\"
\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\n\"
\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n\"
\"Language-Team: CiviCRM Translators <civicrm-translators@lists.civicrm.org>\n\"
\"MIME-Version: 1.0\n\"
\"Content-Type: text/plain; charset=UTF-8\n\"
\"Content-Transfer-Encoding: 8bit\n\"" | tee $potdir/civicrm-{menu,core,modules,helpfiles}.pot $potdir/{countries,provinces}.pot > /dev/null

echo "\"Plural-Forms: nplurals=INTEGER; plural=EXPRESSION;\n\"" | tee -a $potdir/civicrm-{core,modules}.pot > /dev/null

echo | tee -a $potdir/civicrm-{menu,core,modules,helpfiles}.pot $potdir/{countries,provinces}.pot > /dev/null


# build the three XML-originating files
echo ' * building civcrm-menu.pot'
grep -h '<title>' templates/Menu/*.xml | cut -b13- | cut -d'<' -f1 | sort | uniq | tail --lines=+2 | while read entry; do echo -e "msgid \"$entry\"\nmsgstr \"\"\n"; done >> $potdir/civicrm-menu.pot
echo ' * building countries.pot'
grep ^INSERT xml/templates/civicrm_country.tpl     | cut -d\" -f4                                  | while read entry; do echo -e "msgid \"$entry\"\nmsgstr \"\"\n"; done >> $potdir/countries.pot
echo ' * building provinces.pot'
grep '^(' xml/templates/civicrm_state_province.tpl | cut -d\" -f4                                  | while read entry; do echo -e "msgid \"$entry\"\nmsgstr \"\"\n"; done >> $potdir/provinces.pot

# make sure none of the province names repeat
msgcomm --more-than 2 $potdir/provinces.pot $potdir/./provinces.pot > $tempfile
msgcomm -u $potdir/provinces.pot $tempfile | msgcat - $tempfile | sponge $potdir/provinces.pot

# drop strings already in countries.pot
msgcomm $potdir/provinces.pot $potdir/countries.pot > $tempfile
msgcomm -u --no-wrap $potdir/provinces.pot $tempfile | sponge $potdir/provinces.pot



# extract ts()- and {ts}-tagged strings and build -core
echo ' * building civicrm-core.pot'
$root/bin/extractor.php core >> $potdir/civicrm-core.pot

# drop strings already in drupal-civicrm.pot and civicrm-menu.pot
msgcomm $potdir/civicrm-core.pot $potdir/drupal-civicrm.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-core.pot $tempfile | sponge $potdir/civicrm-core.pot
msgcomm $potdir/civicrm-core.pot $potdir/civicrm-menu.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-core.pot $tempfile | sponge $potdir/civicrm-core.pot

# extract ts()- and {ts}-tagged strings and build -modules
echo ' * building civicrm-modules.pot'
$root/bin/extractor.php modules >> $potdir/civicrm-modules.pot

# drop strings already in drupal-civicrm.pot, civicrm-menu.pot and civicrm-core.pot
msgcomm $potdir/civicrm-modules.pot $potdir/drupal-civicrm.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-modules.pot $tempfile | sponge $potdir/civicrm-modules.pot
msgcomm $potdir/civicrm-modules.pot $potdir/civicrm-menu.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-modules.pot $tempfile | sponge $potdir/civicrm-modules.pot
msgcomm $potdir/civicrm-modules.pot $potdir/civicrm-core.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-modules.pot $tempfile | sponge $potdir/civicrm-modules.pot

# extract ts()- and {ts}-tagged strings and build -helpfiles
echo ' * building civicrm-helpfiles.pot'
$root/bin/extractor.php helpfiles >> $potdir/civicrm-helpfiles.pot

# drop strings already in drupal-civicrm.pot, civicrm-menu.pot, civicrm-core.pot and civicrm-modules.pot
msgcomm $potdir/civicrm-helpfiles.pot $potdir/drupal-civicrm.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-helpfiles.pot $tempfile | sponge $potdir/civicrm-helpfiles.pot
msgcomm $potdir/civicrm-helpfiles.pot $potdir/civicrm-menu.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-helpfiles.pot $tempfile | sponge $potdir/civicrm-helpfiles.pot
msgcomm $potdir/civicrm-helpfiles.pot $potdir/civicrm-core.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-helpfiles.pot $tempfile | sponge $potdir/civicrm-helpfiles.pot
msgcomm $potdir/civicrm-helpfiles.pot $potdir/civicrm-modules.pot > $tempfile
msgcomm -u --no-wrap $potdir/civicrm-helpfiles.pot $tempfile | sponge $potdir/civicrm-helpfiles.pot



rm $tempfile
