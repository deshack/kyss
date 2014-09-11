<?php
/**
 * Render KYSS Practice table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( isset( $_GET['q'] ) )
	$practices = KYSS_Practice::search( $_GET['q'] );
else
	$practices = KYSS_Practice::get_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Practice::get_list()` adds a NULL element.
if ( is_array( $practices ) ) 
	$practices = array_filter( $practices );
?>

<h1>Pratiche
	<small><a href="<?php echo get_site_url( 'practices.php?action=add'); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php if ( strpos( $_SERVER['PHP_SELF'], 'documents' ) === false ) search_form(); ?>

<?php
if ( ! empty( $practices ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Protocollo</th>
			<th>Utente</th>
			<th>Tipo</th>
			<th>Data</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $practices as $practice ) : ?>
		<tr>
			<td><?php echo $practice->protocollo?></td>
			<td><?php 
				$user = KYSS_User::get_user_by('id', $practice->utente);
				echo $user->nome . ' ' . $user->cognome; 
			?></td>
			<td><?php echo $practice->tipo; ?></td>
			<td><?php echo $practice->data; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'practices.php?action=view&prot=' . $practice->protocollo ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'practices.php?action=edit&prot=' . $practice->protocollo ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono pratiche da visualizzare.';
endif;
?>