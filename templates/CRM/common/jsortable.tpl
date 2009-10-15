{literal}
<script type="text/javascript">
cj( function( ) {
    var id = -1; var count = 0; var columns='';
    //build columns array for sorting or not sorting
    cj('#options th').each( function( ) { 
        switch( cj(this).attr('id') ) { 
            case 'sortable':
                id = count; 
                columns += ' null,';
            break;
            case 'date':
                columns += '{ "sType": \'date\', "fnRender": function (oObj) { return oObj.aData[' + count + ']; },"bUseRendered": false},';
            break;
            case 'nosort':           
                columns += '{ "bSortable": false },';
            break;
            default:
                if ( cj(this).text() ) {
                    columns += ' null,';
                } else {
                    columns += '{ "bSortable": false },';
                }
            break;
        }
        count++; 
	});
	
	eval('columns =[' + columns + ']');

    //build default sorting
    var sortColumn = '';
    console.log(id);
	if ( id >= 0 ) {
	    sortColumn = '[ id, "asc" ]';
	}
	console.log( sortColumn );
	eval('sortColumn =[' + sortColumn + ']');

	cj('#options').dataTable({
		"aaSorting"    : sortColumn,
		"bPaginate"    : false,
		"bLengthChange": true,
		"bFilter"      : false,
		"bInfo"        : false,
		"bAutoWidth"   : false,
		"aoColumns"    : columns
	});        
});
</script>
{/literal}
