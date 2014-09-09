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
			var newRow = $('.new').load(p);
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
		$('#subscriptions').on('click', '.add', function() {
			var self = $(this);
			var action = {action: 'add'};
			self.loadForm('views/partials/_subscription_form.php', action);
		});

		/**
		 * Delete subscription.
		 */
		$('#subscriptions').on('click', '.delete', function() {
			var self = $(this);
			if (! window.confirm("Sei sicuro di voler eliminare questo iscritto?") )
				return false;
			var id = self.closest('tr').find('td').attr('id');
			var data = {
				utente: id,
				corso: _GET.value,
				action: 'delete'
			};
			$.post('ajax/subscription.php', data);
			self.removeRow();
		});

		/**
		 * Remove subscription form.
		 */
		$('#subscriptions').on('click', '.cancel', function() {
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
				data.push(_GET);
				data.push({name: 'action', value: 'add'});
				console.log(data);
				$.post('ajax/subscription.php', data, function() {
					var data = that.serializeArray();
					self.loadRow('views/partials/_subscription_details.php', data);
					self.removeRow();
				} ).error( function() {
					self.closest('tr').addClass('error');
				});
			}).submit();
		});
	});
})(jQuery);