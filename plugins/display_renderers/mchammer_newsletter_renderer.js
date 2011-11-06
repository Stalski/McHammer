// Ensure the $ alias is owned by jQuery.
(function($) {

// Onload is enough for this s
$(function() {
  var paneGrouper = new DrupalPanelsMcHammer();
  paneGrouper.bindAutoPaneGrouping();
});

/**
 * Base object (class) definition for McHammer Editor.
 */
function DrupalPanelsMcHammer() {
  
  var mchammer = this;

  this.panes = new Array();
  
  this.container = $('.panels-mchammer-display-container');
  
  /**
   * Binds the panes together in pane-groups.
   */
  this.bindAutoPaneGrouping = function() {
    
    //mchammer-views-pane-17
    for (n in Drupal.settings.mchammer) {
      var className = 'mchammer-' + n.replace(":", "-");
      var group = {
        id: className, 
        panes: $('.' + className).removeClass('mchammer-process').addClass('mchammer-processed'),
        settings: Drupal.settings.mchammer[n]
      };
      mchammer.panes.push(group);
    }
    
    // Wrap all elements that belong together.
    for (n in mchammer.panes) {
      $('.' + mchammer.panes[n].id).wrapAll($('<div id="' +  mchammer.panes[n].id + '"></div>'));
      $('#' +  mchammer.panes[n].id).prepend(mchammer.panes[n].settings);
    }
    
    Drupal.attachBehaviors(this.container);
    
  };
  
};

})(jQuery);