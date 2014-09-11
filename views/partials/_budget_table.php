<?php
/**
 * Render KYSS Budget table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( isset( $_GET['q'] ) )
	$budgets = KYSS_Budget::search( $_GET['q'] );
else
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

<?php if ( strpos( $_SERVER['PHP_SELF'], 'documents' ) === false ) search_form(); ?>

<?php
if ( ! empty( $budgets ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Tipo</th>
			<th>Periodo</th>
			<th>Stato</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $budgets as $budget ) : ?>
		<tr>
			<td><?php echo $budget->tipo; ?></td>
			<td><?php echo ( isset( $budget->mese ) ? $budget->mese : '' ) . ' ' . ( isset( $budget->anno ) ? $budget->anno : '' ); ?></td>
			<td><?php switch( $budget->approvato ) {
						case '1':
							echo 'Approvato';
							break;
						case '0':
							echo 'Non approvato';
							break;
						case '':
						default:
							echo 'Nessun giudizio';
							break;
					} 
			?></td>
			<td>
				<a href="<?php echo get_site_url( 'budgets.php?action=view&id=' . $budget->ID ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'budgets.php?action=edit&id=' . $budget->ID ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono bilanci da visualizzare.';
endif;
?>