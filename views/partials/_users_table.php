<?php
/**
 * Render KYSS Users table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.11.0
 */

$users = KYSS_User::get_users_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_User::get_users_list()` adds a NULL element.
$users = array_filter( $users );

if ( ! empty( $users ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome e cognome</th>
			<th>Email</th>
			<th>Telefono</th>
			<th>Gruppo</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $users as $user ) : ?>
		<tr>
			<td><?php echo ( isset( $user->nome ) ? $user->nome : '' ) . ' ' . ( isset( $user->cognome ) ? $user->cognome : '' ); ?></td>
			<td><?php echo isset( $user->email ) ? $user->email : ''; ?></td>
			<td><?php echo isset( $user->telefono ) ? $user->telefono : ''; ?></td>
			<td><?php echo isset( $user->gruppo ) ? $user->gruppo : ''; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
endif;