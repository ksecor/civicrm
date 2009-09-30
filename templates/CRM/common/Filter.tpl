{literal}
d = document;
stregexp = new RegExp;

function initFilter( id ) {
    //build the array
    filterArray = new Array();
    {/literal}
    {foreach from=$tokens item=tokenName key=tokenKey}
    filterArray["{$tokenKey}"] = "{$tokenName}";
    {/foreach}
    {literal}
    tempArray  = new Array();
    remvdArray = new Array();

    //get select object
    selObj = d.getElementById("token"+ id);

    //rebuild the list
    buildOptions(filterArray);

    //clear the input box
    d.getElementById("filter"+id).value = "";

    //clear the last typed value
    lastVal = "";
}

function filter( ob, id ) {
    str = ob.value;
    //if the length of str is 0, keep original array as option
    if ( str.length == 0 ) {
        buildOptions(filterArray);
        remvdArray.length = 0;
    } else {
        //clear tempArray
        tempArray.length = 0;

        //set up temporary array
        for ( i = 0; i < selObj.options.length; i++ ) {
            tempArray[selObj.options[i].value] = selObj.options[i].text;
        }
        //escape the special character
        str = str.replace(/([\\"'()\]\[])/g, "\\$1");

        //case-insensitive regexp
        stregexp = new RegExp( str, "i" );

        //remove appropriate item(s)
        if ( lastVal.length < str.length ) {
            for ( j = selObj.options.length-1; j > -1; j-- ) {
                if ( selObj.options[j].text.match( stregexp ) == null ) {
                    //delete unwanted option
                    delete tempArray[selObj.options[j].value];
                }
            }
        } else {
            //add appropriate item(s)
            //if a removed item matches the new pattern, add it to the list of names
            for ( key in remvdArray) {
                tempName = remvdArray[key].toString();
                if ( tempName.match(stregexp) != null ) {
                    tempArray[key] = tempName;
                }
            }

            //sort the names array
            tempArray.sort();
        }

        //build the new select list
        buildOptions(tempArray);
    }

    //remember the last value on which we narrowed
    lastVal = str;
}

function buildOptions( arrayName ) {
    //clear the select list
    selObj.options.length = 0;
    //to select only valid tokens in tokens list
    var tokenRegx = new RegExp (/{(\w+\.\w+)}/);
    var i = 0;
    for ( script in arrayName ) {
        if ( script.match(tokenRegx) != null ) {
             var option = new Option( arrayName[script], script );
             selObj.options[i] = option;
             i++;
        }
    }
    buildRemvd();
}

function buildRemvd( ) {
    //clear the removed items array
    remvdArray.length = 0;
    var remToken = null;
    //build the removed items array
    for ( key in filterArray ) {
        //for filtering tokens
        remToken =  filterArray[key].toString();
        if ( remToken.match(stregexp) == null ) {
            //remember which item was removed
            remvdArray[key] = filterArray[key];
        }
    }
}

function getMatches(id) {
    if ( selObj.options.length == 1 ) {
        d.getElementById("match"+id).innerHTML = "{/literal}{ts}1 match{/ts}{literal}";
    } else {
        d.getElementById("match"+id).innerHTML = selObj.options.length +"&nbsp;{/literal}{ts}matches{/ts}{literal}";
    }
}
{/literal}
