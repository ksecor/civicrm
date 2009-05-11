<table id="pricelabel" style="display:none" class="form-layout">
<tr>  <td class="label"><label>Total Fee(s)</label> </td> <td id="pricevalue" class="view-value"></td> </tr>
</table>

<script type="text/javascript">
{literal}

var totalfee       = 0;
var thousandMarker = '{/literal}{$config->monetaryThousandSeparator}{literal}';
var seperator      = '{/literal}{$config->monetaryDecimalPoint}{literal}';
var symbol         = '{/literal}{$currencySymbol}{literal}';

var priceSet = price = Array( );
cj("input,#priceset select,#priceset").each(function () {

  if ( cj(this).attr('price') ) {
  switch( cj(this).attr('type') ) {
    
  case 'checkbox':
    
    //default calcution of element. 
    var option = cj(this).attr('price').split('_');
    ele        = option[0];
    addprice   = parseFloat( option[1] );    
    
    if( cj(this).attr('checked') ) {
      totalfee   += addprice;
      price[ele] += addprice;
    }

    //event driven calculation of element.
    cj(this).click( function(){
      
      if ( cj(this).attr('checked') )  {
	totalfee   += addprice;
	price[ele] += addprice;
      } else {
	totalfee   -= addprice;
	price[ele] -= addprice;
      }
      display( totalfee );
    });
    display( totalfee );
    break;
    
  case 'radio':

    //default calcution of element. 
    var option = cj(this).attr('price').split('-');
    ele        = option[0];
    addprice   = parseFloat( option[1] );   
    if ( ! price[ele] ) {
      price[ele] = 0;
    }
    
    if( cj(this).attr('checked') ) {
      totalfee   = parseFloat(totalfee) + addprice - parseFloat(price[ele]);
      price[ele] = addprice;
    }
    
    //event driven calculation of element.
    cj(this).click( function(){ 
      totalfee   = parseFloat(totalfee) + addprice - parseFloat(price[ele]);
      price[ele] = addprice;
      
      display( totalfee );
    });
    display( totalfee );
    break;
    
  case 'text':
    
    //default calcution of element. 
    var textval = parseFloat( cj(this).val() );
    if ( textval ) {
      var option  = cj(this).attr('price').split('_');
      ele         = option[0];
      if ( ! price[ele] ) {
       price[ele] = 0;
      }
      addprice    = parseFloat( option[1] );
      var curval  = textval * addprice;
      if ( textval >= 0 ) {
  	totalfee   = parseFloat(totalfee) + curval - parseFloat(price[ele]);
  	price[ele] = curval;
      }
    }
    
    //event driven calculation of element.
    cj(this).bind( 'keyup', function() { calculateText( this );
	  }).bind( 'blur' , function() { calculateText( this );   
    });
    display( totalfee );
    break;

  case 'select-one':
    
    //default calcution of element. 
    var ele = cj(this).attr('id');
      if ( ! price[ele] ) {
	price[ele] = 0;
      }
      eval( 'var selectedText = ' + cj(this).attr('price') );
      var addprice = parseFloat( cj(selectedText).attr( cj(this).val( ) ) );
    if ( addprice ) {
	totalfee   = parseFloat(totalfee) + addprice - parseFloat(price[ele]);
	price[ele] = addprice;
    }
    
    //event driven calculation of element.
    cj(this).change( function() {
      var ele = cj(this).attr('id');
      if ( ! price[ele] ) {
	price[ele] = 0;
      }
      eval( 'var selectedText = ' + cj(this).attr('price') );
      var addprice = parseFloat( cj(selectedText).attr( cj(this).val( ) ) );
      
      if ( addprice ) {
	totalfee   = parseFloat(totalfee) + addprice - parseFloat(price[ele]);
	price[ele] = addprice;
      } else {
	totalfee   = parseFloat(totalfee) - parseFloat(price[ele]);
	price[ele] = parseFloat('0');
      }
      display( totalfee );
    });
    display( totalfee );
    break;
    }
  }
});

//calculation for text box.
function calculateText( object ) {
  var option  = cj(object).attr('price').split('_');
  ele         = option[0];
  if ( ! price[ele] ) {
    price[ele] = 0;
  }
  addprice    = parseFloat( option[1] );
  var textval = parseFloat( cj(object).attr('value') );
  var curval  = textval * addprice;
    if ( textval >= 0 ) {
	totalfee   = parseFloat(totalfee) + curval - parseFloat(price[ele]);
	price[ele] = curval;
    } else {
	totalfee   = parseFloat(totalfee) - parseFloat(price[ele]);	
	price[ele] = parseFloat('0');
    }
  display( totalfee );  
}

//display calculated amount
function display( totalfee ) {
  if ( totalfee > 0 ) {
    document.getElementById('pricelabel').style.display = "block";
    var totalEventFee  = formatMoney( totalfee, 2, seperator, thousandMarker);
    document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalEventFee;
    scriptfee   = totalfee;
    scriptarray = price;
  } else{
    document.getElementById('pricelabel').style.display = "none";
  }
}

//money formatting/localization
function formatMoney (amount, c, d, t) {
var n = amount, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "," : d, 
    t = t == undefined ? "." : t, s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

{/literal}
</script>
