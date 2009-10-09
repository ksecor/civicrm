{literal}
<script type="text/javascript">
cj( function( ) {
	var id = count = 0; var columns='';

	cj('#options th').each( function(){ 
 
    /*Needed to implement for date sorting FIXME CRM-1744
    var option = cj(this).attr('id').split("_");
    if ( option.length > 1 ) {
        option = option[1];
        } else {
        option = option[0];
     }*/

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
		if( cj(this).text() ) {
			columns += ' null,';
		} else {
			columns += '{ "bSortable": false },';
		}
		break;
	 }
		count++; 
	});
	eval('columns =[' + columns + ']');
	cj('#options').dataTable({
		"aaSorting"    : [[ id, "asc" ]],
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
