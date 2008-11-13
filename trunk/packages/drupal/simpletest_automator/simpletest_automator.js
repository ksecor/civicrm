Drupal.behaviors.trapSimpleTestClick = function() {
  $('a[href]:not(.trappedSimpleTestClick)').each(function() {
    $(this).addClass('trappedSimpleTestClick').click(function() {
      var content = $(this).html();
      var elems = $('a[href]').filter(function() {
        return $(this).html() == content;
      });

      for (var i = 0; i < elems.length; i++) {
        if (elems[i] == this) break;
      }

      $('body').css('cursor', 'wait');
      jQuery.ajax({
        'async': false,
        'cache': false,
        'url': Drupal.settings.simpletestAutomator.clickTrapperURL,
        'data': { 'label': content, 'url': $(this).attr('href'), 'index': i },
        'timeout': 30,
        'error': function() { alert('The click could not be tracked.'); },
        'type': 'POST'
      });
    });
  });
};