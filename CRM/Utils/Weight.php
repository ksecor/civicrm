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
     * Function to correct duplicate weight entries by putting them (duplicate weights) in sequence.
     *
     * @param string  $daoName full name of the DAO
     * @param array   $fieldValues field => value to be used in the WHERE
     * @param string  $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return bool 
     */
    static function correctDuplicateWeights($daoName, $fieldValues = null, $weightField = 'weight') 
    {
        $selectField = "MIN(id) AS dupeId, count(id) as dupeCount, $weightField as dupeWeight";
        $groupBy     = "$weightField having dupeCount>1";
        
        $minDupeID =& CRM_Utils_Weight::query( 'SELECT', $daoName, $fieldValues, $selectField, null, null, $groupBy );
        $minDupeID->fetch();
        
        if ( $minDupeID->dupeId ) {
            $additionalWhere = "id !=". $minDupeID->dupeId . " AND $weightField >= " . $minDupeID->dupeWeight;
            $update = "$weightField = $weightField + 1";
            $status = CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
        }
        
        if ( $minDupeID->dupeId && $status ) {
            //recursive call to correct all duplicate weight entries.
            return CRM_Utils_Weight::correctDuplicateWeights($daoName, $fieldValues, $weightField);

        } elseif ( !$minDupeID->dupeId ) { 
            // case when no duplicate records are found.
            return true;

        } elseif ( !$status ) {
            // case when duplicate records are found but update status is false.
            return false;
        }
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
    static function delWeight($daoName, $fieldID, $fieldValues = null, $weightField = 'weight') 
    {
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
        $status = CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );

        return $status;
    }
        
    /**
     * Updates the weight fields of other rows according to the new and old weight paased in. 
     * And returns the new weight be used. If old-weight not present, Creates a gap for a new row to be inserted 
     * at the specified new weight
     *
     * @param string $daoName full name of the DAO
     * @param integer $oldWeight
     * @param integer $newWeight 
     * @param array $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return bool 
     */
    static function updateOtherWeights($daoName, $oldWeight, $newWeight, $fieldValues = null, $weightField = 'weight')
    {
        $oldWeight = (int ) $oldWeight;
        $newWeight = (int ) $newWeight;
        
        // max weight is the highest current weight
        $maxWeight = CRM_Utils_Weight::getMax($daoName, $fieldValues, $weightField);
        if ( !$maxWeight ) {
            $maxWeight = 1;
        }
        
        if ( $newWeight > $maxWeight ) {
            $newWeight = $maxWeight;
            if (!$oldWeight) {
                return $newWeight+1; 
            }
        } elseif ( $newWeight < 1 ) {
            $newWeight = 1;
        }
        
        // if they're the same, nothing to do
        if ( $oldWeight == $newWeight ) {
            return $newWeight;
        }
        
        // if oldWeight not present, indicates new weight is to be added. So create a gap for a new row to be inserted. 
        if ( !$oldWeight ) {
            $additionalWhere = "$weightField >= $newWeight";
            $update = "$weightField = ($weightField + 1)";
            CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
            return $newWeight;
        } else {
            if ( $newWeight > $oldWeight ) {
                $additionalWhere = "$weightField > $oldWeight AND $weightField <= $newWeight";
                $update = "$weightField = ($weightField - 1)";
            } elseif ($newWeight < $oldWeight) {
                $additionalWhere = "$weightField >= $newWeight AND $weightField < $oldWeight";
                $update = "$weightField = ($weightField + 1)";
            }
            CRM_Utils_Weight::query( 'UPDATE', $daoName, $fieldValues, $update, $additionalWhere );
            return $newWeight;
        }
    }
    
    /**
     * returns the highest weight.
     *
     * @param string $daoName full name of the DAO
     * @param array  $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return integer
     */
    static function getMax($daoName, $fieldValues = null, $weightField = 'weight')
    {
        $selectField = "MAX($weightField) AS max_weight";
        $weightDAO =& CRM_Utils_Weight::query( 'SELECT', $daoName, $fieldValues, $selectField );
        $weightDAO->fetch();
        if ( $weightDAO->max_weight ) {
            return $weightDAO->max_weight;
        }
        return 0;
    }

    /**
     * returns the default weight ( highest weight + 1 ) to be used.
     *
     * @param string $daoName full name of the DAO
     * @param array  $fieldValues field => value to be used in the WHERE
     * @param string $weightField field which contains the weight value,
     * defaults to 'weight'
     * @return integer
     */
    static function getDefaultWeight($daoName, $fieldValues = null, $weightField = 'weight')
    {
        $maxWeight = CRM_Utils_Weight::getMax($daoName, $fieldValues, $weightField);
        return $maxWeight+1;
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
                            $fieldValues = null,
                            $queryData,
                            $additionalWhere = null,
                            $orderBy = null,
                            $groupBy = null )
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
        if ( is_array($fieldValues) ) {
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
        }
        $where = implode(' AND ', $whereConditions);
        
        switch ( $queryType ) {
            case 'SELECT':
                $query = "SELECT $queryData FROM $table";
                if ( $where ) {
                    $query .= " WHERE $where";
                }
                if ( $groupBy ) {
                    $query .= " GROUP BY $groupBy";
                }
                if ( $orderBy ) {
                    $query .= " ORDER BY $orderBy";
                }
                break;

            case 'UPDATE':
                $query = "UPDATE $table SET $queryData";
                if ( $where ) {
                    $query .= " WHERE $where";
                }
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
