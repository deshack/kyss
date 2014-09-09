<?php
/**
 * Render KYSS Lesson table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.13.0
 */

$lessons = KYSS_Lesson::get_list( $id );
?>

<h2>Lezioni<a href="<?php echo get_site_url( 'lessons.php?action=add&course='.$id ); ?>" title="Nuova"><span class="dashicons dashicons-plus"></span></a></h2>

<?php
if ( ! empty( $lessons ) ) : ?>
<table id="lessons">
	<thead>
		<tr>
			<th>Argomento</th>
			<th>Data e ora</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $lessons as $lesson ) {
			include( VIEWS . '/partials/_lesson_details.php' );
		} ?>
		<tr class="new"></tr>
	</tbody>
</table>
<?php
endif;