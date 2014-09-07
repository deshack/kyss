/**
 * The main KYSS JavaScript file.
 *
 * @project KYSS
 */

(function($) {
	$.fn.extend({
		/**
		 * Load table row form with jQuery Ajax.
		 *
		 * @param {string} p The url to load content from.
		 */
		loadForm: function(p) {
			var row = $(this).closest('tr');
			var newRow = $('.new').load(p, row.find('input').serializeArray());
			row.before(newRow);
			$('.new').removeClass('new');
			row.after('<tr class="new"></tr>');
		},

		/**
		 * Load table row with entry with jQuery Ajax.
		 *
		 * @param {string} p The URL to load content from.
		 */
		loadRow: function(p, data) {
			var row = $(this).closest('tr');
			var newRow = $('.new').load(p, data );
			row.before(newRow);
			$('.new').removeClass('new');
			row.after('<tr class="new"></tr>');
		},

		/**
		 * Remove table row.
		 */
		removeRow: function() {
			var row = $(this).closest('tr');
			row.remove();
		}
	});

	$(function() {
		/**
		 * Add subscription form.
		 */
		$('#subscriptions').on('click', '#add-subscription', function() {
			var self = $(this);
			self.loadForm('views/partials/_subscription_form.php');
		});

		/**
		 * Edit subscription.
		 */
		$('#subscriptions').on('click', '.edit', function() {
			var self = $(this);
			self.loadForm('views/partials/_subscription_form.php');
		});

		/**
		 * Remove subscription form.
		 */
		$('#subscriptions').on('click', '.remove', function() {
			var self = $(this);
			self.removeRow();
		});

		/**
		 * Save subscription form data.
		 */
		$('#subscriptions').on('click', '.submit', function() {
			var self = $(this);
			self.closest('tr').find('form').submit(function(e){
				e.preventDefault();
				var that = $(this);
				var data = that.serializeArray();
				$.post('ajax/subscription.php', data, function() {
					var data = that.serializeArray();
					self.loadRow('views/partials/_subscription_details.php', data);
					self.removeRow();
				} ).error( function() {
					self.closest('tr').addClass('error');
					self.delay(500).removeRow();
				});
			}).submit();
		});
	});
})(jQuery);