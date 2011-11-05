<?php

/**
 * @class
 * Base class for extraction panes to a new display.
 */
class McHammerDefaultExtractor extends McHammerExtractor implements McHammerExtractorInterface {

  /**
   * Extract the source pane. Copy him to the display.
   * @param $display Panels display to extract in.
   */
  public function extract(&$display) {

    if (!$this->sourceDisplay) {
      // @todo => throw new MchammerExtracterException();
    }

    $new_pane = $this->sourceDisplay->clone_pane($this->sourcePane->pid);
    $new_pane->configuration['source'] = $this->sourcePane->type . ':pane-' . $this->sourcePane->pid;
    $display->add_pane($new_pane, $this->sourcePane->panel);
    unset($new_pane);

  }

  /**
   * Revert the pane to his original state.
   * @param $display Panels display to update.
   */
  public function revert(&$display) {

  }

}