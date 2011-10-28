<?php

/**
 * @file
 * Ctools export UI for a McHammer Newsletter.
 */
class mchammer_newsletter_ui extends ctools_export_ui {

  /**
   * Figure out what the cache key is for this object.
   */
  function edit_cache_get_key($item, $op) {
    $export_key = $this->plugin['export']['key'];
    return $op == 'edit' ? 'newsletter:' . $item->{$this->plugin['export']['key']} : "newsletter:::$op";
  }

  function list_build_row($item, &$form_state, $operations) {
    $operations['view'] = array(
      'href' => 'mchammer/newsletter/' . $item->name,
      'title' => t('View'),
    );
    parent::list_build_row($item, $form_state, $operations);
  }

  function edit_form(&$form, &$form_state) {

    ctools_include('export');
    $options = array(0 => t('None'));
    foreach (ctools_export_load_object('mchammer_mail_templates', 'all') as $name => $option) {
      $options[$name] = $option->admin_title;
    }

    if (empty($options)) {
      return;
    }

    // Get the basic edit form
    parent::edit_form($form, $form_state);

    $form['category'] = array(
      '#type' => 'textfield',
      '#size' => 24,
      '#default_value' => $form_state['item']->category,
      '#title' => t('Category'),
      '#description' => t("The category that this newsletter template will be grouped into on the Add Content form. Only upper and lower-case alphanumeric characters are allowed."),
    );

    $form['mail_template_name'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $form_state['item']->mail_template_name,
      '#title' => t('Mail template'),
      '#description' => t('Mail template this newsletter should be derived from'),
    );

    $form['title']['#title'] = t('Title');
    $form['title']['#description'] = t('The title for this newsletter template.');

  }

  /**
   * Validate submission of the newsletter settings form.
   */
  function edit_form_basic_validate($form, &$form_state) {

    parent::edit_form_validate($form, $form_state);
    if (empty($form_state['values']['mail_template_name'])) {
      form_error($form['mail_template_name'], t('A newsletter must be derived from a dynamic mail template.'));
    }
    if (preg_match("/[^A-Za-z0-9 ]/", $form_state['values']['category'])) {
      form_error($form['category'], t('Categories may contain only alphanumerics or spaces.'));
    }

  }

  /**
   * Submit the newsletter settings form.
   */
  function edit_form_basic_submit($form, &$form_state) {

    parent::edit_form_submit($form, $form_state);

    $mailtemplate_ui = new mchammer_mail_template_ui();
    $display = $mailtemplate_ui->create_newsletter($form_state['values']['mail_template_name']);

    $form_state['item']->display = $display;
    $form_state['display'] = &$form_state['item']->display;

  }

  /**
   * Step 2 of wizard: Choose the content.
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