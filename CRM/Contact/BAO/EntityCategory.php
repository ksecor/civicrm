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
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Contact_BAO_EntityCategory extends CRM_Contact_DAO_EntityCategory 
{
    static function getValues(&$params, &$values = null, &$ids = null) 
    {
        $category_id = array();
        $entityCategory = new CRM_Contact_BAO_EntityCategory();
        $entityCategory->copyValues($params);
        $entityCategory->entity_id = $params['contact_id'];
        $entityCategory->find();
        while ($entityCategory->fetch()) {
            $category_id[$entityCategory->category_id] = $entityCategory->category_id;
        } 
        return $category_id;        
    }

    static function retrieve(&$params, &$defaults, &$ids) 
    {
        $contact = CRM_Contact_BAO_Contact::getValues($params, $defaults, $ids);
        
        unset($params['id']);
        eval('$contact->contact_type_object = CRM_Contact_BAO_' . $contact->contact_type . '::getValues( $params, $defaults, $ids );');
        
        return $contact;
    }
}
?>