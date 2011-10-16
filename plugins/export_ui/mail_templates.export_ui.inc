<?php

$plugin = array(
  'schema' => 'mchammer_mail_templates',
  'access' => 'administer mail templates',


  'menu' => array(
    'menu prefix' => 'admin/structure/mchammer', // default
    'menu item' => 'mail-template', // default
    'menu title' => 'McHammer mail templates',
    'menu description' => 'Administer mail templates.',
  ),

  'title singular' => t('Mail template'),
  'title singular proper' => t('Mail template'),
  'title plural' => t('Mail templates'),
  'title plural proper' => t('Mail templates'),

  'use wizard' => TRUE,
  'form info' => array(
    'order' => array(
      'basic' => t('Settings'),
      'layout' => t('Layout'),
      'content' => t('Content'),
    ),
    // We have to add this form specially because it's invisible.
    'forms' => array(
      'move' => array(
        'form id' => 'ctools_export_ui_edit_item_wizard_form',
      ),
    ),
  ),

  'handler' => array(
    'class' => 'mchammer_mail_template_ui',
    'parent' => 'ctools_export_ui',
  ),

);
