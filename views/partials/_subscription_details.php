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
<td id="<?php echo $user->ID; ?>">
	<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $user->ID ); ?>">
		<?php echo $user->nome . ' ' . $user->cognome; ?>
	</a>
</td>
<td>
	<a class="delete" title="Elimina">
		<span class="dashicons dashicons-trash"></span>
	</a>
</td>