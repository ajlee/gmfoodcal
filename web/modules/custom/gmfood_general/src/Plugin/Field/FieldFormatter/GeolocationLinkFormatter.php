<?php

namespace Drupal\gmfood_general\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'geolocation_link_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "geolocation_link_formatter",
 *   label = @Translation("Link to map"),
 *   field_types = {
 *     "geolocation"
 *   }
 * )
 */
class GeolocationLinkFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays a link to the map.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $lat = $item->get('lat')->getValue();
      $long = $item->get('lng')->getValue();
      $element[$delta] = ['#markup' => "<a target='_map' href='https://www.google.com/maps/@" . $lat . "," . $long. ",19z'>View in Google Maps</a>"];
    }

    return $element;
  }

}
