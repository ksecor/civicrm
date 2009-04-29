<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

class SampleContributionGenerator {
    
    /**
     * Generates sample contribution records for existing contributors
     *
     * 
     * @return void
     */
    
    static function process( ) {

        $query = "
SELECT civicrm_contribution.contact_id as cid, civicrm_contribution.receive_date as receiveDate
FROM   civicrm_contribution
";

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $limit = rand (5, 10);
            $receiveDate = CRM_Utils_Date::unformat($dao->receiveDate);
           
            for ( $i = 1; $i < $limit; $i++ ) { 
                $trxnQuery = "SELECT MAX(trxn_id) FROM civicrm_contribution WHERE trxn_id LIKE 'sample\\_%'";
                $p = array( );
                $trxn_id = strval( CRM_Core_Dao::singleValueQuery( $trxnQuery, $p ) );
                $trxn_id = str_replace( 'sample_', '', $trxn_id );
                $trxn_id = intval($trxn_id) + 1;
                $trxn_id = sprintf('sample_%08d', $trxn_id);
                
                $amount = rand ( 10, 99 );
            
                $contributionDate = CRM_Utils_Date::format(CRM_Utils_Date::intervalAdd( 'day', $i+rand(3,10), $receiveDate )); 
                $source   =  CRM_Utils_Date::customFormat( $contributionDate, '%B %Y' );

                $contribParams = array(
                                       'contact_id'            => $dao->cid,
                                       'contribution_type_id'  => 1,
                                       'receive_date'          => $contributionDate,
                                       'total_amount'          => $amount,
                                       'currency'              => 'USD',
                                       'trxn_id'               => $trxn_id,
                                       'source'                => $source.' Mailer '.$i, 
                                       );
                
                require_once 'CRM/Contribute/BAO/Contribution.php';
                $contribution =& new CRM_Contribute_BAO_Contribution();
                $contribution->copyValues($contribParams);
                $contribution->save();
            }
        }
    }
  }

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
$config =& CRM_Core_Config::singleton();

CRM_Utils_System::authenticateScript(true);

require_once 'CRM/Core/Lock.php';
$lock = new CRM_Core_Lock('SampleContributionGenerator');

if ($lock->isAcquired()) {
    // try to unset any time limits
    if (!ini_get('safe_mode')) set_time_limit(0);

    SampleContributionGenerator::process( );
} else {
    throw new Exception('Could not acquire lock, another Sample Contribution Genrator process is running');
}

$lock->release();

echo "Sample Contributins are genarated successfully<p>";