<?php
/**
 * Render KYSS Budget table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

$budgets = KYSS_Budget::get_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Budget::get_list()` adds a NULL element.
if ( is_array( $budgets ) ) 
	$budgets = array_filter( $budgets );
?>

<h1>Bilanci
	<small><a href="<?php echo get_site_url( 'budgets.php?action=add'); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php
if ( ! empty( $budgets ) ) : ?>

<table>
	<thead>
		<tr>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $budgets as $budget ) : ?>
		<tr>
			<td></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono bilanci da visualizzare.';
endif;
?>