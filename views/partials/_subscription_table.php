<?php
/**
 * Render KYSS Subscriptions table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

$subscriptions = KYSS_Subscription::get_list('corso', $id);
?>

<h1>Iscrizioni</h1>

<?php
if ( ! empty( $subscriptions ) ) : ?>
<table id="subscriptions">
	<thead>
		<tr>
			<th>Nome</th>
			<th>Cognome</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $subscriptions as $subscription ) {
			echo '<tr>';
			$user = KYSS_User::get_user_by('id', $subscription->utente);
			include( VIEWS . '/partials/_subscription_details.php' );
			echo '</tr>';
		} ?>
		<tr>
			<td>
				<input type="hidden" name="action" value="create">
			</td>
			<td>
				<input type="hidden" name="corso" value="<?php echo $course->ID; ?>">
			</td>
			<td>
				<a id="add-subscription" title="Aggiungi">
					<span class="dashicons dashicons-plus"></span>
				</a>
			</td>
		</tr>
		<tr class="new"></tr>
	</tbody>
</table>
<?php
else :
	echo 'Non ci sono iscirtti per questo corso.';
endif;