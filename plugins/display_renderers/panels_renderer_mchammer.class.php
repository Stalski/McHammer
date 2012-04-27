<?php

/**
 * Renderer class for all MCHammer behavior.
 */
class panels_renderer_mchammer extends panels_renderer_editor {

  /**
   * Render the links to display when editing a region.
   */
  function get_region_links($region_id) {

    $links = array();
    $links[] = array(
      'title' => t('Add content'),
      'href' => $this->get_url('select-content', $region_id),
      'attributes' => array(
        'class' => array('ctools-use-modal'),
      ),
    );

    return theme('ctools_dropdown', array('title' => theme('image', array('path' => ctools_image_path('icon-addcontent.png', 'panels'))), 'links' => $links, 'image' => TRUE, 'class' => 'pane-add-link panels-region-links-' . $region_id));

  }

  /**
   * Render the links to display when editing a pane.
   */
  function get_pane_links($pane, $content_type) {
    $links = array();

    if (!empty($pane->shown)) {
      $links[] = array(
        'title' => t('Disable this pane'),
        'href' => $this->get_url('hide', $pane->pid),
        'attributes' => array('class' => array('use-ajax')),
      );
    }
    else {
      $links[] = array(
        'title' => t('Enable this pane'),
        'href' => $this->get_url('show', $pane->pid),
        'attributes' => array('class' => array('use-ajax')),
      );
    }

    $subtype = ctools_content_get_subtype($content_type, $pane->subtype);

    if (ctools_content_editable($content_type, $subtype, $pane->configuration)) {
      $links[] = array(
        'title' => isset($content_type['edit text']) ? $content_type['edit text'] : t('Settings'),
        'href' => $this->get_url('edit-pane', $pane->pid),
        'attributes' => array('class' => array('ctools-use-modal')),
      );
    }

    $links[] = array(
      'title' => t('Remove'),
      'href' => '#',
      'attributes' => array(
        'class' => array('pane-delete'),
        'id' => "pane-delete-panel-pane-$pane->pid",
      ),
    );

    if (isset($pane->configuration['source'])) {
      $links[] = array(
        'title' => t('Rerender source'),
        'href' => 'mchammer/nojs/rerender/' . $this->display->cache_key . '/' . $this->mail_template_name . '/' . $pane->configuration['source'],
        'attributes' => array(
          'class' => array('ctools-use-modal'),
          'id' => "pane-revert-source-pane-$pane->pid",
        ),
      );
    }

    return theme('ctools_dropdown', array('title' => theme('image', array('path' => ctools_image_path('icon-configure.png', 'panels'))), 'links' => $links, 'image' => TRUE));

  }

  /**
   * Create a list of categories from all of the content type.
   *
   * @return array
   *   An array of categories. Each entry in the array will also be an array
   *   with 'title' as the printable title of the category, and 'content'
   *   being an array of all content in the category. Each item in the 'content'
   *   array contain the array plugin definition so that it can be later
   *   found in the content array. They will be keyed by the title so that they
   *   can be sorted.
   */
  function get_categories($content_types) {

    $enabled_categories = array_filter(variable_get('mchammer_panel_categories', array()));

    $categories = array();
    $category_names = array();

    foreach ($content_types as $type_name => $subtypes) {
      foreach ($subtypes as $subtype_name => $content_type) {
        list($category_key, $category) = $this->get_category($content_type);

        $content_title = filter_xss_admin($content_type['title']);

        // If category is not root, check if the category is enabled.
        if ($category_key != 'root' && !isset($enabled_categories[$category_key])) {
          continue;
        }
        // If category is root, check if the plugin is enabled.
        elseif ($category_key == 'root' && !isset($enabled_categories[$content_title])) {
          continue;
        }

        if (empty($categories[$category_key])) {
          $categories[$category_key] = array(
            'title' => $category,
            'content' => array(),
          );
          $category_names[$category_key] = $category;
        }

        // Ensure content with the same title doesn't overwrite each other.
        while (isset($categories[$category_key]['content'][$content_title])) {
          $content_title .= '-';
        }

        $categories[$category_key]['content'][$content_title] = $content_type;
        $categories[$category_key]['content'][$content_title]['type_name'] = $type_name;
        $categories[$category_key]['content'][$content_title]['subtype_name'] = $subtype_name;
      }
    }

    // Now sort
    natcasesort($category_names);
    foreach ($category_names as $category => $name) {
      $output[$category] = $categories[$category];
    }

    return $output;
  }

}