<?php
/*
 * @file
 * Special theme adaptations.
 */


/**
 * Override the page theming for the imagecrop pages.
 */
function mchammer_page($variables) {

  $output = '';
  if (isset($variables['messages'])) {
    $output .= $variables['messages'];
  }

  global $theme;
  $query = db_select('block');
  $query->addField('block', 'region');
  $query->condition('theme', $theme);
  $query->condition('module', 'system');
  $query->condition('delta', 'main');
  $region = $query->execute()->fetchField();

  global $theme;

  if (isset($variables['page'][$region])) {
    $output = mchammer_render_page_content($variables['page'][$region]);
  }

  return $output;

}

function mchammer_html($variables) {

  return ':)';

}