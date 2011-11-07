// Ensure the $ alias is owned by jQuery.
(function($) {

DrupalPanelsMcHammer = DrupalPanelsMcHammer || {};

// Onload is enough for this s
$(function() {
  DrupalPanelsMcHammer.detectGroups($('.panels-mchammer-display-container'));
});


//
///**
// * Bind links that will open modals to the appropriate function.
// */
//Drupal.behaviors.McHammer = {
//  attach: function(context) {
//    DrupalPanelsMcHammer.detectGroups(context);
//  }
//};

DrupalPanelsMcHammer.panes = new Array();

DrupalPanelsMcHammer.detectGroups = function(container) {
  
  for (n in Drupal.settings.mchammer) {
    var className = 'mchammer-' + n.replace(":", "--");
    if ($('.' + className, container).hasClass('mchammer-process')) {
      var group = {
        id: className,
        panes: $('.' + className).removeClass('mchammer-process').addClass('mchammer-processed'),
        settings: Drupal.settings.mchammer[n]
      };
      DrupalPanelsMcHammer.panes.push(group);
    }
  }
  
  // Wrap all elements that belong together.
  for (n in DrupalPanelsMcHammer.panes) {
    $('.' + DrupalPanelsMcHammer.panes[n].id).wrapAll($('<div class="mchammer-wrapper" id="' +  DrupalPanelsMcHammer.panes[n].id + '"></div>'));
    $('#' +  DrupalPanelsMcHammer.panes[n].id).prepend(DrupalPanelsMcHammer.panes[n].settings);
  }
  
  Drupal.attachBehaviors(DrupalPanelsMcHammer.panes[n].settings);
  
};

})(jQuery);