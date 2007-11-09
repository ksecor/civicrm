<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id: Status.php 11513 2007-09-18 09:31:05Z lobo $
 *
 */

require_once 'CRM/Contribute/Form/Task.php';

/**
 * This class provides the functionality to email a group of
 * contacts. 
 */
class CRM_Contribute_Form_Task_Status extends CRM_Contribute_Form_Task {

    /**
     * Are we operating in "single mode", i.e. updating the task of only
     * one specific contribution?
     *
     * @var boolean
     */
    public $_single = false;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    
    function preProcess( ) {
        $id = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                           $this, false );

        if ( $id ) {
            $this->_contributionIds    = array( $id );
            $this->_contributionClause =
                " civicrm_contribution.id IN ( $id ) ";
            $this->_single             = true;
            $this->assign( 'totalSelectedContributions', 1 );
        } else {
            parent::preProcess( );
        }

        // check that all the contribution ids have pending status
        $query = "
SELECT count(*)
FROM   civicrm_contribution
WHERE  contribution_status_id != 2
AND    {$this->_contributionClause}";
        $count = CRM_Core_DAO::singleValueQuery( $query,
                                                 CRM_Core_DAO::$_nullArray );
        if ( $count != 0 ) {
            CRM_Core_Error::fatal( 'You can only select contributions in pending status' );
        }

        // we have all the contribution ids, so now we get the contact ids
        parent::setContactIDs( );
        $this->assign( 'single', $this->_single );
    }
    
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    public function buildQuickForm()
    {
        $status = CRM_Contribute_PseudoConstant::contributionStatus( );
        unset( $status[2] );
        unset( $status[5] );
        $this->add('select', 'contribution_status_id',
                   ts('Contribution Status'), 
                   $status,
                   true );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Update Pending Status'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => ts('Done') ),
                                 )
                           );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $params = $this->controller->exportValues( $this->_name );
        $statusID = $params['contribution_status_id'];

        require_once 'CRM/Core/Payment/BaseIPN.php';
        $baseIPN = new CRM_Core_Payment_BaseIPN( );

        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        // for each contribution id, we just call the baseIPN stuff 
        for ( $i = 0; $i < count( $this->_contributionIds ); $i++ ) {
            $input = $ids = $objects = array( );

            $input['component'] = 'contribute';
            
            $ids['contact'     ] = $this->_contactIds[$i];
            $ids['contribution'] = $this->_contributionIds[$i];

            $ids['event'] = $ids['participant'] = $ids['membership'] = null;
            $ids['contributionRecur'] = $ids['contributionPage'] = null;

            if ( ! $baseIPN->validateData( $input, $ids, $objects ) ) {
                CRM_Core_Error::fatal( );
            }

            $contribution =& $objects['contribution'];

            if ( $statusID == 3 ) {
                $baseIPN->cancelled( $objects, $transaction );
                $transaction->commit( );
                return;
            } else if ( $statusID == 4 ) {
                $baseIPN->failed( $objects, $transaction );
                $transaction->commit( );
                return;
            }

            // status is not pending
            if ( $contribution->contribution_status_id != 2 ) {
                $transaction->commit( );
                return;
            }

            // set some fake input values so we can reuse IPN code
            $input['amount']     = $contribution->total_amount;
            $input['is_test']    = $contribution->is_test;
            $input['fee_amount'] = $contribution->fee_amount;
            $input['net_amount'] = $contribution->net_amount;
            $input['trxn_id']    = $contribution->invoice_id;

            $baseIPN->completeTransaction( $input, $ids, $objects, $transaction, false );
        }
    }

}

?>
