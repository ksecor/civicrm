{literal} 
<script type="text/javascript">
form = document.Zandigo;
setFields( );

// Called by onClick of searchFor radio
function showHideZ ( elem ) {
    // unset other radio field 
    var unsetFld = '';
    if ( elem.name == 'custom_89' ) {
        unsetFld = 'custom_90';
    }
    if ( elem.name == 'custom_90' ) {
        unsetFld = 'custom_89';
    }
    unselectRadio( unsetFld, form.name );
    setFields();
}

function setFields ( ) {
    var searchFor = '';
    
    // show and hide flds 
    var peopleFields = new Array( 'id-people-title','id-first-name','id-middle-name','id-last-name','id-gender','id-email' );
    var customFields = new Array( 'id-custom_91','id-custom_92','id-custom_93','id-custom_94','id-custom_95','id-custom_96','id-custom_97' );
    var orgFields    = new Array( 'id-org-title','id-org-name' );

    for( i=0; i < form.elements.length; i++) {
        if (form.elements[i].type == 'radio' && form.elements[i].checked == true) {
            // which radio button is checked
            searchFor = form.elements[i].value; 
            
            // is this a student or counselor (type 1), other person (type 2), or organization search
            var sType = searchType( searchFor );
             

            var hideRows = new Array();
            var showRows = new Array();
            switch (sType) 	{
                case 1 :
                    showRows = peopleFields.concat( customFields );
                    hideRows = orgFields;
                    break;
                case 2 :
                    showRows = peopleFields;
                    hideRows = customFields.concat( orgFields );
                    break;
                case 3 :
                    showRows = orgFields;
                    hideRows = peopleFields.concat( customFields );
                    break;
            }
            for( j=0; j < hideRows.length; j++ ){
                hide( hideRows[j], 'table-row' );
            }
            for( j=0; j < showRows.length; j++ ){
                show( showRows[j], 'table-row' );
            }
            return;
        }
    }
}

function searchType ( searchFor ) {
    var sTypes = new Array( null,
                            new Array( 'Student', 'Guidance Counselor' ),
                            new Array( 'Admissions Officer', 'Parent', 'Non Profit Director', 'College Access Director' ),
                            new Array( 'High School', 'College', 'Organization', 'College Access Program' ) );
    
    for ( i = 1; i < sTypes.length; i++ ) {
        for ( j = 0; j < sTypes[i].length; j++ ) {
            if ( sTypes[i][j] == searchFor ) {
                return i;
            }
        }
    }
}

</script>
{/literal}

