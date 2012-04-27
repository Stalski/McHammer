<?php
/*
 * @file
 * Pages for McHammer pages.
 */

/**
 * Page callback to render/preview a mail template.
 */
function mchammer_mail_template_page($mail_template) {
  return panels_render_display($mail_template->display);
}

/**
 * Page callback to render/preview a newsletter.
 */
function mchammer_newsletter_page($newsletter) {
  return panels_render_display($newsletter->display);
}

/**
 * Ajax callback to rerender multiple panes derived from a mail
 * template pane.
 */
function mchammer_modal_rerender($js, $cache_key, $template_name, $pane_name) {

  if ($js) {
    ctools_include('modal');
    ctools_include('ajax');
  }

  $form_state = array(
    'title' => t('Are you sure you want to rerender the source of this pane?'),
    'ajax' => TRUE,
    'template_name' => $template_name,
    'pane_name' => $pane_name,
    'cache_key' => $cache_key,
  );

  // Panels editor links don't work with no js.
  if (!$js) {
    return MENU_NOT_FOUND;
  }

  // Send this all off to our form. This is like drupal_get_form only wizardy.
  $output = ctools_modal_form_wrapper('mchammer_modal_rerender_confirm', $form_state);

  if (!empty($form_state['executed'])) {
    $output = array();
    $group  = mchammer_modal_rerender_original_pane($cache_key, $template_name, $pane_name, $form_state);
    $output[] = ajax_command_html('#panels-dnd-main', $group);
    // @TODO The panels should know about the newly rerendered panes. Fix this one way or another
    //$output[] = ajax_command_invoke('input[name="panel[pane][middle]"]', 'mcHammerUpdatePanes', array('pane_name' => $pane_name, 'template_name' => $template_name, 'pids' => $group->pids));
    $output[] = ctools_modal_command_dismiss();
  }

  print ajax_render($output);
  exit;

}

/**
 * form to rerender a derived pane.
 */
function mchammer_modal_rerender_confirm($form, $form_state, $template_name = NULL, $pane_name = NULL, $cache_key = NULL) {

  $path = isset($_GET['destination']) ? $_GET['destination'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']: '<front>');

  // Prepare the form.
  $form = array(
    'redirect_path' => array(
      '#type' => 'hidden',
      '#value' => $path,
    ),
  );

  $form['#pane_name'] = (empty($pane_name) ? $form_state['pane_name'] : $pane_name);
  $form['#template_name'] = (empty($template_name) ? $form_state['template_name'] : $template_name);
  $form['#template_name'] = (empty($template_name) ? $form_state['template_name'] : $template_name);

  $output = confirm_form($form,
    $form_state['title'],
    $path,
    t('This action cannot be undone.<br/>When the source contains multiple pains, all of the pains will be reverted.'),
    t('Rerender'),
    t('Cancel'));

  return $output;

}

/**
 * Handler for wipe confirmation
 */
function mchammer_modal_rerender_original_pane($cache_key, $template_name, $pane_name, &$form_state) {

  $mailtemplate_ui = new mchammer_mail_template_ui();
  $display = $mailtemplate_ui->revert_newsletter_pane($template_name, $cache_key, $pane_name);
  $form_state['display'] = $display;

  ctools_include('ajax');
  ctools_include('plugins', 'panels');
  ctools_include('display-edit', 'panels');
  $renderer = panels_get_renderer_handler('mchammer_newsletter', $display);

  return $renderer->render_layout();

}