<?php
/**
 * Render KYSS Subscription details.
 *
 * Note: this file generates the details to be put in the
 * subscriptions list table. Therefore it is different from
 * all the other *_details partials.
 *
 * $subscription is a KYSS_Subscription object defined in
 * _subscription_table.php.
 *
 * @package KYSS
 * @subpackage Partials
 * @since  0.13.0
 */
require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/load.php' );

if ( ! isset( $user ) ) {
	$user = KYSS_User::get_user_by('id',$_REQUEST['utente']);
}
?>

<td><?php echo $user->nome ?></td>
<td><?php echo $user->cognome ?></td>
<td>
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="utente" value="<?php echo $user->ID; ?>">
	<a class="edit" title="Modifica">
		<span class="dashicons dashicons-edit"></span>
	</a>
</td>