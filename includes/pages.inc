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
function mchammer_modal_rerender($js, $pane_name) {

  if ($js) {
    ctools_include('modal');
    ctools_include('ajax');
  }

  $form_state = array(
    'title' => t('Are you sure you want to rerender this pane?'),
    'ajax' => TRUE,
    'pane_name' => $pane_name,
  );

  // Send this all off to our form. This is like drupal_get_form only wizardy.
  $output = ctools_modal_form_wrapper('mchammer_modal_rerender_confirm', $form_state);

  // Fall back if $js is not set.
  if (!$js) {

    return drupal_get_form('mchammer_modal_rerender_confirm', $pane_name);

  }
  else {

    if (!empty($form_state['executed'])) {
      $output = array();
      $output[] = ctools_modal_command_dismiss();
      //$output[] = ajax_command_invoke('.heartbeat-activity-' . $uaid, 'heartbeatRemoveElement', array($uaid, t('Activity deleted.')));
    }

    print ajax_render($output);
    exit;
  }
}

/**
 * form to rerender a derived pane.
 */
function mchammer_modal_rerender_confirm($form, $form_state) {

  $path = isset($_GET['destination']) ? $_GET['destination'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']: '<front>');

  // Prepare the form.
  $form = array(
    'redirect_path' => array(
      '#type' => 'hidden',
      '#value' => $path,
    ),
  );

  $form['#pane_name'] = $form_state['pane_name'];
  if (!empty($form_state['pane_name'])) {
    $form['#pane_name'] = $form_state['pane_name'];
  }

  $output = confirm_form($form,
    t('Are you sure you want to rerender this pane?'),
    $path,
    t('This action cannot be undone.'),
    t('Delete'),
    t('Cancel'));

  return $output;

}

/**
 * Handler for wipe confirmation
 */
function heartbeat_delete_log_confirm_submit($form, &$form_state) {

  // Set the flag so the form knows it's been executed.
  $form_state['executed'] = TRUE;

  if (!empty($form_state['pane_name'])) {
  // act
  }

}