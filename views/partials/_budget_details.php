<?php
/**
 * Render KYSS Budget details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $id ) ) {
	$message = 'Bilancio non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$budget = KYSS_Budget::get( $id );
?>

<h1 class="page-title">
	Dettagli bilancio <small><?php echo ( isset( $budget->mese ) ? $budget->mese : $budget->tipo ) . ' ' . $budget->anno; ?></small>
</h1>

<div class="row">
	<div class="medium-3 columns">
		<dl>
			<dt>Cassa</dt>
			<dd><?php echo $budget->cassa . ' €'; ?>
			</dd>
		</dl>
	</div>
	<div class="medium-3 columns">
		<dl>
			<dt>Banca</dt>
			<dd>
				<?php echo $budget->banca . ' €'; ?>
			</dd>
		</dl>
	</div>
	<div class="medium-6 columns">
		<dl>
			<dt>Stato</dt>
			<dd><?php switch ( $budget->approvato ) {
					case '1':
						echo 'Approvato nel verbale ' . $budget->verbale;
						break;
					case '0':
						echo 'Non approvato nel verbale ' . $budget->verbale;
						break;
					case '':
					default:
						echo 'In attesa di valutazione';
						break;
					}?>
			</dd>
		</dl>
	</div>
</div>

<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'budgets.php?action=edit&id=' . $id ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<?php back_button(); ?>
		</div>
	</div>
</footer>