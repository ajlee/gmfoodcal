<?php

namespace Drupal\gmfood_general\Controller;

use \Drupal\Core\Controller\ControllerBase;
use \Drupal\node\NodeInterface;
use \Drupal\Core\Url;
use \Drupal\node\Entity\Node;
use \Drupal\Core\Access\AccessResult;
use Symfony\Component\HttpFoundation\Response;
use \Drupal\Core\Entity\EntityInterface;

/**
 * An controller for Rest feeds
 * connects URLs to feeds and checks permissions
 */
class RestFeedsController extends ControllerBase {


  public function __construct()
  {
  }

  /**
   * Displays the form and custom HTML for the Post to discourse page
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node where the tab should be added
   * Returns a render-able array for a test page.
   */
  public function displayXml(NodeInterface $node) {

    $build = [
      '#title' => 'Hello world',
      'view' => [
        '#type' => 'view',
        '#name' => 'single_calendar_rest_feed',
        '#display_id' => 'rest_export_1',
        '#arguments' => [$node->id(), $node->id(), $node->id()],
        '#embed' => TRUE,
      ],
    ];
    $output = \Drupal::service('renderer')->renderRoot($build);

    $response = new Response();
    $response->setContent($output);
    $response->headers->set('Content-Type', 'text/xml');

    return $response;

  }

  public function displayICal() {


    // Get the current URL and cut in parts you can later check more easily.
    //$current_uri = \Drupal::request()->getRequestUri(); // '/url/node1/rss'
    //$current_uri = ltrim($current_uri, '/'); // 'url/node1/rss'

    // RIGHT TRIM
    //$url_array = explode('/', $current_uri); // array [ url, node1, rss ]
    //$last_part = array_pop($url_array); // rss
    //$url_alias = implode('/', $url_array); // 'url/node1'
    //$langcode = 'en';

    // does url/node1 match some content
    //kint($current_uri);
    //kint($url_alias);
    //$system_path = \Drupal::service('path.alias_manager')->getPathByAlias($url_alias, $langcode);

    //$params = Url::fromUri("internal:" . $system_path)->getRouteParameters();
    /*$entity_type = key($params);
    $node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
    kint($node);

    $path_matches = \Drupal::service('path.matcher')->matchPath($path, $patterns);
    // Check URL to match a certain pattern, and switch the view mode.
    if ($arg[0] === 'url' && $arg[1] === 'alias2' && $arg[2] === 'path') {
      $view_mode = 'teaser';
    }*/

    $build = array();

    // Get the current URL and cut in parts you can later check more easily.
    $current_uri = \Drupal::request()->getRequestUri(); // '/url/node1/rss'
    //$current_uri = ltrim($current_uri, '/'); // 'url/node1/rss'

    // RIGHT TRIM
    $url_array = explode('/', $current_uri); // array [ url, node1, rss ]
    $last_part = array_pop($url_array); // rss
    kint($last_part);

    // is the last part of the URL rss, ical or json
    if (in_array($last_part, array('rss', 'ical', 'json'))){

      $url_alias = implode('/', $url_array); // 'url/node1'
      $langcode = 'en';

      // does url/node1 match some content
      //kint($current_uri);
      //kint($url_alias);
      $system_path = \Drupal::service('path.alias_manager')->getPathByAlias($url_alias, $langcode);

      kint($system_path);
      // change to node?
      $new_url = Url::fromUri("internal:" . $system_path);
      kint($new_url);

      // is it a node url?
      // if not it will trigger an exception and will run the catch () section
      try {
        // is it a node url
        if($new_url->getRouteName() == 'entity.node.canonical') {
          $params = $new_url->getRouteParameters('node');
          kint($node_id);
          $parent_node = \Drupal\node\Entity\Node::load($params['node']);
          //print_r($parent_node);
          $parent_type = $parent_node->getType();
          if($parent_type == 'event') {
            kint('event');
            $build['ical']['view'] = [
              '#type' => 'view',
              '#name' => 'event_ical',
              '#display_id' => 'feed_1',
              '#arguments' => [
                $params['node'],
              ]
            ];
          }
        }
      }
      catch(Exception $e) {
        kint('catch');
      }
      //$params = $new_url->getRouteParameters();

      //$entity_type = key($params);
      //$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
      //kint($params);

      /* $path_matches = \Drupal::service('path.matcher')->matchPath($path, $patterns);
      // Check URL to match a certain pattern, and switch the view mode.
      if ($arg[0] === 'url' && $arg[1] === 'alias2' && $arg[2] === 'path') {
        $view_mode = 'teaser';
      }*/

      return $build;
    }
  }


  /**
   * Checks access for the calendar node feeds.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node where the tab should be added.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   An access result object.
   */
  public function checkAccess() {
    return AccessResult::allowed();
  }
}
