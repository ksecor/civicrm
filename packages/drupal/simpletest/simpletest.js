// $Id: simpletest.js,v 1.2 2007/09/18 15:30:06 rokZlender Exp $
/**
 * Creates a select all checkbox before in every test group fieldset
 */
$(document).ready(function() {
  $('.select_all').each(function() {
    var legend = $('> legend', this);
    var cbs =  $('fieldset :checkbox', this);
    var collapsed = 1;
    var selectAllChecked = 1;
    var cbInitialValue = "";

    for (i=0; i < cbs.length; i++) {
      if (!cbs[i].checked) {
        selectAllChecked = 0;
      }
      else {
        collapsed = 0;
      }
    }
    if (!collapsed && !selectAllChecked) 
      $('fieldset', this).removeClass('collapsed');

    var item = $('<div class="form-item"></div>').html('<label class="option"><input type="checkbox" id="'+legend.html()+'-selectall" /> Select all tests in this group</label>'+'<div class="description">Select all tests in group '+ legend.html() +'</div>');
    
    // finds all checkboxes in group fieldset and selects them or deselects
    item.find(':checkbox').attr('checked', selectAllChecked).click(function() {
      $(this).parents('fieldset:first').find('fieldset :checkbox').attr('checked', this.checked);
    }).end();
    
    // add select all checkbox
    legend.after(item);
  });
});