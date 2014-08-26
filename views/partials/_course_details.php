<?php
/**
 * Render KYSS Courses details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $id ) ) {
	$message = 'Stai tentando di visualizzare i dettagli di una corso, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$course = KYSS_Course::get_course_by_id( $id );
?>

<h1 class="page-title">
	Dettagli corso <small><?php echo $course->nome; ?></small>
</h1>

<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Livello</dt>
			<dd><?php echo isset( $course->livello ) ? $course->livello : '-'; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Inizio</dt>
			<dd><?php echo isset( $course->data_inizio ) ? date( 'd/m/Y', strtotime( $course->data_inizio ) ) : ''; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Fine</dt>
			<dd><?php echo isset( $course->data_fine ) ? date( 'd/m/Y', strtotime( $course->data_fine ) ) : ''; ?></dd>
		</dl>
	</div>
</div>
<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>N° lezioni</dt>
			<dd><?php echo isset( $course->lezioni ) ? $course->lezioni : '-'; ?></dd>
			</dd>
		</dl>
	</div>
	<div class="medium-8 columns">
		<dl>
			<dt>Luogo</dt>
			<dd><?php echo isset( $course->luogo ) ? $course->luogo : '-'; ?></dd>
		</dl>
	</div>
</div>
<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'courses.php?action=edit&id=' . $id ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'courses.php' ); ?>" class="button">
				<span class="dashicons dashicons-undo"></span>
			</a>
		</div>
	</div>
</footer>