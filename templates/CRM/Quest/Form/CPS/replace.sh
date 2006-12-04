#!/bin/sh
  for i in `find . -name \*.tpl`; do
    echo $i;
    perl -pi -e 's/MatchApp/CPS/g;' $i;
    perl -pi -e 's/matchapp/cps/g;' $i;
  done


