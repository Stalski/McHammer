<?php
/**
 * @file
 * McHammerViewsExtractor class
 *
 * Extractor class that handles views extraction to panes.
 */

class McHammerViewsExtractor extends McHammerExtractor implements McHammerExtractorInterface {

  /**
   * Extract the views pane. Get all the results and create a pane for each one of them.
   * @param $display Panels display to extract in.
   */
  public function extract(&$display) {

    $view = views_get_view($this->sourcePane->subtype);
    if (!$view) {
      // @todo => throw new MchammerExtracterException();
    }

    $view->execute();
    foreach ($view->result as $result) {

      // @todo Create entity plugin instead of using node.
      $new_pane = panels_new_pane('node', 'node', TRUE);
      $new_pane->configuration['nid'] = $result->nid;
      $new_pane->configuration['source'] = 'view';
      $display->add_pane($new_pane, $this->sourcePane->panel);
      $new_pane->configuration['source'] = $this->sourcePane->type . ':pane-' . $this->sourcePane->pid;

      unset($new_pane);

    }

  }

  /**
   * Revert the pane to his original state.
   * @param $display Panels display to use.
   */
  public function revert(&$display) {

  }

}
