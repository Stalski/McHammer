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
function mchammer_modal_rerender($js, $template_name, $pane_name) {

  if ($js) {
    ctools_include('modal');
    ctools_include('ajax');
  }

  $form_state = array(
    'title' => t('Are you sure you want to rerender this pane?'),
    'ajax' => TRUE,
    'template_name' => $template_name,
    'pane_name' => $pane_name,
  );

  // Fall back if $js is not set.
  if (!$js) {
    return drupal_get_form('mchammer_modal_rerender_confirm', $pane_name);
  }
  else {

    $form_state['wtf'] = 'jow dude';
    // Send this all off to our form. This is like drupal_get_form only wizardy.
    $output = ctools_modal_form_wrapper('mchammer_modal_rerender_confirm', $form_state);

    if (!empty($form_state['executed'])) {
      $output = array();
      $group  = mchammer_modal_rerender_pane_group($template_name, $pane_name, $form_state);
      $output[] = ajax_command_replace('div#mchammer-' . str_replace(":", "--", $pane_name), $group->content);
      // @TODO The panels should know about the newly rerendered panes. Fix this one way or another
      //$output[] = ajax_command_invoke('input[name="panel[pane][middle]"]', 'mcHammerUpdatePanes', array('pane_name' => $pane_name, 'template_name' => $template_name, 'pids' => $group->pids));
      $output[] = ctools_modal_command_dismiss();
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
  $form['#template_name'] = $form_state['template_name'];
  if (!empty($form_state['template_name'])) {
    $form['#template_name'] = $form_state['template_name'];
  }

  $output = confirm_form($form,
    t('Are you sure you want to rerender this pane?'),
    $path,
    t('This action cannot be undone.'),
    t('Rerender'),
    t('Cancel'));

  return $output;

}

/**
 * Handler for wipe confirmation
 */
function mchammer_modal_rerender_pane_group($template_name, $pane_name, &$form_state) {

  $mailtemplate_ui = new mchammer_mail_template_ui();
  $display = $mailtemplate_ui->create_newsletter($template_name);

  $new_display = clone($display);
  $new_display->content = array();
  foreach ($display->content as $key => $content) {
    if ($content->configuration['source'] == $pane_name) {
      $new_display->content[$key] = $content;
    }
  }

  ctools_include('ajax');
  ctools_include('plugins', 'panels');
  ctools_include('display-edit', 'panels');
  $renderer = panels_get_renderer_handler('mchammer_newsletter', $new_display);

  $form_state['display'] = $new_display;

  $form_state['wtf'] = 'true, dude';
  return $renderer->render($pane_name);

}