<?php

require_once 'Structures/DataGrid.php';
require_once 'Structures/DataGrid/Column.php';
require_once 'Structures/DataGrid/Record.php';
require_once 'HTML/Table.php';

class CRM_Paging  
{
    function __construct()
    {
    }
    // this function is used for drawing the page numbers
    function f_Paging( $obj_users_paging ) 
    {
        $dg_paging =& new Structures_DataGrid ( 3 );
        $dg_paging->bind ( $obj_users_paging );
        $a_details[0] =  $dg_paging->renderer->getPaging( $mode = 'Sliding', $separator = '|', $prev = '<< Move Previous', $next = 'Move Next >>', $delta = 3 );
       
        $a_details[1] = $_current_pagevalue = ( int ) $dg_paging->getCurrentPage( );
        return  $a_details;
    }
    
    // this function is used for displaying the records
    function f_Rendering( $obj_users_rendering ) 
    {
        /*$dg_rendering =& new Structures_DataGrid ( );
        $dg_renderHTMLtable =& new Structures_DataGrid_Renderer_HTMLTable ( &$dg_rendering );
        $dg_rendering->bind ( $obj_users_rendering );
        $dg_rendering->renderer->render( );*/
        
        $dg_rendering_record = new Structures_DataGrid_DataSource_DataObject();
        $dg_rendering_record->bind($obj_users_rendering);
        
        $render_arr = array();
        
        $render_arr = $dg_rendering_record->fetch(0,null,null,'ASC');
        return $render_arr;
        // print_r($render_arr);
    }
    
}

?>
