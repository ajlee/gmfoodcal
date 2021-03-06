<?php

namespace Drupal\gmfood_general\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'geolocation_link_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "geolocation_link_url_formatter",
 *   label = @Translation("Outputs URL to Google Map"),
 *   field_types = {
 *     "geolocation"
 *   }
 * )
 */
class GeolocationLinkUrlFormatter extends FormatterBase {

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
      $element[$delta] = ['#markup' => "View in Google Maps at https://www.google.com/maps/@" . $lat . "," . $long . ",19z"];
    }

    return $element;
  }

}
