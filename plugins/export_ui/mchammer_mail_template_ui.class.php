<?php

/**
 * @file
 * Ctools export UI for a McHammer Mail Template.
 */
class mchammer_mail_template_ui extends ctools_export_ui {

  /**
   * Figure out what the cache key is for this object.
   * We prefix with namespace: to filter the export key later.
   */
  function edit_cache_get_key($item, $op) {
    $export_key = $this->plugin['export']['key'];
    return $op == 'edit' ? 'template:' . $item->{$this->plugin['export']['key']} : "template:::$op";
  }

  /**
   * Implements list_build_row().
   */
  function list_build_row($item, &$form_state, $operations) {

    $operations['preview'] = array(
      'href' => 'mchammer/' . $item->name,
      'title' => t('Preview'),
    );

    //admin/structure/mchammer/newsletter/add/basic/newsletter_example_1
    $newsletter_plugin = ctools_get_export_ui('newsletter.export_ui');
    $operations['create_newsletter'] = array(
     'href' => 'admin/structure/mchammer/newsletter/add/' . $item->name . '/create-newsletter',
     // 'href' => ctools_export_ui_plugin_menu_path($newsletter_plugin, 'add/basic/%ctools_export_ui', $item->name),
     'title' => t('Create newsletter'),
    );
    parent::list_build_row($item, $form_state, $operations);
  }

  /**
   * Implements edit_form().
   */
  function edit_form(&$form, &$form_state) {

    // Get the basic edit form
    parent::edit_form($form, $form_state);

    $form['title']['#title'] = t('Title');
    $form['title']['#description'] = t('The title for this newsletter template.');

  }

  /**
   * Validate submission of the mail template edit form.
   */
  function edit_form_basic_validate($form, &$form_state) {
    parent::edit_form_validate($form, $form_state);
    // 'add' is a pre-reserved machine name
    if ($form_state['values']['name'] == 'add') {
      form_set_error('name', t("'add' can't be used as a title"));
    }
  }

  /**
   * Step 2 of wizard: Choose a layout.
   */
  function edit_form_layout(&$form, &$form_state) {

    ctools_include('common', 'panels');
    ctools_include('display-layout', 'panels');
    ctools_include('plugins', 'panels');

    // Trigger the module restriction for the allowed layouts.
    $available_layouts = panels_get_layouts();
    $allowed_layouts = new stdClass();
    $allowed_layouts->allowed_layout_settings = array();
    foreach ($available_layouts as $name => $layout) {
      $allowed_layouts->allowed_layout_settings[$name] = $layout['module'] == 'mchammer';
    }

    $form_state['allowed_layouts'] = $allowed_layouts;

    // Make sure there is a display to work with.
    if ($form_state['op'] == 'add' && empty($form_state['item']->display)) {
      $form_state['item']->display = panels_new_display();
    }

    $form_state['display'] = &$form_state['item']->display;

    // Tell the Panels form not to display buttons.
    $form_state['no buttons'] = TRUE;

    // Change the #id of the form so the CSS applies properly.
    $form['#id'] = 'panels-choose-layout';
    $form = panels_choose_layout($form, $form_state);

    if ($form_state['op'] == 'edit') {
      $form['buttons']['next']['#value'] = t('Change');
    }

  }

  /**
   * Validate that a layout was chosen.
   */
  function edit_form_layout_validate(&$form, &$form_state) {
    $display = &$form_state['display'];
    if (empty($form_state['values']['layout'])) {
      form_error($form['layout'], t('You must select a layout.'));
    }
    if ($form_state['op'] == 'edit') {
      if ($form_state['values']['layout'] == $display->layout) {
        form_error($form['layout'], t('You must select a different layout if you wish to change layouts.'));
      }
    }
  }

  /**
   * A layout has been selected, set it up.
   */
  function edit_form_layout_submit(&$form, &$form_state) {

    $display = &$form_state['display'];
    if ($form_state['op'] == 'edit') {
      if ($form_state['values']['layout'] != $display->layout) {
        $form_state['item']->temp_layout = $form_state['values']['layout'];
        $form_state['clicked_button']['#next'] = 'move';
      }
    }
    else {
      $form_state['item']->display->layout = $form_state['values']['layout'];
    }

  }

  /**
   * When a layout is changed, the user is given the opportunity to move content.
   */
  function edit_form_move(&$form, &$form_state) {

    $form_state['display'] = &$form_state['item']->display;
    $form_state['layout'] = $form_state['item']->temp_layout;

    ctools_include('common', 'panels');
    ctools_include('display-layout', 'panels');
    ctools_include('plugins', 'panels');

    // Tell the Panels form not to display buttons.
    $form_state['no buttons'] = TRUE;

    // Change the #id of the form so the CSS applies properly.
    $form = panels_change_layout($form, $form_state);

    // This form is outside the normal wizard list, so we need to specify the
    // previous/next forms.
    $form['buttons']['previous']['#next'] = 'layout';
    $form['buttons']['next']['#next'] = 'content';

  }

  /**
   * Save the changed selection of layout.
   */
  function edit_form_move_submit(&$form, &$form_state) {
    panels_change_layout_submit($form, $form_state);
  }

  /**
   * Step 3 of wizard: Choose the content.
   */
  function edit_form_content(&$form, &$form_state) {

    ctools_include('ajax');
    ctools_include('plugins', 'panels');
    ctools_include('display-edit', 'panels');

    // If we are cloning an item, we MUST have this cached for this to work,
    // so make sure:
    if ($form_state['form type'] == 'clone' && empty($form_state['item']->export_ui_item_is_cached)) {
      $this->edit_cache_set($form_state['item'], 'clone');
    }

    $cache_key = $this->edit_cache_get_key($form_state['item'], $form_state['form type']);
    $cache = panels_edit_cache_get('mchammer:' . $cache_key);

    $form_state['renderer'] = panels_get_renderer_handler('mchammer', $cache->display);
    $form_state['renderer']->cache = &$cache;

    $form_state['display'] = &$cache->display;
    $form_state['content_types'] = $cache->content_types;

    // Tell the Panels form not to display buttons.
    $form_state['no buttons'] = TRUE;
    $form_state['display_title'] = FALSE;
    $form_state['no preview'] = TRUE;

    $form = panels_edit_display_form($form, $form_state);

    // Remove the update button
    if (isset($form['buttons']['next'])) {
      unset($form['buttons']['next']);
    }

    $id = $form_state['form type'] == 'add' ? 'new' : $form_state['item']->name;

    $form['buttons']['preview'] = array(
      '#markup' => l(t('Preview'), 'mchammer/template/' . $id, array('attributes' => array('class' => 'button', 'target' => '_blank'))),
      '#id' => 'panels-preview-button',
    );

    // Make sure the theme will work since our form id is different.
    $form['#theme'] = 'panels_edit_display_form';

  }

  /**
   * Save the display.
   */
  function edit_form_content_submit(&$form, &$form_state) {
    panels_edit_display_form_submit($form, $form_state);
    $form_state['item']->display = $form_state['display'];
  }

  /**
   * Create the newsletter display from a given display name.
   * @param $mailtemplate_name Machine name from the panel display to use.
   * @return A new generated panel display.
   */
  function create_newsletter($mailtemplate_name) {

    ctools_include('content');

    // Load the original mail template
    $template = mchammer_mail_template_load($mailtemplate_name);

    $display = panels_new_display();
    $display->layout = $template->display->layout;

    // Construct the panes for the new display
    foreach ($template->display->panels as $region) {

      foreach ($region as $pid) {

        $pane = $template->display->content[$pid];

        $extractor = McHammerExtractorFactory::getExtractor($pane->type, $template->display);
        $extractor->setSourcePane($pane);
        $extractor->extract($display);

      }

      }

      return $display;

    }

  /**
   * Revert the newsletter display his panes from a given source.
   * @param $mailtemplate_name Machine name from the panel display to revert to.
   * @param $display_cache_key Panels cache key from newsletter display
   * @param $source_key Key from the source panes to be reverted.
   * @return The reverted display.
   */
  function revert_newsletter_pane($mailtemplate_name, $display_cache_key, $source_key) {

    ctools_include('content');

    // Load the current display from cache
    $cache = panels_edit_cache_get($display_cache_key);
    $display = $cache->display;

    // Load the original mail template
    $template = mchammer_mail_template_load($mailtemplate_name);

    // Remove the panes from the source to revert.
    foreach ($display->content as $key => $content) {
      if ($content->configuration['source'] == $source_key) {
        $pane = $display->content[$key];
        unset($display->panels[$pane->panel][$key]);
        unset($display->content[$key]);
      }
    }

    list(, $pane_key) = explode(':', $source_key);
    list(, $pid) = explode('-', $pane_key);

    $pane = $template->display->content[$pid];

    $extractor = McHammerExtractorFactory::getExtractor($pane->type, $template->display);
    $extractor->setSourcePane($pane);
    $extractor->extract($display);

    $cache->display = $display;

    panels_edit_cache_set($cache);

    return $display;

  }

}