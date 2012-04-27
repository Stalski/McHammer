<?php
// Plugin definition
$plugin = array(
  'title' => t('Port of antwerp: Radar'),
  'category' => t('Mail templates'),
  'icon' => 'sample.png',
  'theme' => 'poa-mail',
  'regions' => array(
    'top' => t('Top column'),
    'left' => t('Left column'),
    'right' => t('Small right column'),
  ),
  'module' => 'mchammer', // mchammer as module is required
  'html-mchammer' => 'html-poa-mail', // html.tpl.php for mchammer
);
