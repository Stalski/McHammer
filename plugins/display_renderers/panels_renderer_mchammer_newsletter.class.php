<?php

/**
 * Renderer class for all Newsletter behavior.
 */
class panels_renderer_mchammer_newsletter extends panels_renderer_mchammer {

  public $mail_template_name = '';
  protected $source_panes = array();
  protected $source_display = NULL;

  /**
   * Prepare the attached display for rendering.
   *
   * This is the outermost prepare method. It calls several sub-methods as part
   * of the overall preparation process. This compartmentalization is intended
   * to ease the task of modifying renderer behavior in child classes.
   *
   * If you override this method, it is important that you either call this
   * method via parent::prepare(), or manually set $this->prep_run = TRUE.
   *
   * @param mixed $external_settings
   *  An optional parameter allowing external code to pass in additional
   *  settings for use in the preparation process. Not used in the default
   *  renderer, but included for interface consistency.
   */
  function prepare($external_settings = NULL) {

    parent::prepare();
    $this->prepare_source();

  }

  /**
   * Prepare the "revert to source" links for rendering.
   */
  function prepare_source() {

    ctools_include('content');

    $template = mchammer_mail_template_load($this->mail_template_name);
    $this->source_display = $template->display;
    foreach ($this->source_display->content as $pane) {

      $content_type = ctools_get_content_type($pane->type);

      // This is just used for the title bar of the pane, not the content itself.
      // If we know the content type, use the appropriate title for that type,
      // otherwise, set the title using the content itself.
      $title = ctools_content_admin_title($content_type, $pane->subtype, $pane->configuration, $this->display->context);
      if (!$title) {
        $title = t('Deleted/missing content type @type', array('@type' => $pane->type));
      }

      $this->source_panes_panels[$pane->panel][] = $pane->type . ':pane-' . $pane->pid;
      $this->source_panes[$pane->type . ':pane-' . $pane->pid] = $title;

    }

  }

  /**
   * Implements panels_renderer_editor::add_meta()
   */
  function add_meta() {

    parent::add_meta();

    if ($this->admin) {
      ctools_include('ajax');
      ctools_include('modal');
      ctools_include('cleanstring');
      ctools_modal_add_js();

      //ctools_add_js('mchammer_newsletter_renderer', 'mchammer', 'plugins/display_renderers');
      ctools_add_css('mchammer_newsletter_renderer', 'mchammer', 'plugins/display_renderers');

      $this->clean_key = ctools_cleanstring($this->display->cache_key);

      // Create the links for the modal to re-render the panes.
      $setting = array('mchammer' => array());
      foreach ($this->display->content as $object) {
        if (isset($object->configuration['source'])) {
          $this->pane_groups[$object->configuration['source']] = str_replace(":" , "--", $object->configuration['source']);
        }
      }
      $setting['mchammer'] = $this->pane_groups;
      drupal_add_js($setting, 'setting');
    }

  }

  /**
   * Implements panels_renderer_editor::render()
   */
  function render() {

    $output = parent::render();
    $output = '<div id="panels-mchammer-display-' . $this->clean_key . '" class="panels-mchammer-display-container">' . $output . '</div>';
    $output .= '<div id="panels-mchammer-display-links">';
    foreach ($this->source_panes as $source_pane) {
      $output .= ctools_modal_text_button(t('Rerender @name', array('@name' => $source_pane)), 'mchammer/nojs/rerender/' . $this->display->cache_key . '/' . $this->mail_template_name . '/' . $source_pane, t('Rerender'),  'ctools-modal-ctools-mchammer-style');
    }
    $output .= '</div>';

    return $output;

  }

  /**
   * Implements panels_renderer_editor::render_pane()
   */
  function render_pane(&$pane) {

    // Pass through to normal rendering if not in admin mode.
    if (!$this->admin) {
      return parent::render_pane($pane);
    }

    ctools_include('content');
    $content_type = ctools_get_content_type($pane->type);

    // This is just used for the title bar of the pane, not the content itself.
    // If we know the content type, use the appropriate title for that type,
    // otherwise, set the title using the content itself.
    $title = ctools_content_admin_title($content_type, $pane->subtype, $pane->configuration, $this->display->context);
    if (!$title) {
      $title = t('Deleted/missing content type @type', array('@type' => $pane->type));
    }

    $buttons = $this->get_pane_links($pane, $content_type);

    // Render administrative buttons for the pane.

    $block = new stdClass();
    if (empty($content_type)) {
      $block->title = '<em>' . t('Missing content type') . '</em>';
      $block->content = t('This pane\'s content type is either missing or has been deleted. This pane will not render.');
    }
    else {
      $block = ctools_content_admin_info($content_type, $pane->subtype, $pane->configuration, $this->display->context);
    }

    $output = '';
    $class = 'panel-pane';

    if (empty($pane->shown)) {
      $class .= ' hidden-pane';
    }

    if (isset($this->display->title_pane) && $this->display->title_pane == $pane->pid) {
      $class .= ' panel-pane-is-title';
    }

    // Add custom classes to trigger some contextual information with js & css style.
    if (isset($pane->configuration['source'])) {
      list($pane_type, $pane_name) = explode(":", $pane->configuration['source']);
      $class .= ' mchammer-process mchammer-' . $pane_type . '--' . $pane_name;
      if (isset($this->source_panes[$pane->configuration['source']])) {
        $title .= ' (source: ' . $this->source_panes[$pane->configuration['source']] . ')';
      }
    }

    $output = '<div class="' . $class . '" id="panel-pane-' . $pane->pid . '">';

    $output .= '<div class="grabber">';

    if ($buttons) {
      $output .= '<span class="buttons">' . $buttons . '</span>';
    }
    if (!$block->title) {
      $block->title = t('No title');
    }

    $output .= '<span class="text">' . $title . '</span>';
    $output .= '</div>'; // grabber

    $output .= '<div class="panel-pane-collapsible">';
    $output .= '<div class="pane-title">' . $block->title . '</div>';
    $output .= '<div class="pane-content">' . filter_xss_admin(render($block->content)) . '</div>';
    $output .= '</div>'; // panel-pane-collapsible

    $output .= '</div>'; // panel-pane

    return $output;
  }

  /**
   * Render all prepared regions, placing already-rendered panes into their
   * appropriate positions therein.
   *
   * @return array
   *   An array of rendered panel regions, keyed on the region name.
   */
  function render_regions() {

    $this->rendered['regions'] = array();

    // Loop through all panel regions, put all panes that belong to the current
    // region in an array, then render the region. Primarily this ensures that
    // the panes are arranged in the proper order.
    $content = array();
    foreach ($this->prepared['regions'] as $region_id => $conf) {
      $region_panes = array();
      foreach ($conf['pids'] as $pid) {
        // Only include panes for region rendering if they had some output.
        if (!empty($this->rendered['panes'][$pid])) {
          $region_panes[$pid] = $this->rendered['panes'][$pid];
        }
      }
      $this->rendered['regions'][$region_id] = $this->render_region($region_id, $region_panes);
    }

    return $this->rendered['regions'];
  }

}