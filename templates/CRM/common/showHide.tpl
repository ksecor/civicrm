{* This included tpl hides and displays the appropriate blocks as directed by the php code which assigns showBlocks and hideBlocks arrays. *}
 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

    on_load_init_blocks( showBlocks, hideBlocks{if $elemType EQ 'table-row'}, 'table-row'{/if} );
 </script>
