<?php

require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

class WGM_Form_Renderer extends HTML_QuickForm_Renderer_ArraySmarty {

  function _elementToArray(&$element, $required, $error) {
    $el = parent::_elementToArray($element, $required, $error);

    // add label html
    if ( isset($el['label']) and $el['label'] ) {
      $el['label_html'] = "<label for=\"$el[name]\">$el[label]</label>";
      $el['html_labelled'] = $el['label_html'] . $el['html'];
    }
    
    return $el;
  }
  
} // end WGM_Form_Renderer
