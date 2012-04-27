<?php
/**
 * @file
 * Administration pages for MCHammer mail templates and newsletters.
 */

/**
 * General settings form for mc hammer.s
 */
function mchammer_settings_form() {

  $form = array();

  ctools_include('content');
  $content_types = ctools_content_get_available_types();
  $renderer = new panels_renderer_editor();
  $categories = $renderer->get_categories($content_types);

  $options = array();
  foreach ($categories as $key => $category) {

    // Root subcategories
    if ($key == 'root') {
      foreach ($category['content'] as $sub_key => $sub_category) {
        $options[$sub_key] = $sub_category['title'];
      }
    }
    // Tabs
    else {
      $options[$key] = $category['title'];
    }

  }

  $form['mchammer_panel_categories'] = array(
    '#title' => t('Panel categories that are enabled for MC Hammer'),
    '#type' => 'checkboxes',
    '#options' => $options,
    '#default_value' => variable_get('mchammer_panel_categories', array()),
    '#description' => t("Categories that are not selected, will not be visible in the 'Add content' from panels"),
  );

  return system_settings_form($form);

}