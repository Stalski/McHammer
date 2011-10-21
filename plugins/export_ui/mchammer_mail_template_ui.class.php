<?php

/**
 * @file
 * Ctools export UI for a McHammer Mail Template.
 */
class mchammer_mail_template_ui extends ctools_export_ui {

  function edit_form(&$form, &$form_state) {

    // Get the basic edit form
    parent::edit_form($form, $form_state);

    $form['category'] = array(
      '#type' => 'textfield',
      '#size' => 24,
      '#default_value' => $form_state['item']->category,
      '#title' => t('Category'),
      '#description' => t("The category that this newsletter template will be grouped into on the Add Content form. Only upper and lower-case alphanumeric characters are allowed."),
    );

    $form['title']['#title'] = t('Title');
    $form['title']['#description'] = t('The title for this newsletter template.');

  }

  /**
   * Step 2 of wizard: Choose a layout.
   */
  function edit_form_layout(&$form, &$form_state) {
    dsm($form_state);
    ctools_include('common', 'panels');
    ctools_include('display-layout', 'panels');
    ctools_include('plugins', 'panels');

    // @todo -- figure out where/how to deal with this.
    $form_state['allowed_layouts'] = 'mchammer_mail_template';

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
    dsm($form_state['display']);
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
   * Step 3 of wizard: Choose the content.
   */
  function edit_form_content(&$form, &$form_state) {

    ctools_include('ajax');
    ctools_include('plugins', 'panels');
    ctools_include('display-edit', 'panels');
    ctools_include('context');

    // If we are cloning an item, we MUST have this cached for this to work,
    // so make sure:
    if ($form_state['form type'] == 'clone' && empty($form_state['item']->export_ui_item_is_cached)) {
      $this->edit_cache_set($form_state['item'], 'clone');
    }

    //$cache = panels_edit_cache_get('panels_mini:' . $this->edit_cache_get_key($form_state['item'], $form_state['form type']));
    $cache = panels_edit_cache_get('mchammer:' . $this->edit_cache_get_key($form_state['item'], $form_state['form type']));

    $form_state['renderer'] = panels_get_renderer_handler('editor', $cache->display);
    $form_state['renderer']->cache = &$cache;

    $form_state['display'] = &$cache->display;
    $form_state['content_types'] = $cache->content_types;
    // Tell the Panels form not to display buttons.
    $form_state['no buttons'] = TRUE;
    $form_state['display_title'] = !empty($cache->display_title);

    $form = panels_edit_display_form($form, $form_state);

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

}