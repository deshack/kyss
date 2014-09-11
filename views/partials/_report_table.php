<?php
/**
 * Render KYSS Report table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( isset( $_GET['q'] ) )
	$reports = KYSS_Report::search( $_GET['q'] );
else
	$reports = KYSS_Report::get_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Report::get_list()` adds a NULL element.
if ( is_array( $reports ) ) 
	$reports = array_filter( $reports );
?>

<h1>Verbali
	<small><a href="<?php echo get_site_url( 'reports.php?action=add'); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php if ( strpos( $_SERVER['PHP_SELF'], 'documents' ) === false ) search_form(); ?>

<?php
if ( ! empty( $reports ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Protocollo</th>
			<th>Data</th>
			<th>Azioni</th>	
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $reports as $report ) : ?>
		<tr>
			<td><?php echo $report->protocollo; ?></td>
			<td><?php 
				$meeting = KYSS_Meeting::get_meeting_by_id( $report->riunione );
				echo date( 'd/m/Y', strtotime( $meeting->data_inizio ) );
			 ?></td>
			<td>
				<a href="<?php echo get_site_url( 'reports.php?action=view&prot=' . $report->protocollo ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'reports.php?action=edit&prot=' . $report->protocollo ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono verbali da visualizzare.';
endif;
?>