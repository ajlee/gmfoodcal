import 'popper.js';
import 'bootstrap';

(function () {

  'use strict';
  console.log(0);
  jQuery(document).ready(function(){
          var ua = navigator.userAgent;

          if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile|CriOS/i.test(ua))
            console.log(1);
            // do nothing with datepicker?

          else if(/Chrome/i.test(ua)) {
            console.log(2);
            jQuery('[type="date"]').datepicker('enable');
            jQuery('[type="date"]').datepicker({ 'dateFormat':'yy-mm-dd'});
          }

          else
            console.log(3);
             // do nothing with datepicker?
  });


  // enable a timepicker
  Drupal.behaviors.enableTimePicker = {
    attach: function (context) {
      jQuery('[type="time"]').jonthorntonTimepicker({'timeFormat':'H:i:s'});
    }
  };

})(jQuery, Drupal);
