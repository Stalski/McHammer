<?php

/**
 * @file
 * Contains ctools export_ui plugin for heartbeat templates.
 */

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

  // All callbacks will accept &$form and &$form_state as arguments.
  'form' => array(
    'settings' => 'mchammer_ctools_export_ui_form',
    'validate' => 'mchammer_ctools_export_ui_form_validate',
    'submit' => 'mchammer_ctools_export_ui_form_submit',
  ),

  'handler' => array(
    'class' => 'ctools_export_ui_mail_template',
    'parent' => 'ctools_export_ui',
  ),

);
