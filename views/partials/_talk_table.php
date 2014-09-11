<?php
/**
 * Render KYSS Talk table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

if ( isset( $_GET['q'] ) )
	$talks = KYSS_Talk::search( $_GET['q'] );
elseif ( ! empty( $id ) )
	$talks = KYSS_Talk::get_list( $id, 'ASC' );
else
	$talks = KYSS_Talk::get_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Talk::get_list()` adds a NULL element.
if ( is_array( $talks ) )
	$talks = array_filter( $talks ); 
?>

<h1>Talk
	<small><a href="<?php echo get_site_url( 'talks.php?action=add' ); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php if ( strpos( $_SERVER['PHP_SELF'], 'talks' ) !== false ) search_form(); ?>

<?php
if ( ! empty( $talks ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Titolo</th>
			<th>Data</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $talks as $talk ) : ?>
		<tr>
			<td><?php echo isset( $talk->titolo ) ? $talk->titolo : ''; ?></td>
			<td><?php echo isset( $talk->data ) ? $talk->data : ''; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'talks.php?action=view&id=' . $talk->ID ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'talks.php?action=edit&id=' . $talk->ID ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono talk da visualizzare.';
endif;