/**
 * The main KYSS JavaScript file.
 *
 * @project KYSS
 */

(function($) {
	$.fn.extend({
		/**
		 * Load table row with jQuery Ajax.
		 *
		 * @param {string} p The url to load content from.
		 */
		loadRow: function(p) {
			var row = $(this).closest('tr');
			var form = $('.new').load(p);
			row.before(form);
			$('.new').removeClass('new');
			row.after('<tr class="new"></tr>');
		}
	});

	$(function() {
		/**
		 * Add subscription form.
		 */
		$('#subscriptions').on('click', '#add-subscription', function() {
			var self = $(this);
			self.loadRow('views/partials/_subscription_form.php');
		});

		/**
		 * Remove subscription form.
		 */
	});
})(jQuery);