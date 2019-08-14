import 'popper.js';
import 'bootstrap';

(function () {

  'use strict';

  Drupal.behaviors.enableDatePicker = {
    attach: function (context) {
      jQuery('[type="date"]').datepicker();
    }
  };


  Drupal.behaviors.enableTimePicker = {
    attach: function (context) {
      jQuery('[type="time"]').jonthorntonTimepicker({'timeFormat':'H:i:s'});
    }
  };

})(jQuery, Drupal);
