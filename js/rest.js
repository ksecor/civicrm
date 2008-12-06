/*
eg. CiviREST (contact/search,array(...))
CiviREST(entitytag,add)

It really should be a class (or one class per entity, inherit from a common civicrmEntity ?
and it really really really should be a post for destructive actions (changes at the server level)

it also should use closure so we can properly interface the result of the call to the called object

    this.loadData = function() {
        var obj = this;
        $.getJSON( url, function( data ) {
            obj.gotData( data );  // instead of this.gotData( data );
        });
    }; 
*/

function civiREST (entity,action,params) {
  params ['q']="civicrm/"+entity+"/"+action;
  params ['json'] = 1;
  //$.post("/sites/all/modules/civicrm/extern/rest.php?json=1",{q:"civicrm/"+entity+"/"+action},
/*  $.get("/sites/all/modules/civicrm/extern/rest.php",params,
  function(data){
    alert("Data Loaded: " + data);
); 
  }*/
  jQuery('#restmsg').removeClass('msgok').removeClass('msgnok').html("");
  $.getJSON("/sites/all/modules/civicrm/extern/rest.php",params,function(result){
  if (result.is_error == 1) {
    jQuery('#restmsg').addClass('msgnok').html(result.error_message);
    return false;
  }
  jQuery('#restmsg').addClass('msgok').html('Saved');
  return true;
  });
}
