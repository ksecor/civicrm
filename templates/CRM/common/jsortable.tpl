{literal}
<script type="text/javascript">
var tableId = '';
cj('table.display').each(function(){
    tableId += this.id.substring(6) + ','; 
});
tableId = tableId.substring(0, tableId.length - 1 );
if(!tableId) tableId = 0;
eval('tableId =[' + tableId + ']');

cj( function( ) {
  cj.each(tableId, function(i,n){
    tabId = (!n) ? '#options' : '#option' + n;
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
