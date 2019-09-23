(function ($) {
  'use strict';
  ///
  /// create a Drupal behaviour
  /// https://www.drupal.org/docs/8/api/javascript-api/javascript-api-overview
  ///

  //$( document ).ready( function() {
    Drupal.behaviors.auto_end_event_date = {
      attach: function (context) {
        var start_date_selector = '[data-drupal-selector="edit-field-start-date-0-value-date"]';
        var end_date_selector = '[data-drupal-selector="edit-field-end-date-0-value-date"]';
        var start_time_selector = '[data-drupal-selector="edit-field-start-date-0-value-time"]';
        var end_time_selector = '[data-drupal-selector="edit-field-end-date-0-value-time"]';
        $(start_date_selector).on('focusout',function() {
          if(!$(end_date_selector).val()) {
            $(end_date_selector).val($(this).val());
          }
        });
        $(start_time_selector).on('focusout',function() {
          if(!$(end_time_selector).val()) {
            var valuestart = $(start_time_selector).val();
            var valueend = moment(valuestart,'HH:mm:ss').add('2','hours').format('HH:mm');
            console.log(valuestart);
            console.log(valueend);
            //var time_end_string = valueend.hours() + ':' +  valueend.minutes() + ':' + valueend.seconds();
            //console.log(time_end_string);
            $(end_time_selector).val(valueend);
          }
        });
      }
    };
  //});
})(jQuery);
