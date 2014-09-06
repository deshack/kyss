<?php
/**
 * Render KYSS Report details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $prot ) ) {
	$message = 'Verbale non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$report = KYSS_Report::get( $prot );
if ( isset( $report ) )
	$meeting = KYSS_Meeting::get_meeting_by_id( $report->riunione );
?>

<h1 class="page-title">
	Dettagli verbale <small><?php echo $report->protocollo; ?></small>
</h1>

<div class="row">
	<div class="medium-6 columns">
		<dl>
			<dt>Riunione</dt>
			<dd><?php echo ( isset( $meeting->nome ) ? $meeting->nome : '' ) . ' (' . date( 'd/m/Y', strtotime( $meeting->data_inizio ) ) . ')'; ?>
			</dd>
		</dl>
	</div>
</div>
<fieldset>
	<legend>Contenuto</legend>
	<div><?php echo $report->contenuto; ?></div>
</fieldset>

<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'reports.php?action=edit&prot=' . $prot ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<?php back_button(); ?>
		</div>
	</div>
</footer>