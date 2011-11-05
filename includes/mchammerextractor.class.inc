<?php
/**
 * @file
 * McHammerExtractor class + interface + factory
 *
 */

interface McHammerExtractorInterface {

  /**
   * Extract the source pane his results to a new pane.
   */
  public function extract(&$display);

  /**
   * Revert the pane to his original state.
   */
  public function revert(&$display);

}

/**
 * Factory class for McHammerExtractor.
 */
class McHammerExtractorFactory {

  public static function getExtractor($type, $display) {

    switch ($type) {

      case 'views':
        return new McHammerViewsExtractor($display);

      default:
        return new McHammerExtractor($display);

    }

  }

}

/**
 * @class
 * Base class for extraction panes to a new display.
 */
class McHammerExtractor  implements McHammerExtractorInterface {

  /**
   * The source display
   */
  protected $sourceDisplay;

  /**
   * The source pane
   */
  protected $sourcePane;

  public function __construct($sourceDisplay) {
    $this->sourceDisplay = $sourceDisplay;
  }

  /**
   * Set the source display.
   */
  public function setSourceDisplay($sourceDisplay) {
    $this->sourceDisplay = $sourceDisplay;
  }

  /**
   * Get the source display.
   */
  public function getSourceDisplay() {
    return $this->sourceDisplay;
  }

  /**
   * Set the source pane.
   */
  public function setSourcePane($sourcePane) {
    $this->sourcePane = $sourcePane;
  }

  /**
   * Get the source pane.
   */
  public function getSourcePane() {
    return $this->sourcePane;
  }

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
?>