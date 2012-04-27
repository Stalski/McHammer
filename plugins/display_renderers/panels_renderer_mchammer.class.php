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

}