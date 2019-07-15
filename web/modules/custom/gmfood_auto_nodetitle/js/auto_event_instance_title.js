(function ($) {
  'use strict';
  Drupal.behaviors.popuptag_dialog = {
    attach: function (context) {
	var date_selector = "edit-field-event-dates-0-inline-entity-form-field-event-date-0-value-date";
        var title_selector = "edit-field-event-dates-0-inline-entity-form-title-0-value";
	var main_title_selector = "edit-title-0-value";
        var inline_form_selector = 'edit-field-event-dates-wrapper';
	$("[data-drupal-selector='" + date_selector + "']").on('change', generateInlineNodeTitle);

	jQuery('[data-drupal-selector^="edit-field-event-dates-"][data-drupal-selector$="-inline-entity-form"]').each(function (index, value) { 
		console.log(index); 
	});
 
	function generateInlineNodeTitle(e) {
	        console.log('test');
                console.log($("[data-drupal-selector='" + date_selector + "']").val());
                var date_val = ($("[data-drupal-selector='" + date_selector + "']").val());
                var main_title_val = $("[data-drupal-selector='" + main_title_selector + "'").val();
                $("[data-drupal-selector='" + title_selector + "']").val(date_val + ' ' + main_title_val);

	}
    }
  };
})(jQuery);
