<?php

namespace richp10\discourseAPI;
namespace Drupal\gmfood_discourse\Controller;

require_once DRUPAL_ROOT . "/modules/custom/gmfood_discourse/vendor/discourse_api/src/DiscourseAPI.php";

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;

/**
 * An controller for Discourse
 * displays a form for discourse pages and checks permissions to view it
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
   * Displays the form and custom HTML for the Post to discourse page
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node where the tab should be added
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
    else if ($type == "event") {
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

    return $build;
  }

  /**
   * Checks access for the simplenews node tab.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node where the tab should be added.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   An access result object.
   */
  public function checkAccess(NodeInterface $node) {
    $account = $this->currentUser();
    $node_type = $node->getType();

    // the node types that can be posted to discourse
    $allowed_node_types = ['simplenews_issue', 'event','news'];

    if (in_array($node_type, $allowed_node_types)) {
      // requires simplenews permissions to be able to post to discourse
      return AccessResult::allowedIfHasPermission($account, 'administer newsletters')
        ->orIf(AccessResult::allowedIfHasPermission($account, 'send newsletter'));
    }
    return AccessResult::neutral();
  }

}
