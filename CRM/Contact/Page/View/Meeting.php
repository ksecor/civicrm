    function edit( )
    {
        //set the path depending on open activity or activity history (view mode)
        $history = CRM_Utils_Request::retrieve( 'history', 'Boolean',
                                                $this ); 
        $context = CRM_Utils_Request::retrieve( 'context', 'String',$this );
        $this->assign('context', $context );

        $this->_id = CRM_Utils_Request::retrieve('id', 'Integer',
                                                 $this);
        $this->_caseID = CRM_Utils_Request::retrieve( 'caseid', 'Integer',
                                                      $this );
        $this->assign('caseid', $this->_caseID );
        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $edit = CRM_Utils_Request::retrieve( 'edit', 'Integer',$this );
       
         if($edit){
                
                  $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id=1&action=view&reset=1&selectedChild=activity&id=". $this->_id."&cid=". $this->_contactId."&history={$history}&subType=1&context=".$context . "");
         }
        if ( $context == 'Home' ) {
            if($edit){
                $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id=1&action=view&reset=1&selectedChild=activity&id=". $this->_id."&cid=". $this->_contactId."&history={$history}&subType=1&context=".$context);
            }else{
                $url = CRM_Utils_System::url('civicrm', 'reset=1' );
            }
        }else if ($context == 'case'){
            
             if($edit){
                $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id=1&action=view&reset=1&selectedChild=activity&id=". $this->_id."&cid=". $this->_contactId."&history={$history}&subType=1&context=".$context."&caseid=".$this->_caseID);
             }else{
             
                 $url = CRM_Utils_System::url('civicrm/contact/view/case',
                                              "show=1&action=view&reset=1&cid={$this->_contactId}&id={$this->_caseID}&selectedChild=case" );
             }
             
        } else {

            if($edit){
                
                $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id=1&action=view&reset=1&selectedChild=activity&id=". $this->_id."&cid=". $this->_contactId."&history={$history}&subType=1&context=activity");
            } else{ 
                $url = CRM_Utils_System::url('civicrm/contact/view',
                                             "show=1&action=browse&reset=1&history={$history}&cid={$this->_contactId}&selectedChild=activity" );
            }
        }      
        $session->pushUserContext( $url );
        
        if (CRM_Utils_Request::retrieve('confirmed', 'Boolean',
                                        CRM_Core_DAO::$_nullObject )){
            
                require_once 'CRM/Activity/BAO/Activity.php';
                
                CRM_Activity_BAO_Activity::del( $this->_id, 'Meeting');
            CRM_Utils_System::redirect($url);
        }
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_Meeting', ts('Contact Meetings'), $this->_action );
        $controller->reset( );
        $controller->setEmbedded( true );

        $controller->set( 'contactId', $this->_contactId );
        $controller->set( 'id'       , $this->_id );
        $controller->set( 'pid'      , $this->get( 'pid' ) );
        $controller->set( 'log'      , $this->get( 'log' ) );

        $controller->process( );
        $controller->run( );
    }

    /**
     * This function is the main function that is called when the page loads,
     * it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( )
    {
        $this->preProcess( );

        $pid = CRM_Utils_Request::retrieve( 'pid', 'Positive',
                                            $this ); 
        $log = CRM_Utils_Request::retrieve( 'log', 'Boolean',
                                            $this ); 
        
        if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::VIEW | CRM_Core_Action::DELETE) ) {
            $this->edit( );
        }

        return parent::run( );
    }
