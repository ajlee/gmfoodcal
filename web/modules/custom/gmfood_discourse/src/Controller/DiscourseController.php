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
  private $discourseApiKey = null;
  private $discourseApi = null;

  public function __construct()
  {
    // initialise the API
    $this->discourseApiKey = getenv('DISCOURSE_KEY');
    $this->discourseApi = new \richp10\discourseAPI\DiscourseAPI("forum.gmfoodforum.org", $this->discourseApiKey, 'https');
  }

  /**
   * Returns a render-able array for a test page.
   */
  public function displayForm(NodeInterface $node) {

    $nodehtml = null;
    $titlehtml = null;
    $type = $node->getType();

    // Preprocessing newsletters
    if ($type == "simplenews_issue") {
      $view_mode = 'email_plain';
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      $build = $view_builder->view($node, $view_mode);
      $nodehtml = \Drupal::service('renderer')->renderPlain($build);
      $titlehtml = '<h3>' . $node->getTitle() . '</h3>';
    }


    $category = $this->discourseApi->getCategories();
    $string = serialize($category);
    $build = [];
    $build['heading'] = [
      '#type' => 'markup',
      '#markup' => '<h4 class="discourse-form-heading">' . $this->t('Use this form to post ' . $type . ' <strong>' . $node->getTitle() . '</strong> to the Discourse forum') . '</h4><br/>',
    ];

    // LOG WHO POSTED TO DISCOURSE
    //
    $build['#title'] = $node->getTitle();
    $build['form'] = $this->formBuilder()->getForm('Drupal\gmfood_discourse\Form\DiscourseForm', $node);
    $build['footer'] = [
      '#type' => 'markup',
      '#markup' => '<h2>Discourse view</h2><hr/><article>' . $titlehtml . $nodehtml . '</article>',
    ];
    return $build;
  }

  public function postToDiscourse() {

  }
}
