#!/bin/bash

src=/opt/local/lib/php
dst=.

rsyncOptions="-avC --exclude=svn"
rsync="rsync $rsyncOptions"

for code in PEAR DB HTML Log Smarty Validate Pager PHP; do
  echo $code
  [ -a $src/$code.php ] && $rsync $src/$code.php $dst
  [ -d $src/$code ] && $rsync $src/$code $dst
done

[ -d ../PEAR/HTML ] && $rsync ../PEAR/HTML $dst