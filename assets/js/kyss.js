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
		loadForm: function(p, action) {
			var row = $(this).closest('tr');
			var newRow = $('.new').load(p, action);
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
			var action = {action: 'form'};
			self.loadForm('subscriptions.php', action);
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
			$.post('ajax-subscription.php', data);
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
				var utente = data[0].value;
				console.log(utente);
				var users = self.closest('tbody').find('td');
				users.each(function() {
					var id = $(this).attr("id");
					console.log(id);
					if ( typeof id != 'undefined' && id == utente ) {
						alert( "Questo utente è già iscritto!" );
						iscritto = true;
						return false;
					}
				});
				if ( typeof iscritto != 'undefined' && iscritto ) {
					self.removeRow();
					return false;
				}
				data.push(_GET);
				data.push({name: 'action', value: 'add'});
				$.post('ajax-subscription.php', data, function() {
					var data = that.serializeArray();
					data.push({name: 'action', value: 'add'});
					self.loadRow('subscriptions.php', data);
					self.removeRow();
				} ).error( function() {
					self.closest('tr').addClass('error');
				});
			}).submit();
		});

		/**
		 * Delete lesson.
		 */
		$('#lessons').on('click', '.delete', function() {
			var self = $(this);
			if (! window.confirm("Sei sicuro di voler eliminare questa lezione?") )
				return false;
			var date = self.closest('tr').find('time').text();
			var data = {
				data: date,
				corso: _GET.value,
				action: 'delete'
			};
			$.post('ajax-lesson.php', data);
			self.removeRow();
		});
	});
})(jQuery);