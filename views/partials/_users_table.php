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
if( is_array( $users ) )
	$users = array_filter( $users ); ?>

<h1 class="page-title">Utenti<a href="<?php echo get_site_url( 'users.php?action=add'); ?>" title="Aggiungi nuovo">
	<span class="dashicons dashicons-plus"></span>
</a></h1>

<?php if ( ! empty( $users ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome e cognome</th>
			<th>Email</th>
			<th>Telefono</th>
			<th>Gruppo</th>
			<th>Azioni</th>
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
			<td>
				<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $user->ID ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'users.php?action=edit&id=' . $user->ID ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
endif;