<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/


require_once 'CRM/Core/Rule.php';
require_once 'Validate.php';

class CRM_Type {
    const
        T_INT       =     1,
        T_STRING    =     2,
        T_ENUM      =     2,
        T_DATE      =     4,
        T_TIME      =     8,
        T_BOOL      =    16,
        T_BOOLEAN   =    16,
        T_TEXT      =    32,
        T_BLOB      =    64,
        T_TIMESTAMP =   256,
        T_DOUBLE    =   512,
        T_MONEY     =  1024,
        T_DATE      =  2048,
        T_EMAIL     =  4096,
        T_URL       =  8192,
        T_CCNUM     = 16384,
        T_FLOAT     = 32768;

    const
        TWO          =  2,
        FOUR         =  4,
        EIGHT        =  8,
        TWELVE       = 12,
        SIXTEEN      = 16,
        TWENTY       = 20,
        MEDIUM       = 20,
        THIRTY       = 30,
        BIG          = 30,
        FORTYFIVE    = 45,
        HUGE         = 45;

}

?>