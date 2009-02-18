<?php
class PCPBlock extends DrupalTestCase
{
    /*
     * Helper function to create a PCP Block for Contribution Page
     *
     * @param  int $contributionPageId - id of the Contribution Page
     * to be deleted
     * @return array of created pcp block
     *
     */
    function create( $contributionPageId ) {
        $profileParams = array(
                               'group_type' => 'Individual,Contact',
                               'title'      => 'Test Supprorter Profile',
                               'help_pre'   => 'Profle to PCP Contribution',
                               'is_active'  => 1,
                               'is_cms_user' => 2
                               );
        
        $ufGroup   = civicrm_uf_group_create ( $profileParams );
        $profileId = $ufGroup['id'];

        $fieldsParams = array (
                               array (
                                      'field_name'       => 'first_name',
                                      'field_type'       => 'Individual',
                                      'visibility'       => 'Public Pages and Listings',
                                      'weight'           => 1,
                                      'label'            => 'First Name',
                                      'is_required'      => 1,
                                      'is_active'        => 1 ),
                               array (
                                      'field_name'       => 'last_name',
                                      'field_type'       => 'Individual',
                                      'visibility'       => 'Public Pages and Listings',
                                      'weight'           => 2,
                                      'label'            => 'Last Name',
                                      'is_required'      => 1,
                                      'is_active'        => 1 ),
                               array (
                                      'field_name'       => 'email',
                                      'field_type'       => 'Contact',
                                      'visibility'       => 'Public Pages and Listings',
                                      'weight'           => 3,
                                      'label'            => 'Email',
                                      'is_required'      => 1,
                                      'is_active'        => 1 )
                               );
        
        foreach( $fieldsParams as $value ){
            $ufField   = civicrm_uf_field_create( $profileId , $value );
        }
        $joinParams =  array(
                             'module'       => 'Profile',
                             'entity_table' => 'civicrm_contribution_page',
                             'entity_id'    => 1,
                             'weight'       => 1,
                             'uf_group_id'  => $profileId ,
                             'is_active'    => 1
                             );
        require_once 'api/v2/UFJoin.php';
        $ufJoin = civicrm_uf_join_add( $joinParams );
        
        $params = array(
                        'entity_table'          => 'civicrm_contribution_page',
                        'entity_id'             => $contributionPageId,
                        'supporter_profile_id'  => $profileId,
                        'is_approval_needed'    => 0,
                        'is_tellfriend_enabled' => 0,
                        'tellfriend_limit'      => 0,
                        'link_text'             => 'Create your own Personal Campaign Page!',
                        'is_active'             => 1,
                        'notify_email'          => 'info@civicrm.org'
                        );
        require_once 'CRM/Contribute/BAO/PCP.php';
        $blockPCP = CRM_Contribute_BAO_PCP::add( $params);
        return array( 'blockId' => $blockPCP->id, 'profileId' => $profileId );
    }
    /*
     * Helper function to delete a PCP related stuff viz. Profile, PCP Block Entry
     *
     * @param  array key value pair
     * pcpBlockId - id of the PCP Block Id, profileID - id of Supporter Profile
     * to be deleted
     * @return boolean true if success, false otherwise
     *
     */
    function delete( $params )
    {
        require_once 'api/v2/UFGroup.php';
        $resulProfile = civicrm_uf_group_delete( $params['profileId'] );

        require_once 'CRM/Contribute/DAO/PCPBlock.php';
        $dao     =& new CRM_Contribute_DAO_PCPBlock( );
        $dao->id = $params['blockId'];
        if ( $dao->find( true ) ) {
            $resultBlock = $dao->delete( );
        }
        if ( $id = CRM_Utils_Array::value( 'pcpId', $params ) ){
            require_once 'CRM/Contribute/BAO/PCP.php';
            CRM_Contribute_BAO_PCP::delete( $id );
        }
        if ( $resulProfile && $resultBlock ) {
            return true;
        }
        return false;
    }
}
?>
