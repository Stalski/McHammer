<?php
/*
 * @file
 *
 */

/**
 * Menu callback to render a list of McHammer mail templates.
 */
function mchammer_list_templates() {
  return "x";
}

/**
 * Override of node add page to force layout selection prior
 * to actually editing a node.
 */
function mchammer_add_template() {
  $output = '';

  ctools_include('plugins', 'panels');
  ctools_include('common', 'panels');

  $layouts = panels_common_get_allowed_layouts('mail_template');
  return panels_common_print_layout_links($layouts, 'admin/structure/mchammer/mail-template/add/panel', array('query' => $_GET));
}

// ---------------------------------------------------------------------------
// Meat of the Panels API; almost completely passing through to panels.module

/**
 * Pass through to the panels layout editor.
 */
function mchammer_edit_layout($mail_template) {
//  ctools_include('plugins', 'panels');
  ctools_include('context');
  $display = panels_load_display($mail_template->panels_mail_template['did']);
  $display->context = mchammer_get_context($mail_template);
  return panels_edit_layout($display, t('Save'), "admin/structure/mchammer/mail-template/$mail_template->mtid/panel_layout", 'panels_node');
}

/**
 * Pass through to the panels content editor.
 */
function mchammer_edit_content($mail_template) {
  ctools_include('context');
  $display = panels_load_display($mail_template->panels_mail_template['did']);
  $display->context = mchammer_get_context($mail_template);
  ctools_include('common', 'panels');
  $content_types = panels_common_get_allowed_types('mchammer', $display->context);

  return panels_edit($display, "admin/structure/mchammer/mail-template/$mail_template->mtid/panel_content", $content_types);
}