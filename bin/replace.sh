#!/bin/sh
for j in test; do
  cd ../$j;
  for i in `find . -name \*.js`; do
    echo $i;
    perl -pi -e 's/CiviCRM version 1.5/CiviCRM version 1.6/' $i;
    perl -pi -e 's/Copyright CiviCRM LLC (c) 2004-2006/Copyright CiviCRM LLC (c) 2004-2006                                /' $i;   
    perl -pi -e 's/Foundation at info\[AT]socialsourcefoundation\[DOT]org.  If you have/Foundation at info\[AT]civicrm\[DOT]org.  If you have questions      /' $i;
    perl -pi -e 's/questions about the Affero General Public License or the licensing/about the Affero General Public License or the licensing  of      /' $i;
    perl -pi -e 's/CiviCRM, see the CiviCRM license FAQ at                         /CiviCRM, see the CiviCRM license FAQ at                         /' $i;
    perl -pi -e 's/at http:\/\/www.openngo.org\/faqs\/licensing.html/http:\/\/civicrm.org\/licensing\/           /' $i;
  done
done


