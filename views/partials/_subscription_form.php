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

<td></td>
<td>
	<form>
		<div class="row">
			<div class="small-3 columns">
				<label for="utente" class="label-select right">
					<?php echo isset($_POST['utente']) ? 'Modifica iscritto' : 'Aggiungi iscritto'; ?>
				</label>
			</div>
			<div class="small-5 columns end">
				<select name="utente">
				<?php foreach ( $users as $user ) : ?>
					<option value="<?php echo $user->ID; ?>"<?php echo isset( $subscription->utente ) ? selected( $subscription->utente, $user->ID, false ) : ''; ?>>
						<?php echo $user->cognome . ' ' . $user->nome; ?>
					</option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
	</form>
</td>
<td>
	<a class="submit" title="Salva">
		<span class="dashicons dashicons-yes"></span>
	</a>
	<a class="cancel" title="Annulla">
		<span class="dashicons dashicons-no"></span>
	</a>
</td>