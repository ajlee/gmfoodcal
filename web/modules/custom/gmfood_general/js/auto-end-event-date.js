(function ($) {
  'use strict';
  //
  // javascript for the add node event page - custom date functionality
  //

  $( document ).ready( function() {

    // find out whether HTML5 dates are supported
    var isDateSupported = function () {
      var input = document.createElement('input');
      var value = 'a';
      input.setAttribute('type', 'date');
      input.setAttribute('value', value);
      return (input.value !== value);
    };



    // set up variables.
    var end_date_clicked = false;
    var end_time_clicked = false;
    var start_container_selector = '#edit-field-start-date-0';
    var end_container_selector = '#edit-field-end-date-0';
    var start_date_selector = '[data-drupal-selector="edit-field-start-date-0-value-date"]';
    var end_date_selector = '[data-drupal-selector="edit-field-end-date-0-value-date"]';
    var start_time_selector = '[data-drupal-selector="edit-field-start-date-0-value-time"]';
    var end_time_selector = '[data-drupal-selector="edit-field-end-date-0-value-time"]';

    // additional help if the date type is not supported
    if (!isDateSupported) {
      $(start_container_selector).find('label').removeClass('visually-hidden');
      $(start_container_selector).find('label').first().html('Date (in yyyy-mm-dd format)');

      $(end_container_selector).find('label').removeClass('visually-hidden');
      $(end_container_selector).find('label').first().html('Date (in yyyy-mm-dd format)');
    }

    // get value of start time field
    var start_time = $(start_time_selector).val();
    var start = moment();

    // sets value to the next hour at HH:00:00
    var dateTime = moment(start).add(1, "hours").format("HH") + ':00:00';
    console.log(dateTime);

    // action when the page loads - set default values of start time
    if ( start_time == '' || start_time == '00:00:00') {
      console.log('5');
      $(start_time_selector).val(dateTime);
    }

    // when start date / time changes set the end date / time
    // unless end date time has been edited - then do nothing
    $(start_container_selector).on('change',function() {
      if(!$(end_date_selector).val()) {
        if(!end_date_clicked) {
          $(end_date_selector).val($(start_date_selector).val());
        }
      }
      if(!end_time_clicked) {

        // set end time to be 2 hours after start time
        // unless end time has been edited then do nothing
        var valuestart = $(start_time_selector).val();
        var valueend = moment(valuestart,'HH:mm:ss').add('2','hours').format('HH:mm');
        $(end_time_selector).val(valueend);
      }
    });

    // when end date / time have been changed set this value to true
    $(end_date_clicked).on('focus', function() {
      end_date_clicked = true;
    });
    $(end_time_selector).on('focus', function() {
      end_time_clicked = true;
    });
  });
})(jQuery);
