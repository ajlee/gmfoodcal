import 'popper.js';
import 'bootstrap';

(function () {

  'use strict';

  // enable a datepicker
  Drupal.behaviors.enableDatePicker = {
    attach: function (context) {
      jQuery('[type="date"]').datepicker({ 'dateFormat':'yy-mm-dd'});
    }
  };

  // enable a timepicker
  Drupal.behaviors.enableTimePicker = {
    attach: function (context) {
      jQuery('[type="time"]').jonthorntonTimepicker({'timeFormat':'H:i:s'});
    }
  };

})(jQuery, Drupal);
