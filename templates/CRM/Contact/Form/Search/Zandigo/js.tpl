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
    for( i=0; i < form.elements.length; i++) {
        if (form.elements[i].type == 'radio' && form.elements[i].checked == true) {
            // which radio button is checked
            searchFor = form.elements[i].value; 
            
            // is this a student or counselor (type 1), other person (type 2), or organization search
            var sType = searchType( searchFor );
             
            // show and hide flds 
            var hideRows = new Array();
            var showRows = new Array();
            switch (sType) 	{
                case 1 :
                    showRows = ['id-people-title','id-first-name','id-middle-name','id-last-name','id-gender','id-email','id-custom_91','id-custom_92','id-custom_93','id-custom_94','id-custom_95','id-custom_96','id-custom_97'];
                    hideRows = ['id-org-title','id-org-name'];
                    break;
                case 2 :
                    showRows = ['id-people-title','id-first-name','id-middle-name' ,'id-last-name', 'id-gender', 'id-email'];
                    hideRows = ['id-org-title','id-org-name','id-custom_91','id-custom_92','id-custom_93','id-custom_94','id-custom_95','id-custom_96','id-custom_97'];
                    break;
                case 3 :
                    showRows = ['id-org-title','id-org-name'];
                    hideRows = ['id-people-title','id-first-name','id-middle-name' ,'id-last-name', 'id-gender', 'id-email','id-custom_91','id-custom_92','id-custom_93','id-custom_94','id-custom_95','id-custom_96','id-custom_97'];
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
    var sType1 = ['Student', 'Guidance Counselor'];
    for( i=0; i < sType1.length; i++) {
        if ( sType1[i] == searchFor ) {
            return 1;
        }
    }
    var sType2 = ['Admissions Officer', 'Parent', 'Non Profit Director', 'College Access Director'];
    for( i=0; i < sType2.length; i++) {
        if ( sType2[i] == searchFor ) {
            return 2;
        }
    }
    var sType3 = ['High School', 'College', 'Organization', 'College Access Program'];
    for( i=0; i < sType3.length; i++) {
        if ( sType3[i] == searchFor ) {
            return 3;
        }
    }

}

</script>
{/literal}

