// Ensure the $ alias is owned by jQuery.
(function($) {


/**
 * Bind links that will open modals to the appropriate function.
 */
Drupal.behaviors.McHammer = {
  attach: function (context, settings) {
    $('.panels-mchammer-display-container', context).once('mchammer-display-container', function() {
      if (DrupalPanelsMcHammer.panel == '') {
        DrupalPanelsMcHammer.detectGroups($('.panels-mchammer-display-container'));
      }
      DrupalPanelsMcHammer.wrap(context);
    });
  }
};

/**
 * McHammer Panels Renderer object.
 */
var DrupalPanelsMcHammer = {};

/**
 * Panel wrapper.
 */
DrupalPanelsMcHammer.panel = '';

/**
 * The grouped panes.
 */
DrupalPanelsMcHammer.panes = new Array();

/**
 * Detects the pane-groups while nesting the panes in "panes". 
 */
DrupalPanelsMcHammer.detectGroups = function(container) {
  
  DrupalPanelsMcHammer.panel = container;
  for (n in Drupal.settings.mchammer) {
    var key = n.replace(":", "--");
    var className = 'mchammer-' + key;
    if ($('.' + className, DrupalPanelsMcHammer.panel).hasClass('mchammer-process')) {
      var paneGroup = {
        key: key,
        id: className,
        panes: $('.' + className),
        link: $('.mchammer-style-' + key)
      };
      DrupalPanelsMcHammer.panes.push(paneGroup);
    }
  }
};

/**
 * Wraps and adds the link to the pane-groups.
 */
DrupalPanelsMcHammer.wrap = function(container) {
  // Wrap all elements that belong together.
  $.each(DrupalPanelsMcHammer.panes, function(i, paneGroup) {
    var panes = $('.' + paneGroup.id, container);
    if (panes.hasClass('mchammer-process')) {
      panes.wrapAll($('<div class="mchammer-wrapper" id="' +  paneGroup.id + '"></div>'));
      $('#' +  paneGroup.id, container).before(paneGroup.link);
      panes.removeClass('mchammer-process').addClass('mchammer-processed');
    }
  });
  
};

})(jQuery);