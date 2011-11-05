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
        return new McHammerDefaultExtractor($display);

    }

  }

}

/**
 * @class McHammerExtractor
 * Class that provides information about extracting of displays.
 */
class McHammerExtractor {

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

}