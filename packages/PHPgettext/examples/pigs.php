<?php
/*
   Copyright (c) 2003 Danilo Segan <danilo@kvota.net>.

   This file is part of PHP-gettext.

   PHP-gettext is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   PHP-gettext is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PHP-gettext; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

require("../streams.php");
require("../gettext.php");
$input = new FileReader('serbian.mo');
$l10n = new gettext_reader($input);

// create standard wrapers, so xgettext could work
function T_($text) {
  global $l10n;
  return $l10n->translate($text);
}

function T_ngettext($single, $plural, $number) {
  global $l10n;
  return $l10n->ngettext($single, $plural, $number);
}
    
print T_("This is how the story goes.\n\n");
for ($number=6; $number>=0; $number--) {
  print sprintf( T_ngettext("%d pig went to the market\n", 
			  "%d pigs went to the market\n", $number), 
		 $number );
}

?>
