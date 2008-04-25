{* Custom Data form*}
{include file="CRM/Contact/Form/CustomData.tpl" mainEdit=$mainEditForm}

<script type="text/javascript">  
    var showBlocks = new Array({$showBlocks1});  
    var hideBlocks = new Array({$hideBlocks1});  
  
{* hide and display the appropriate blocks as directed by the php code *}  
    on_load_init_blocks( showBlocks, hideBlocks );  
 </script>  
