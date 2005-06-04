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

/**
 * One place to store frequently used values in Select Elements. Note that
 * some of the below elements will be dynamic, so we'll probably have a 
 * smart caching scheme on a per domain basis
 * 
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

$GLOBALS['_CRM_CORE_SELECTVALUES']['prefixName'] =  array(
                                      ''    => '-title-',
                                      'Mrs' => 'Mrs.',
                                      'Ms'  => 'Ms.',
                                      'Mr'  => 'Mr.',
                                      'Dr'   => 'Dr.',
                                      ' ' => '(none)',
                                      );
$GLOBALS['_CRM_CORE_SELECTVALUES']['suffixName'] =  array(
                                      ''    => '-suffix-',
                                      'Jr'  => 'Jr.',
                                      'Sr'  => 'Sr.',
                                      'II'   =>'II',
                                      ' ' => '(none)',
                                      );
$GLOBALS['_CRM_CORE_SELECTVALUES']['greeting'] =  array(
                                      'Formal'    => 'default - Dear [first] [last]',
                                      'Informal'  => 'Dear [first]',
                                      'Honorific' => 'Dear [title] [last]',
                                      'Custom'    => 'Customized',
                                      );
$GLOBALS['_CRM_CORE_SELECTVALUES']['_date'] =  array(
                                        'language'         => 'en',
                                        'format'           => 'd M Y',
                                        'addEmptyOption'   => true,
                                        'emptyOptionText'  => '-select-',
                                        'emptyOptionValue' => ''
                                      );
$GLOBALS['_CRM_CORE_SELECTVALUES']['phoneType'] =  array(
                                      ''       => '-select-',
                                      'Phone'  => 'Phone',
                                      'Mobile' => 'Mobile',
                                      'Fax'    => 'Fax',
                                      'Pager'  => 'Pager'
                                      );
$GLOBALS['_CRM_CORE_SELECTVALUES']['county'] =  array(
                                  ''   => '-select-',
                                  1001 => 'San Francisco',
                                  );
$GLOBALS['_CRM_CORE_SELECTVALUES']['pcm'] =  array(
                               ''     => '-no preference-',
                               'Phone' => 'Phone', 
                               'Email' => 'Email', 
                               'Post'  => 'Postal Mail',
                               );
$GLOBALS['_CRM_CORE_SELECTVALUES']['contactType'] =  array(
                                       ''            => '- all contacts -',
                                       'Individual'   => 'Individuals',
                                       'Household'    => 'Households',
                                       'Organization' => 'Organizations',
                                       );
$GLOBALS['_CRM_CORE_SELECTVALUES']['customDataType'] =  array(
                                          ''           => '-select-',
                                          'String'     => 'Text',
                                          'Int'        => 'Integer',
                                          'Float'      => 'Decimal Number',
                                          'Money'      => 'Money',
                                          'Text'       => 'Memo',
                                          'Date'       => 'Date',
                                          'Boolean'    => 'Yes/No',
                                          );
$GLOBALS['_CRM_CORE_SELECTVALUES']['customHtmlType'] =  array(
                                          ''                        => '-select-',
                                          'Text'                    => 'Single-line input field (text or numeric)',
                                          'TextArea'                => 'Multi-line text box (textarea)',
                                          'Select'                  => 'Drop-down (select list)',
                                          'Radio'                   => 'Radio buttons',
                                          'Checkbox'                => 'Checkbox(es)',
                                          'Select Date'             => 'Date selector',
                                          'Select State / Province' => 'State / Province selector',
                                          'Select Country'          => 'Country selector',
                                          );
$GLOBALS['_CRM_CORE_SELECTVALUES']['customGroupExtends'] =  array(
                                              'Contact'      => '-all contact types-',
                                              'Individual'   => 'Individuals',
                                              'Household'    => 'Households',
                                              'Organization' => 'Organizations',
                                              );
$GLOBALS['_CRM_CORE_SELECTVALUES']['customGroupStyle'] =  array(
                                            'Tab'    => 'Tab',
                                            'Inline' => 'Inline',
                                            );
$GLOBALS['_CRM_CORE_SELECTVALUES']['groupContactStatus'] =  array(
                                              'In'      => 'In',
                                              'Out'     => 'Out',
                                              'Pending' => 'Pending',
                                              );
$GLOBALS['_CRM_CORE_SELECTVALUES']['groupType'] =  array(
                                     'query'    => 'Dynamic',
                                     'static'   => 'Static',
                                     );


class CRM_Core_SelectValues {

    /**
     * prefix names
     * @var array
     * @static
     */
    

    /**
     * suffix names
     * @var array
     * @static
     */
    

    /**
     * greetings
     * @var array
     * @static
     */
    
    
    /**
     * date combinations. We need to fix maxYear (and we do so at the
     * end of this file)
     * static values cannot invoke a function in php
     * @var array
     * @static
     */
    

    /**
     * different types of phones
     * @var array
     * @static
     */
    


    /**
     * list of counties
     * @var array
     * @static
     */
    
    
    /**
     * preferred communication method
     * @var array
     * @static
     */
      

    /**
     * various pre defined contact super types
     * @var array
     * @static
     */
    
    

    /**
     * Extended property (custom field) data types
     *
     * @var array
     * @static
     */
    
    
    /**
     * Custom form field types
     * @var array
     * @static
     */
    
    
    
    /**
     * various pre defined extensions for dynamic properties and groups
     *
     * @var array
     * @static
     */
    


    /**
     * styles for displaying the custom data group
     *
     * @var array
     * @static
     */
    


    /**
     * the status of a contact within a group
     *
     * @ @var array
     * @static
     */
    

    /**
     * list of Group Types
     * @var array
     * @static
     */
    
  
    
    /**
     * compose the parameters for a date select object
     *
     * @param  $type the type of date
     *
     * @return array         the date array
     * @access public
     * @static
     */
     function &date( $type = 'birth' ) {
        $newDate = $GLOBALS['_CRM_CORE_SELECTVALUES']['_date'];

        if ( $type == 'birth' ) {
            $minOffset = 100;
            $maxOffset = 0;
        } else if ( $type == 'relative' ) {
            $minOffset = 20;
            $maxOffset = 20;
        } else if ( $type == 'custom' ) {
            $minOffset = 100;
            $maxOffset = 20;
        }
        
        $year = date('Y');
        $newDate['minYear'] = $year - $minOffset;
        $newDate['maxYear'] = $year + $maxOffset;

        return $newDate;
    }

}

?>
