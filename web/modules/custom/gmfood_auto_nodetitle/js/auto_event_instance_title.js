(function ($) {
  'use strict';

  ///
  /// create a Drupal behaviour
  /// https://www.drupal.org/docs/8/api/javascript-api/javascript-api-overview
  ///
  Drupal.behaviors.auto_node_title = {
    attach: function (context) {

        //
        // get the form item HTML elements from the event edit page
        //
	      var date_selector = "edit-field-event-dates-0-inline-entity-form-field-event-date-0-value-date";
        var title_selector = "edit-field-event-dates-0-inline-entity-form-title-0-value";
	      var main_title_selector = "edit-title-0-value";
        var inline_form_selector = 'edit-field-event-dates-wrapper';

        //
        // when the event event_dates date changes, update the event_date title
        //
	      $("[data-drupal-selector='" + date_selector + "']").on('change', generateInlineNodeTitle);

        //
        // when the event title changes, update all event_date titles
        //
	      jQuery("[data-drupal-selector='" + main_title_selector + "']").on('change', triggerGenerateAll);
        jQuery('.field--name-field-inline-event-date').each(function (index, value) {
		      jQuery(this).on('change', generateInlineNodeTitle);
	      });

        //
        // Regenerates all event_date titles
        //
	      function triggerGenerateAll() {
	     	  jQuery('.field--name-field-inline-event-date').each(function (index, value) {
			      jQuery(this).trigger("change");
		      });
    	  }

        //
        // Regerates a single event_date title when an inline event_date item is changed
        //
	      function generateInlineNodeTitle(e) {
          // find the nearest fieldset item in the DOM heirachy
		      var fieldset = $(this).parentsUntil('fieldset');
          // get the date element
          var date_val = ($(this).find('input').first().val());
          // get the event_date title
		      var inline_title = fieldset.find('[data-drupal-selector^="edit-field-event-dates-"][data-drupal-selector$="-inline-entity-form-title-0-value"]');
          // get the event title
          var main_title_val = $("[data-drupal-selector='" + main_title_selector + "']").val();
          // set the event_date title with a new value
          $(inline_title).val(date_val + ' ' + main_title_val);
	      }

        //
        // hides the event_date node title (because we are autogenerating it, doesn't need to be manually edited)
        function hideInlineNodeTitle() {
          jQuery('.form-item-field-event-dates-0-inline-entity-form-title-0-value').css('display','none');
        }
        //
        // initialise the page by generating all event_date titles and hiding the event_date titles
        //
	      triggerGenerateAll();
        hideInlineNodeTitle();
      }
  };
})(jQuery);
