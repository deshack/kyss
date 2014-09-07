<?php
/**
 * Render KYSS Subscription add form.
 *
 * Note: this file generates the form to be rendered in the
 * subscriptions list table. Therefore it is different from
 * all the other *_form partials.
 *
 * $subscription is a KYSS_Subscription object defined in
 * _subscription_table.php.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/load.php' );

$users = KYSS_User::get_users_list();

?>

<td>Aggiungi iscritto</td>
<td>
	<form>
		<input type="hidden" name="action" value="<?php echo $action; ?>">
		<input type="hidden" name="corso" value="<?php echo $_POST['corso']; ?>">
		<select name="utente">
		<?php foreach ( $users as $user ) : ?>
			<option value="<?php echo $user->ID; ?>"<?php echo isset( $subscription->utente ) ? selected( $subscription->utente, $user->ID, false ) : ''; ?>>
				<?php echo $user->nome . ' ' . $user->cognome; ?>
			</option>
		<?php endforeach; ?>
		</select>
	</form>
</td>
<td>
	<a class="submit" title="Salva">
		<span class="dashicons dashicons-yes"></span>
	</a>
	<a class="remove" title="Annulla">
		<span class="dashicons dashicons-no"></span>
	</a>
</td>