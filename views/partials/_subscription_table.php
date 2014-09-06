<?php
/**
 * Render KYSS Subscriptions table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

$subscriptions = KYSS_Subscription::get_list('corso', $id);

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Subscription::get_list()` adds a NULL element.
if ( is_array( $subscriptions ) )
	$subscriptions = array_filter( $subscriptions ); 
?>

<h1>Iscrizioni
	<small><a href="#">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php
if ( ! empty( $subscriptions ) ) : ?>
<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Cognome</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $subscriptions as $subscription ) : 
		$user = KYSS_User::get_user_by('id', $subscription->utente);?>
		<tr>
			<td><?php echo $user->nome ?></td>
			<td><?php echo $user->cognome ?></td>
			<td>
				<a href="#" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td>
				<a href="#" title="Aggiungi">
					<span class="dashicons dashicons-plus"></span>
				</a>
			</td>
		</tr>
	</tbody>
</table>
<?php
else :
	echo 'Non ci sono iscirtti per questo corso.';
endif;