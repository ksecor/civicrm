{literal}
<script type="text/javascript">
cj( function( ) {
var tableId = '';
var count   = 1;

//rename id of table with sequence
//and create the object for navigation
cj('table.display').each(function(){
    cj(this).attr('id','option' + count);
    tableId += count + ',';
    count++; 
});

//remove last comma
tableId = tableId.substring(0, tableId.length - 1 );
eval('tableId =[' + tableId + ']');

  cj.each(tableId, function(i,n){
    tabId = '#option' + n;
    var id = -1; var count = 0; var columns='';
    //build columns array for sorting or not sorting
    cj(tabId + ' th').each( function( ) {
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
	columns = columns.substring(0, columns.length - 1 );
	eval('columns =[' + columns + ']');

    //build default sorting
    var sortColumn = '';
	if ( id >= 0 ) {
	    sortColumn = '[ id, "asc" ]';
	}

	eval('sortColumn =[' + sortColumn + ']');
    	cj(tabId).dataTable({
            "aaSorting"    : sortColumn,
            "bPaginate"    : false,
            "bLengthChange": true,
            "bFilter"      : false,
            "bInfo"        : false,
            "bAutoWidth"   : false,
            "aoColumns"    : columns
    	});        
    });
});
</script>
{/literal}
