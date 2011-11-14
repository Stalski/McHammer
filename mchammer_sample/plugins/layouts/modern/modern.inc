<?php
// Plugin definition
$plugin = array(
  'title' => t('Example: Modern'),
  'category' => t('Mail templates'),
  'icon' => 'sample.png',
  'theme' => 'modern',
  'regions' => array(
    'left' => t('Left column'),
    'middle' => t('Middle column'),
  ),
  'module' => 'mchammer', // mchammer as module is required
);
