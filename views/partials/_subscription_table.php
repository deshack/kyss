<?php
/**
 * Render KYSS Subscriptions table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

$hook->add( 'kyss_footer', function() {
?>
<script type="text/javascript">
_GET = {
	name: "corso",
	value: "<?php echo $_GET['id']; ?>"
};
</script>
<?php
});

$subscriptions = KYSS_Subscription::get_list('corso', $id);
?>

<h2>Iscritti</h2>

<?php
if ( ! empty( $subscriptions ) ) : ?>
<table id="subscriptions">
	<thead>
		<tr>
			<th>Utente</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $subscriptions as $subscription ) {
			echo '<tr>';
			$user = KYSS_User::get_user_by( 'id', $subscription->utente );
			include( VIEWS . '/partials/_subscription_details.php' );
			echo '</tr>';
		} ?>
		<tr>
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