<?php

$plugin = array(
  'schema' => 'mchammer_newsletters',
  'access' => 'administer newsletters',

  'menu' => array(
    'menu prefix' => 'admin/structure/mchammer', // default
    'menu item' => 'newsletter', // default
    'menu title' => 'McHammer newsletters',
    'menu description' => 'Administer newsletters.',
  ),

  'title singular' => t('Newsletter'),
  'title singular proper' => t('Newsletter'),
  'title plural' => t('Newsletters'),
  'title plural proper' => t('Newsletters'),

  'use wizard' => FALSE,
  'form info' => array(
    'order' => array(
      'basic' => t('Settings'),
      'layout' => t('Layout'),
      'content' => t('Content'),
    ),
    // We have to add this form specially because it's invisible.
//    'forms' => array(
//      'move' => array(
//        'form id' => 'ctools_export_ui_edit_item_wizard_form',
//      ),
//    ),
  ),

  'handler' => array(
    'class' => 'mchammer_newsletter_ui',
    'parent' => 'mchammer_mail_template_ui',
  ),

);
