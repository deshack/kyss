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

<h2>Iscritti</h2>

<?php
if ( ! empty( $subscriptions ) ) : ?>
<table id="subscriptions">
	<thead>
		<tr>
			<th width="2em">#</th>
			<th>Utente</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$c = 0;
			foreach ( $subscriptions as $subscription ) {
				$c++;
				echo '<tr>';
				$user = KYSS_User::get_user_by( 'id', $subscription->utente );
				include( VIEWS . '/partials/_subscription_details.php' );
				echo '</tr>';
		} ?>
		<tr>
			<td></td>
			<td></td>
			<td>
				<a class="add" title="Aggiungi">
					<span class="dashicons dashicons-plus"></span>
				</a>
			</td>
		</tr>
		<tr class="new"></tr>
	</tbody>
</table>
<?php
endif;