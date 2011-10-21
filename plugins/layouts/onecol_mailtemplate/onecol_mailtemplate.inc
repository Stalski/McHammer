<?php

/**
 * implementation of hook_panels_layouts()
 */
// Plugin definition
$plugin = array(
  'title' => t('Single column mail template'),
  'category' => t('Mail templates'),
  'icon' => 'onecol.png',
  'theme' => 'panels_onecol_mailtemplate',
  'css' => 'onecol.css',
  'regions' => array('middle' => t('Middle column')),
);
