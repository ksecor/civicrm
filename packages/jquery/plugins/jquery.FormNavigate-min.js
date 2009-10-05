/**
 * jQuery.FormNavigate.js
 * jQuery Form onChange Navigate Confirmation plugin
 *
 * Copyright (c) 2009 Law Ding Yong
 * 
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * See the file license.txt for copying permission.
 */
var global_formNavigate = true; 
(function($){ $.fn.FormNavigate = function(message) { window.onbeforeunload = confirmExit; function confirmExit( event ) { if (global_formNavigate == true) {  event.cancelBubble = true;  }  else  { return message; } };$(this+ ":input[type=text], :input[type='textarea'], :input[type='password'], :input[type='radio'], :input[type='checkbox'], :input[type='file'], select").change(function(){ global_formNavigate = false; }); $(this+ ":input[type='textarea']").keyup(function(){ global_formNavigate = false; }); $(this+ ":submit").click(function(){ global_formNavigate = true; }); }; })(jQuery);