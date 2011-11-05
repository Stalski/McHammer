<?php

/**
 * @file
 * Ctools export UI for a McHammer Newsletter.
 */
class mchammer_newsletter_ui extends ctools_export_ui {

  private $list = array();

  /**
   * Implements ctools_export_ui::init().
   * Create menu entries for extra operations.
   */
  function init($plugin) {

    $plugin['menu']['items']['create-newsletter'] = array();
    $plugin['menu']['items']['create-newsletter']['path'] = 'add/%/create-newsletter';
    $plugin['menu']['items']['create-newsletter']['title'] = 'Create newsletter from template';
    $plugin['menu']['items']['create-newsletter']['page callback'] = 'ctools_export_ui_switcher_page';
    $plugin['menu']['items']['create-newsletter']['page arguments'] = array($plugin['name'], 'create_newsletter', 5);
    $plugin['menu']['items']['create-newsletter']['access callback'] = 'user_access';
    $plugin['menu']['items']['create-newsletter']['access arguments'] = array('create newsletters');
    $plugin['menu']['items']['create-newsletter']['load arguments'] = array($plugin['name']);
    $plugin['menu']['items']['create-newsletter']['type'] = MENU_CALLBACK;

    return parent::init($plugin);

  }

  /**
   * Implements ctools_export_ui::edit_cache_get_key().
   * Figure out what the cache key is for this object.
   */
  function edit_cache_get_key($item, $op) {
    $export_key = $this->plugin['export']['key'];
    return $op == 'edit' ? 'newsletter:' . $item->{$this->plugin['export']['key']} : "newsletter:::$op";
  }

  /**
   * Implements ctools_export_ui::list_build_row().
   * Creates a list of newsletters.
   */
  function list_build_row($item, &$form_state, $operations) {
    $operations['view'] = array(
      'href' => 'mchammer/newsletter/' . $item->name,
      'title' => t('View'),
    );
    parent::list_build_row($item, $form_state, $operations);
  }

  /**
   * Implements ctools_export_ui::add_page().
   */
  function add_page($js, $input, $step = NULL, $template_name = NULL) {
    drupal_set_title($this->get_page_title('add'));

    // If a step not set, they are trying to create a new item. If a step
    // is set, they're in the process of creating an item.
    if (!empty($this->plugin['use wizard']) && !empty($step)) {
      $item = $this->edit_cache_get(NULL, 'add');
    }

    // Instantiate a new mail template plugin.
    if (empty($item)) {
      $item = ctools_export_crud_new($this->plugin['schema']);
    }

    $form_state = array(
      'plugin' => $this->plugin,
      'object' => &$this,
      'ajax' => $js,
      'item' => $item,
      'op' => 'add',
      'form type' => 'add',
      'rerender' => TRUE,
      //'no_redirect' => TRUE,
      'no_redirect' => FALSE,
      'step' => $step,
      // Store these in case additional args are needed.
      'function args' => func_get_args(),
      'template_name' => $template_name,
    );

    $output = $this->edit_execute_form($form_state);
    if (!empty($form_state['executed'])) {
      $export_key = $this->plugin['export']['key'];
      drupal_goto(str_replace('%ctools_export_ui', $form_state['item']->{$export_key}, $this->plugin['redirect']['add']));
    }

    return $output;

  }

  /**
   * Implements ctools_export_ui::edit_page().
   * Main entry point to edit an item.
   */
  function edit_page($js, $input, $item, $step = NULL) {

    drupal_set_title($this->get_page_title('edit', $item));

    // Check to see if there is a cached item to get if we're using the wizard.
    if (!empty($this->plugin['use wizard'])) {
      $cached = $this->edit_cache_get($item, 'edit');
      if (!empty($cached)) {
        $item = $cached;
      }
    }

    $form_state = array(
        'plugin' => $this->plugin,
        'object' => &$this,
        'ajax' => $js,
        'item' => $item,
        'op' => 'edit',
        'form type' => 'edit',
        'rerender' => TRUE,
        'no_redirect' => TRUE,
        'step' => $step,
        // Store these in case additional args are needed.
        'function args' => func_get_args(),
    );

    $output = $this->edit_execute_form($form_state);
    if (!empty($form_state['executed'])) {
      $export_key = $this->plugin['export']['key'];
      // Override the default redirect to the list to always show what is shown in the newsletter.
      // $redirect = $this->plugin['redirect']['edit'];
      $redirect = ctools_export_ui_plugin_menu_path($this->plugin, 'edit', $form_state['item']->{$export_key}) . '/content';
      drupal_goto(str_replace('%ctools_export_ui', $form_state['item']->{$export_key}, $redirect));
    }

    return $output;

  }

  /**
   * Page callback to add a newsletter from template
   */
  function create_newsletter_page($js, $input, $template_name, $step = NULL) {
    return $this->add_page($js, $input, $step, $template_name);
  }

  /**
   * Implements ctools_export_ui::edit_form().
   * Edit form
   */
  function edit_form(&$form, &$form_state) {

    $this->get_mail_templates();

    if (isset($form_state['template_name'])) {

      // Bail out if the template does not exist.
      if (!isset($this->list[$form_state['template_name']])) {
        drupal_not_found();
        exit;
      }

      // Initialize a form derived from a mail template.
      $this->derive_form($form_state['template_name'], $form, $form_state);

    }
  	// Default to creating a newsletter with extra selectbox for templates.
    else {
      $this->default_edit_form($form, $form_state);
  	}

  }

  /**
   * Implements ctools_export_ui::edit_form_basic_validate().
   * Validate submission of the newsletter settings form.
   */
  function edit_form_basic_validate($form, &$form_state) {

    parent::edit_form_validate($form, $form_state);
    if (empty($form_state['values']['mail_template_name'])) {
      form_error($form['mail_template_name'], t('A newsletter must be derived from a dynamic mail template.'));
    }
    if (preg_match("/[^A-Za-z0-9 ]/", $form_state['values']['category'])) {
      form_error($form['category'], t('Categories may contain only alphanumerics or spaces.'));
    }

  }

  /**
   * Implements ctools_export_ui::edit_form_basic_submit().
   * Submit the newsletter settings form.
   */
  function edit_form_basic_submit($form, &$form_state) {

    parent::edit_form_submit($form, $form_state);

    $display = $this->get_display_from_template($form_state['values']['mail_template_name']);

    $form_state['item']->display = $display;
    $form_state['display'] = &$form_state['item']->display;

  }

  /**
   * Implements ctools_export_ui::edit_form_content().
   * Step 2 of wizard: Choose the content.
   */
  function edit_form_content(&$form, &$form_state) {

    ctools_include('ajax');
    ctools_include('plugins', 'panels');
    ctools_include('display-edit', 'panels');

    // If we are cloning an item, we MUST have this cached for this to work,
    // so make sure:
    if ($form_state['form type'] == 'clone' && empty($form_state['item']->export_ui_item_is_cached)) {
      $this->edit_cache_set($form_state['item'], 'clone');
    }

    $cache_key = $this->edit_cache_get_key($form_state['item'], $form_state['form type']);
    $cache = panels_edit_cache_get('mchammer:' . $cache_key);

    $form_state['renderer'] = panels_get_renderer_handler('editor', $cache->display);
    $form_state['renderer']->cache = &$cache;

    $form_state['display'] = &$cache->display;
    $form_state['content_types'] = $cache->content_types;
    // Tell the Panels form not to display buttons.
    $form_state['no buttons'] = TRUE;
    $form_state['display_title'] = !empty($cache->display_title);

    $form = panels_edit_display_form($form, $form_state);

    // Build up the lock button.
    $form['buttons']['lock'] = array(
      '#type' => 'submit',
      '#value' => $form_state['item']->locked ? t('Unlock') : t('Lock'),
      //'#attributes' => array('class' => array('use-ajax-submit')),
      '#id' => 'panels-lock-button',
      '#submit' => array('panels_edit_display_form_submit', 'panels_edit_display_form_lock'),
    );
    if ($form_state['item']->locked) {
      drupal_set_message(t('This newsletter is locked and will not be regenerated untill you remove the lock and regenerate the content.'), 'warning');
    }

    // Make sure the theme will work since our form id is different.
    $form['#theme'] = 'panels_edit_display_form';

  }

  /**
   * Implements ctools_export_ui::edit_form_content_submit().
   * Save the display.
   */
  function edit_form_content_submit(&$form, &$form_state) {
    panels_edit_display_form_submit($form, $form_state);
    $form_state['item']->display = $form_state['display'];
  }

  /**
   * Fetch all mail templates.
   */
  private function get_mail_templates() {
    if (empty($this->list)) {
      $this->list = mchammer_mail_templates_list();
    }
    return $this->list;
  }

  /**
   * Gets the display for a template name.
   */
  private function get_display_from_template($template_name) {
    $mailtemplate_ui = new mchammer_mail_template_ui();
    return $mailtemplate_ui->create_newsletter($template_name);
  }

  /**
   * Derives a newsletter from a given template argument.
   */
  private function derive_form($template_name, &$form, &$form_state) {

    ctools_include('content');

    // Load the original mail template
    $template = mchammer_mail_template_load($template_name);

    // Get the basic edit form
    parent::edit_form($form, $form_state);

    $form['category'] = array(
      '#type' => 'hidden',
      '#default_value' => $template->category,
    );
    $form['mail_template_name'] = array(
      '#type' => 'hidden',
      '#default_value' => $template->name,
    );

  }

  /**
   * Creates a form to add or edit basic newsletter information.
   */
  private function default_edit_form(&$form, &$form_state) {

    ctools_include('export');
    $options = array(0 => t('None'));
    foreach (ctools_export_load_object('mchammer_mail_templates', 'all') as $name => $option) {
      $options[$name] = $option->admin_title;
    }

    if (empty($options)) {
      return;
    }

    // Get the basic edit form
    parent::edit_form($form, $form_state);

    $form['category'] = array(
      '#type' => 'textfield',
      '#size' => 24,
      '#default_value' => $form_state['item']->category,
      '#title' => t('Category'),
      '#description' => t("The category that this newsletter template will be grouped into on the Add Content form. Only upper and lower-case alphanumeric characters are allowed."),
    );

    $form['mail_template_name'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $form_state['item']->mail_template_name,
      '#title' => t('Mail template'),
      '#description' => t('Mail template this newsletter should be derived from'),
    );

    $form['title']['#title'] = t('Title');
    $form['title']['#description'] = t('The title for this newsletter template.');

  }

}