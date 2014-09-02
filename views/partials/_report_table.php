<?php
/**
 * Render KYSS Report table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

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

<?php
if ( ! empty( $reports ) ) : ?>

<table>
	<thead>
		<tr>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $reports as $report ) : ?>
		<tr>
			<td></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono verbali da visualizzare.';
endif;
?>