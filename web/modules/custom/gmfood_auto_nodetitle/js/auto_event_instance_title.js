(function ($) {
  'use strict';
  Drupal.behaviors.popuptag_dialog = {
    attach: function (context) {
	var date_selector = "edit-field-event-dates-0-inline-entity-form-field-event-date-0-value-date";
        var title_selector = "edit-field-event-dates-0-inline-entity-form-title-0-value";
	var main_title_selector = "edit-title-0-value";
        var inline_form_selector = 'edit-field-event-dates-wrapper';
	$("[data-drupal-selector='" + date_selector + "']").on('change', generateInlineNodeTitle);

	jQuery("[data-drupal-selector='" + main_title_selector + "']").on('change', triggerGenerateAll);
	jQuery('.field--name-field-inline-event-date').each(function (index, value) { 
		jQuery(this).on('change', generateInlineNodeTitle);
		/*
		console.log('test1.' + index); 
		console.log(jQuery(this).find('input').val());
		var fieldset= jQuery(this).parentsUntil('fieldset');
		console.log(fieldset);
		var inline_event_title = fieldset.find('[data-drupal-selector^="edit-field-event-dates-"][data-drupal-selector$="-inline-entity-form-title-0-value"]');
		inline_event_title.val('bleh');
		*/
	});
	
	function triggerGenerateAll() {
		 jQuery('.field--name-field-inline-event-date').each(function (index, value) {
			jQuery(this).trigger("change");
		});
	}
	function generateInlineNodeTitle(e) {
		var fieldset = $(this).parentsUntil('fieldset');
                var date_val = ($(this).find('input').first().val());
		var inline_title = fieldset.find('[data-drupal-selector^="edit-field-event-dates-"][data-drupal-selector$="-inline-entity-form-title-0-value"]');
                var main_title_val = $("[data-drupal-selector='" + main_title_selector + "'").val();
                $(inline_title).val(date_val + ' ' + main_title_val);
	}
	triggerGenerateAll();
    }
  };
})(jQuery);
