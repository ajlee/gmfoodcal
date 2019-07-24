<?php

namespace Drupal\gmfood_discourse\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class DiscourseController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function content() {
    $build = [
      '#markup' => $this->t('Hello Discourse!'),
    ];
    return $build;
  }

}
