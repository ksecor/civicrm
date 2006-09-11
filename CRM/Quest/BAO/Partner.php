<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/** 
 *  this file contains functions for Partners
 */


require_once 'CRM/Quest/DAO/Partner.php';

class CRM_Quest_BAO_Partner extends CRM_Quest_DAO_Partner {

    
    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * function to get all parnters
     *
     */
    function getPartners( $type = 'College')
    {
        $partners = array();
        $dao = &new CRM_Quest_DAO_Partner();
        if ( $type != 'All' ) {
            $dao->partner_type =  $type ;
        }
        $dao->orderBy('weight');
        $dao->find();
        while( $dao->fetch() ) {
            $partners[$dao->id] = $dao->name;
        }

        return $partners;
    }
    
     /**
     * function to add/update partner Information
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function &createRelative(&$relativeParams, &$ids) {
        $dao = & new CRM_Quest_DAO_PartnerRelative();
        $dao->copyValues($relativeParams);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $dao->save();
        
        return $dao;
    }

    static function &getPartnersForContact( $cid, $is_supplement = null ) {
        $query = "
SELECT p.name as name
FROM   quest_partner p,
       quest_partner_ranking r
WHERE  r.contact_id  = $cid
  AND  r.partner_id  = p.id
  AND  ( r.ranking     >= 1 OR
         r.is_forward  = 1 )
";

        if ( $is_supplement !== null ) {
            $query .= " AND p.is_supplement = $is_supplement";
        }

        $partners = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $partners[$dao->name] = 1;
        }
        return $partners;
    }

    static function &getPartnersDetails ($cid , &$details) {
        $partnersLint  = self::getPartnersForContact( $cid , true);
       
        self::partner_amherst($cid ,&$details );
        self::partner_bowdoin($cid ,&$details );
        self::partner_columbia($cid ,$details );
//         self::partner_pomona($cid ,$details);
//         self::partner_princeton($cid ,$details);
//         self::partner_rice($cid ,$details);
//         self::partner_stanford($cid ,$details);
//         self::partner_wellesley($cid ,$details);
//         self::partner_wheaton($cid ,$details);
        
        print_r($details);
        
    }

    static function partner_amherst($cid ,&$details ) {
        require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
        $dao->contact_id = $cid;
        $fields =
            array(
                  'publication'       => array( 'Amherst Publication'           , 'Publication Name'        ),
                  'representative'    => array( 'Amherst Representative'        , 'Representative Name'     ),
                  'campus_visit'      => array( 'Campus Visit'                  , 'Whom did you Meet?'      ),
                  'college_counselor' => array( 'College Counselor'             , 'Counselor Name'          ),
                  'website'           => array( 'Amherst College Website'       , 'Site URL'                ),
                  'guidebook'         => array( 'Guide Books/Magazines/Websites', 'Name(s)'                 ),
                  'siblings'          => array( 'Siblings, parents, or grandparents who attended', 'Name(s)'),
                  'quest'             => array( 'QuestBridge'			, 'Specify how'),
                  'other'             => array( 'Other'                         , 'Name(s)'                 ),
                  );

        $partnerDatails["Athletics Supplement"] = array();
        
        if ( $dao->find( true ) ) {
            foreach ( $fields as $name => $titles ) {
                $cond = "is_{$name}";
                if ( $dao->$cond ) {
                    $partnerDatails["Applicant Information"][$cond] = 1;
                }
                $partnerDatails["Applicant Information"][$name] = $dao->$name;
            }

            $partnerDatails["Athletics Supplement"]["height"] = $dao->height;
            $partnerDatails["Athletics Supplement"]["weight"] = $dao->weight;
            
        }
        //Essay Information 
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_amherst_essay', $cid, $cid );
        $partnerDatails["Essay"]['essay'] = array();
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDatails["Essay"]['essay'] );

        //Athletics Supplement
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_amherst_athletic', $cid, $cid );
        
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDatails["Athletics Supplement"] );

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::setDefaults( $cid, 'Amherst', $partnerDatails["Athletics Supplement"] );
        
        $details["Amherst College"] = $partnerDatails;

    }
    
    static function partner_bowdoin($cid ,&$details ) {
        
        //Applicant Information
        $partnerBowdoin["Applicant Information"] = array();
        require_once 'CRM/Quest/Partner/DAO/Bowdoin.php';
        $dao =& new CRM_Quest_Partner_DAO_Bowdoin( );
        $dao->contact_id = $cid;
        if ( $dao->find( true ) ) {
            $partnerBowdoin["Applicant Information"]['learn'] = $dao->learn;
            $partnerBowdoin["Athletics Supplement"]['height'] = $dao->height;
            $partnerBowdoin["Athletics Supplement"]['weight'] = $dao->weight;
        }
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_bowdoin_applicant', $cid, $cid );
        
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerBowdoin["Applicant Information"] );


        //Athletics Supplement
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_bowdoin_athletic', $cid, $cid );
        
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerBowdoin["Athletics Supplement"] );

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::setDefaults( $cid, 'Bowdoin', $partnerBowdoin["Athletics Supplement"] );


        
        $details["Bowdoin College"] = $partnerBowdoin;
    }

    static function partner_columbia($cid ,&$details ) {

        //Applicant Information
        $partnerDatails["Applicant Information"] = array();
        
        require_once 'CRM/Quest/Partner/DAO/Columbia.php';
        $dao =& new CRM_Quest_Partner_DAO_Columbia( );
        $dao->contact_id = $cid;
        if ( $dao->find( true ) ) {
            $dao->storeValues($dao, $partnerDatails["Applicant Information"]);
        }

        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_columbia_applicant', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDatails["Applicant Information"] );
        
        $fields = array( 'career' => 'columbia_career', 'interest' => 'columbia_interest');
        
        $names = array('career'                => array( 'newName' => 'columbia_career',
                                                         'groupName' => 'columbia_career' ),
                       'interest'              => array( 'newName' => 'columbia_interest',
                                                         'groupName' => 'columbia_career' ),
                       );
        
        CRM_Core_OptionGroup::lookupValues( $partnerDatails["Applicant Information"], $names, false);
        
        //Interest

        $partnerDatails["Interests"] = array();
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_columbia_interest', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDatails["Interests"] );

        //Personal Essay
        $partnerDatails["Personal Essay"] =array();
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_columbia_personal', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDatails["Personal Essay"] );
        
        $details["Columbia University"] = $partnerDatails;
        
    }

}
    
?>