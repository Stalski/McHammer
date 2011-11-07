<?php

/**
 * Renderer class for all Newsletter behavior.
 */
class panels_renderer_mchammer_newsletter extends panels_renderer_editor {

  public $mail_template_name = '';
  public $pane_groups = array();

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

      ctools_add_js('mchammer_newsletter_renderer', 'mchammer', 'plugins/display_renderers');
      ctools_add_css('mchammer_newsletter_renderer', 'mchammer', 'plugins/display_renderers');

      $this->clean_key = ctools_cleanstring($this->display->cache_key);

      // Create the links for the modal to re-render the panes.
      $setting = array('mchammer' => array());
      foreach ($this->display->content as $object) {
        $this->pane_groups[$object->configuration['source']] = str_replace(":" , "--", $object->configuration['source']);
      }
      $setting['mchammer'] = $this->pane_groups;
      drupal_add_js($setting, 'setting');
    }

  }

  /**
   * Implements panels_renderer_editor::render()
   */
  function render($pane_name = NULL) {
    $output = '';

    if (!isset($pane_name)) {
      $output = parent::render();
      $output = '<div id="panels-mchammer-display-' . $this->clean_key . '" class="panels-mchammer-display-container">' . $output . '</div>';
      $output .= '<div id="panels-mchammer-display-links">';
      foreach ($this->pane_groups as $name => $group) {
        $output .= ctools_modal_text_button(t('Rerender @name', array('@name' => $name)), 'mchammer/nojs/rerender/' . $this->mail_template_name . '/' . $name, t('Rerender'),  'ctools-modal-ctools-mchammer-style mchammer-style-' . $group);
      }
      $output .= '</div>';
    }
    else {
      // Rerender the panes with the same pane_name.
      foreach ($this->display->content as $pid => $pane) {
        $output .= $this->render_pane($pane);
      }

    }
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
    list($pane_type, $pane_name) = explode(":", $pane->configuration['source']);
    $class .= ' mchammer-process mchammer-' . $pane_type . '--' . $pane_name;

    $output = '<div class="' . $class . '" id="panel-pane-' . $pane->pid . '">';

    if (!$block->title) {
      $block->title = t('No title');
    }

    $output .= '<div class="grabber">';
    if ($buttons) {
      $output .= '<span class="buttons">' . $buttons . '</span>';
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

}