<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'CRM/Core/DAO.php';

class CRM_Utils_Weight {

    /**
     * Adjust the weights so that it starts at 1, with no gaps
     *
     * @param string $daoName full name of the DAO
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $keyField field unique identifier for the table,
     * defaults to 'id'
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return bool
     */
//    static function adjustWeight( $daoName,
//                                  $fieldValues,
//                                  $keyField = 'id',
//                                  $weightField = 'weight' )
//    {
//        CRM_Core_Error::fatal( "This currently does not work as expected.  Please do not try to use it." );
//
//        $weightDAO =& CRM_Utils_Weight::query( 'SELECT', $daoName, $fieldValues, $keyField, null, $weightField );
//        
//        $idlist = array( );
//        while ( $weightDAO->fetch() ) {
//            $idlist[] = $weightDAO->$keyField;
//        }
//
//        if ( empty( $idlist ) ) {
//            return false;
//        }
//
//        $params = array();
//
//        // transaction to insulate the weight variable
//        CRM_Core_DAO::transaction( 'BEGIN' );
//        CRM_Core_DAO::executeQuery( 'SET @weight=0', $params );
//
//        $where  = "$keyField IN (" . implode( ',', $idlist ) . ")";
//        $update = "$weightField = (@weight:=@weight+1)";
//        CRM_Utils_Weight::query( 'UPDATE', $daoName, array(), $update, $where );
//
//        CRM_Core_DAO::transaction( 'COMMIT' );
//        return true;
//    }

    /**
     * Create a gap for a new row to be inserted at the specified weight
     *
     * @param string $daoName full name of the DAO
     * $param integer $weight the desired weight
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return integer the value of the new weight.  may be different from the
     * requested weight if greater than the max weight
     */
     static function addWeight( $daoName, $weight, $fieldValues, $weightField = 'weight' )
     {
         $weight = (int ) $weight;
         if ( $weight < 1 ) {
             $weight = 1;
         }

         // get current max
         $maxWeight = CRM_Utils_Weight::getMax($daoName, $fieldValues, $weightField);

         if ( $weight >= $maxWeight ) {
             // no adjustement to database necessary
             return $maxWeight;
         }

         $additionalWhere = "$weightField >= $weight";
         $update = "$weightField = $weightField + 1";
         CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );

         return $weight;
     }

    /**
     * Remove a row from the specified weight, and shift all rows below it up
     *
     * @param string $daoName full name of the DAO
     * $param integer $weight the weight to be removed
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return bool 
     */
//     static function delWeight($daoName, $weight, $fieldValues, $weightField = 'weight') 
//     {
//         // fieldValues may not be empty.  This would cause a few problems.
//         if ( empty( $fieldValues ) ) {
//             return false;
//         }

//         $weight = (int)$weight;
//         if ( $weight < 1 ) {
//             return false;
//         }

//         // verify weight is not in use
//         $fromField = 'COUNT(*) AS weight_exists';
//         $additionalWhere = "$weightField = $weight";
//         $weightDAO = CRM_Utils_Weight::query( 'SELECT', $daoName, $fieldValues, $fromField, $additionalWhere );
//         $weightDAO->fetch();
//         if ( $weightDAO->weight_exists ) {
//             return false;
//         }

//         // fill the gap
//         $additionalWhere = "$weightField > $weight";
//         $update = "$weightField = $weightField - 1";
//         CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );

//         return true;
//     }

    /**
     * Remove a row from the specified weight, and shift all rows below it up
     *
     * @param string $daoName full name of the DAO
     * $param integer $weight the weight to be removed
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return bool 
     */
    static function delWeight($daoName, $fieldID, $fieldValues, $weightField = 'weight') 
    {
        // fieldValues may not be empty.  This would cause a few problems.
        if ( empty( $fieldValues ) ) {
            return false;
        }

        require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
        eval( '$object   =& new ' . $daoName . '( );' );
        $object->id = $fieldID;
        if ( !$object->find( true ) ) {
            return false;
        }        

        $weight = (int)$object->weight;
        if ( $weight < 1 ) {
            return false;
        }

        // fill the gap
        $additionalWhere = "$weightField > $weight";
        $update = "$weightField = $weightField - 1";
        CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );

        return true;
    }

    /**
     * Move a row to a new weight
     *
     * @param string $daoName short name of the DAO
     * @param integer $oldWeight
     * @param integer $newWeight 
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return bool 
     */
//     static function moveWeight($daoName, $fieldId, $oldWeight, $newWeight, $fieldValues, $weightField = 'weight')
//     {
//         $oldWeight = (int ) $oldWeight;
//         $newWeight = (int ) $newWeight;

//         $maxWeight = CRM_Utils_Weight::getMax($daoName, $fieldValues, $weightField);

//         // make sure the new weight is within the correct range
//         if ( $newWeight > $maxWeight ) {
//             $newWeight = $maxWeight;
//         } elseif ( $newWeight < 1 ) {
//             $newWeight = 1;
//         }

//         // if they're the same, nothing to do
//         if ( $oldWeight == $newWeight ) {
//             return $newWeight;
//         }

//         // create a gap at the necessary position, if needed
//         if ( $newWeight < $maxWeight ) {
//             if ( $newWeight > $oldWeight ) {
//                 // account for subsequent shifts down
//                 $newWeight++;
//             }
//             $additionalWhere = "$weightField >= $newWeight";
//             $update = "$weightField = $weightField + 1";
//             CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
//         }

//         // move the row
//         if ( $oldWeight > $newWeight ) {
//             // don't move the target row
//             $oldWeight++;
//         }
//         $additionalWhere = "$weightField = $oldWeight";
//         $update = "$weightField = $newWeight";
//         CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );

//         // close the gap
//         $additionalWhere = "$weightField > $oldWeight";
//         $update = "$weightField = $weightField - 1";
//         CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
        
//         // get the new weight value
//         $fieldValues = array( 'id' => $fieldId );
//         $weightDAO =& CRM_Utils_Weight::query( 'SELECT', $daoName, $fieldValues, $weightField );
//         $weightDAO->fetch();

//         return $weightDAO->weight;
//     }
        
    /**
     * Updates the weight fields of other rows according to the new and old weight paased in. 
     * And returns the new weight be used.
     *
     * @param string $daoName full name of the DAO
     * @param integer $oldWeight
     * @param integer $newWeight 
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return bool 
     */
    static function updateOtherWeights($daoName, $oldWeight, $newWeight, $fieldValues, $weightField = 'weight')
    {
        $oldWeight = (int ) $oldWeight;
        $newWeight = (int ) $newWeight;
        
        if (!$newWeight) {
            return;
        }

        // max weight is the highest current weight
        $maxWeight = CRM_Utils_Weight::getMax($daoName, $fieldValues, $weightField) - 1;

        if ( $newWeight >= $maxWeight ) {
            $newWeight = $maxWeight;
        } elseif ( $newWeight < 1 ) {
            $newWeight = 1;
        }
        
        // if they're the same, nothing to do
        if ( $oldWeight == $newWeight ) {
            return $newWeight;
        }

        if (!$oldWeight) {
            $additionalWhere = "$weightField >= $newWeight";
            $update = "$weightField = ($weightField + 1)";
            CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
            return $newWeight;
        } else {
            if ( $newWeight > $oldWeight ) {
                if (($newWeight-$oldWeight) == 1) {
                    $additionalWhere = "$weightField = $newWeight";
                    $update = "$weightField = ($weightField - 1)";
                    CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
                    return $newWeight;
                } else {
                    $additionalWhere = "$weightField > $oldWeight AND $weightField <= $newWeight";
                    $update = "$weightField = ($weightField - 1)";
                    CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
                    return $newWeight;
                }
            } elseif ($newWeight < $oldWeight) {
                $additionalWhere = "$weightField >= $newWeight AND $weightField < $oldWeight";
                $update = "$weightField = ($weightField + 1)";
                CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
                return $newWeight;
            }
        }

        return $newWeight;
    }

    /**
     * return the highest weight + 1
     *
     * @param string $daoName full name of the DAO
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return integer
     */
    static function getMax($daoName, $fieldValues, $weightField = 'weight')
    {
        $selectField = "MAX($weightField) AS max_weight";
        $weightDAO =& CRM_Utils_Weight::query( 'SELECT', $daoName, $fieldValues, $selectField );
        $weightDAO->fetch();
        if ( $weightDAO->max_weight ) {
            return $weightDAO->max_weight + 1;
        } else {
            return 1;
        }
    }

    /**
     * Execute a weight-related query
     *
     * @param string $queryType SELECT, UPDATE, DELETE
     * @param string $daoName full name of the DAO
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $queryData data to be used, dependent on the query type
     * @param string $orderBy optional ORDER BY field
     * @return Object CRM_Core_DAO objet that holds the results of the query
     */
    static function &query( $queryType,
                            $daoName,
                            $fieldValues,
                            $queryData,
                            $additionalWhere = null,
                            $orderBy = null )
    {
        require_once 'CRM/Utils/Type.php';

        require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");

        $dao =& new $daoName;
        $table = $dao->getTablename();
        $fields =& $dao->fields();
        $fieldlist = array_keys($fields);

        $whereConditions = array();
        if ($additionalWhere) {
            $whereConditions[] = $additionalWhere;
        }
        $params = array();
        $fieldNum = 0;
        foreach ( $fieldValues as $fieldName => $value ) {
            if ( !in_array( $fieldName, $fieldlist) ) {
                // invalid field specified.  abort.
                return false;
            }
            $fieldNum++;
            $whereConditions[] = "$fieldName = %$fieldNum";
            $fieldType = $fields[$fieldName]['type'];
            $params[$fieldNum] = array( $value, CRM_Utils_Type::typeToString( $fieldType ) );
        }
        $where = implode(' AND ', $whereConditions);

        switch ( $queryType ) {
            case 'SELECT':
                $query = "SELECT $queryData FROM $table WHERE $where";
                if ( $orderBy ) {
                    $query .= " ORDER BY $orderBy";
                }
                break;

            case 'UPDATE':
                $query = "UPDATE $table SET $queryData";
                $query .= " WHERE $where";
                break;

            case 'DELETE':
                $query = "DELETE FROM $table WHERE $where AND $queryData";
                break;
            default:
                return false;

        }

        $resultDAO = CRM_Core_DAO::executeQuery( $query, $params );
        return $resultDAO;
    }
}
