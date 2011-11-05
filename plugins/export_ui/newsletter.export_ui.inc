<?php

$plugin = array(

  'schema' => 'mchammer_newsletters',
  'access' => 'administer newsletters',

  'menu' => array(
    'menu prefix' => 'admin/structure/mchammer', // default
    'menu item' => 'newsletter', // default
    'menu title' => 'Administer newsletters',
    'menu description' => 'A MCHammer Newsletter is the mail content that will be used to schedule and send.',
  ),

  'title singular' => t('Newsletter'),
  'title singular proper' => t('Newsletter'),
  'title plural' => t('Newsletters'),
  'title plural proper' => t('Newsletters'),

  'use wizard' => TRUE,
  'form info' => array(
    'order' => array(
      'basic' => t('Settings'),
      'content' => t('Content'),
    ),
    // We have to add this form specially because it's invisible.
    'forms' => array(
      'lock' => array(
        'form id' => 'ctools_export_ui_edit_item_wizard_form',
      ),
    ),
  ),

  'handler' => array(
    'class' => 'mchammer_newsletter_ui',
    'parent' => 'ctools_export_ui',
  ),

);