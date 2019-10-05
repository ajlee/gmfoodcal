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
    var start_desc_selector = '#edit-field-start-date-0--description';
    var end_desc_selector = '#edit-field-end-date-0--description';


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

    // action when the page loads - set default values of start time
    if ( start_time == '' || start_time == '00:00:00') {
      $(start_time_selector).val(dateTime);
    }

    // when start date / time changes set the end date / time
    // unless end date time has been edited - then do nothing
    $(start_container_selector).on('change',function() {
        // if end date hasn't been filled yet
      if(!end_date_clicked) {
        $(end_date_selector).val($(start_date_selector).val());
        end_date_clicked = true;
      }

      // when the end time hasn't been filled yet
      if(!end_time_clicked) {

        // set end time to be 2 hours after start time
        // unless end time has been edited then do nothing
        var valuestart = $(start_time_selector).val();
        var valueend = moment(valuestart,'HH:mm:ss').add('2','hours').format('HH:mm');
        $(end_time_selector).val(valueend);
        end_time_clicked = true;
      }

      compare_date_times();
    });

    // when end date / time have been changed set this value to true
    $(end_date_clicked).on('focus', function() {
      end_date_clicked = true;
    });
    $(end_time_selector).on('focus', function() {
      end_time_clicked = true;
    });
    $(end_container_selector).on('change',function() {
      compare_date_times();
    });

    // function to compare the time and date fields
    // add error helper classes when the date times are invalid
    function compare_date_times(){
      var start_date = moment($(start_date_selector).val());
      var end_date = moment($(end_date_selector).val());

      // clear errors first
      $(start_date_selector).removeClass('error');
      $(end_date_selector).removeClass('error');
      $(start_time_selector).removeClass('error');
      $(end_time_selector).removeClass('error');
      $(start_desc_selector).removeClass('form-item--error-message');
      $(end_desc_selector).removeClass('form-item--error-message');

      // is the start is AFTER the end date?
      //
      if (start_date > end_date) {
        $(start_date_selector).addClass('form-control error');
        $(end_date_selector).addClass('form-control error');
        $(start_desc_selector).addClass('form-item--error-message');
        $(end_desc_selector).addClass('form-item--error-message');
      }
      else {

        // compare the time fields
        var start_date_time = moment($(start_date_selector).val() + 'T' + $(start_time_selector).val(),moment.HTML5_FMT.DATETIME_LOCAL_SECONDS);
        var end_date_time = moment($(end_date_selector).val() + 'T' + $(end_time_selector).val(),moment.HTML5_FMT.DATETIME_LOCAL_SECONDS);

        // is the start date/time AFTER the end date/time?
        if (start_date_time >= end_date_time) {
          $(start_time_selector).addClass('form-control error');
          $(end_time_selector).addClass('form-control error');
          $(start_desc_selector).addClass('form-item--error-message');
          $(end_desc_selector).addClass('form-item--error-message');
        }
      }
    }
  });
})(jQuery);

