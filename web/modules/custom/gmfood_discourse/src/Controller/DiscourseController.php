<?php

namespace richp10\discourseAPI;
namespace Drupal\gmfood_discourse\Controller;

require_once "/var/www/drupal8/web/modules/custom/gmfood_discourse/vendor/discourse_api/src/DiscourseAPI.php";

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;

/**
 * An example controller.
 */
class DiscourseController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function displayForm(NodeInterface $node) {

    $api_key = getenv('DISCOURSE_KEY');
    $api = new \richp10\discourseAPI\DiscourseAPI("forum.gmfoodforum.org", getenv('DISCOURSE_KEY'), 'https');
    $category = $api->getCategories();
    $string = serialize($category);
    $build = [];
    $build['heading'] = [
      '#type' => 'markup',
      '#markup' => '<h4 class="discourse-form-heading">' . $this->t('Use this form to post <strong>' . $node->getTitle() . '</strong> to the Discourse forum') . '</h4><br/>',
    ];
    $build['#title'] = $node->getTitle();
    $build['form'] = $this->formBuilder()->getForm('Drupal\gmfood_discourse\Form\DiscourseForm', $node);
    return $build;
  }

}
