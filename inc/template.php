<?php
/**
 * KYSS General Template.
 *
 * Holds a number of general template functions.
 *
 * @package KYSS
 * @subpackage  Template
 * @since  0.11.0
 */

// Add general-purpose meta tags to KYSS head.
global $hook;
$metas = array(
	'viewport_meta',
	'charset_meta'
);
foreach ( $metas as $meta )
	$hook->add( 'kyss_head', $meta );
unset( $metas );

/**
 * Display a noindex meta tag.
 *
 * Outputs a @link(noindex, http://en.wikipedia.org/wiki/Noindex) meta tag that tells
 * web robots not to index the page content. Typical usage is as a kyss_head callback.
 * @example
 * ```
 * $hook->add( 'kyss_head', 'no_robots' );
 * ```
 *
 * @since  0.11.0
 */
function no_robots() {
	echo '<meta name="robots" content="noindex,follow" />' . "\n";
}

/**
 * Display a viewport meta tag.
 *
 * Outputs a responsive meta tag to improve the experience on small-width devices.
 *
 * @since  0.11.0
 */
function viewport_meta() {
	echo '<meta name="viewport" content="width=device-width">' . "\n";
}

/**
 * Display a HTML5 charset meta tag.
 *
 * @since  0.11.0
 */
function charset_meta() {
	echo '<meta charset="utf-8">' . "\n";
}

/**
 * Display page header.
 *
 * @since  0.11.0
 */
function get_header() {
	require( VIEWS . 'header.php' );
}

/**
 * Display sidebar.
 *
 * @since  0.11.0
 */
function get_sidebar() {
	require( VIEWS . 'sidebar.php' );
}

/**
 * Display page footer.
 *
 * @since 0.11.0
 */
function get_footer() {
	require( VIEWS . 'footer.php' );
}

/**
 * Return HTML for the `value` attribute.
 *
 * Note: the output starts with a space.
 *
 * @since  0.11.0
 *
 * @param  string $value The `value` attribute content.
 * @return  string HTML code for the `value` attribute.
 */
function get_value_html( $value ) {
	return ' value="' . $value . '"';
}

/**
 * Output the HTML checked attribute.
 *
 * Compares the first two arguments. If identical, marks as checked.
 *
 * @since  0.12.0
 *
 * @param  mixed $checked One of the values to compare.
 * @param  mixed $current Optional. The other value to compare, if not just true.
 * Default <true>.
 * @param  bool $echo Optional. Whether to echo or just return the string. Default
 * <true>.
 * @return  string HTML checked attribute or empty string.
 */
function checked( $checked, $current = true, $echo = true ) {
	return _checked_helper( $checked, $current, $echo, 'checked' );
}

/**
 * Output the HTML selected attribute.
 * 
 * Compares the first two arguments. If identical, marks as selected.
 *
 * @since  0.12.0
 *
 * @param  mixed $selected One of the values to compare.
 * @param  mixed $current Optional. The other value to compare, if not just true.
 * Default <true>.
 * @param  bool $echo Optional. Whether to echo or just return the string. Default
 * <true>.
 * @return  string HTML select attribute or empty string.
 */
function selected( $selected, $current = true, $echo = true ) {
	return _checked_helper( $selected, $current, $echo, 'selected' );
}

/**
 * Output the HTML disabled attribute.
 *
 * Compares the first two arguments. If identical, marks as disabled.
 *
 * @since  0.12.0
 *
 * @param  mixed $disabled One of the values to compare.
 * @param  mixed $current Optional. The other value to compare, if not just
 * true. Default <true>.
 * @param  bool $echo Optional. Whether to echo or just return the string. Default
 * <true>.
 * @return  string HTML disabled attribute or empty string.
 */
function disabled( $disabled, $current = true, $echo = true ) {
	return _checked_helper( $disabled, $current, $echo, 'disabled' );
}

/**
 * Private helper function for checked, selected, and disabled.
 *
 * Compares the first two arguments and if identical marks as $type.
 *
 * @since  0.12.0
 * @access private
 *
 * @param  mixed $helper One of the values to compare.
 * @param  mixed $current The other value to compare, if not just true.
 * @param  bool $echo Whether to echo or just return the string.
 * @param  string $type The type of check we are doing. Accepts <checked>,
 * <selected>, <disabled>.
 * @return  string HTML attribute or empty string.
 */
function _checked_helper( $helper, $current, $echo, $type ) {
	if ( (string) $helper === (string) $current )
		$result = " $type='$type'";
	else
		$result = '';

	if ( $echo )
		echo $result;

	return $result;
}

/**
 * Render back button.
 *
 * @since  0.12.0
 */
function back_button() {
?>
<a href="javascript:history.back()" class="button">
	<span class="dashicons dashicons-undo"></span>
</a>
<?php
}

/**
 * Render field error description.
 *
 * @since  0.12.0
 *
 * @param  string $message Custom error message.
 */
function field_error( $message = '' ) {
	if ( empty( $message ) )
		$message = 'Quasto campo non pu&ograve; essere vuoto.';
?>
<small class="error"><?php echo $message; ?></small>
<?php
}

/**
 * Render alert box with custom message.
 *
 * @since  0.13.0
 *
 * @param  string $message Custom message.
 * @param  string $classes Optional. Additional classes.
 */
function alert_box( $message, $classes = '' ) {
?>
<div data-alert class="alert-box <?php echo $classes; ?>">
	<?php echo $message; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php
}

/**
 * Render success alert box with custom message.
 *
 * @since  0.13.0
 *
 * @param  string $message Custom message.
 */
function alert_success( $message ) {
	alert_box( $message, 'success' );
}

/**
 * Render error alert box with custom message.
 *
 * @since  0.13.0
 *
 * @param  string $message Custom message.
 */
function alert_error( $message ) {
	alert_box( $message, 'alert' );
}

/**
 * Render info alert box with custom message.
 *
 * @since  0.13.0
 *
 * @param  string $message Custom message.
 */
function alert_info( $message ) {
	alert_box( $message, 'info' );
}

/**
 * Render alert box with information about save action.
 *
 * @since  0.13.0
 *
 * @param  misc $error Entity to check errors against.
 */
function alert_save( $error ) {
	if ( ! isset( $_GET['save'] ) || $_GET['save'] != 'true' )
		return;

	if ( is_kyss_error( $error ) ) {
		$message = $error->get_error_message();
		$message = preg_replace( '/Duplicate entry \'[0-9]+\-/', '', $message );
		$message = preg_replace( '/\' for key \'PRIMARY\'/', '', $message );
		alert_error( $message );
	}
	elseif ( ! $error )
		alert_error( 'Salvataggio fallito.' );
	else
		alert_success( 'Salvataggio effettuato con successo.' );
}

/**
 * Render search form.
 *
 * @since  0.13.0
 */
function search_form() {
?>
<form method="get" action="">
	<input type="search" id="q" name="q" placeholder="Cerca&hellip;"<?php echo isset( $_GET['q'] ) ? get_value_html( $_GET['q'] ) : ''; ?>>
</form>
<?php
if ( isset( $_GET['q'] ) )
	alert_info( "Risultati della ricerca per: <b>{$_GET['q']}</b>." );
}

/**
 * Print a list of upcoming events.
 *
 * @since  0.13.0
 */
function upcoming_events() {
	$events = KYSS_Event::get_upcoming();

	if ( is_kyss_error( $events ) )
		return false;
	if ( ! $events )
		return;
?>
<li>
	<h4 class="text-center">Prossimi eventi</h4>
	<table>
		<thead>
			<tr>
				<th>Nome</th>
				<th>Inizio</th>
				<th>Fine</th>
			</tr>
		</thead>
		<tbody>
	<?php
		foreach ( $events as $event ) : ?>
			<tr>
				<td>
					<a href="<?php echo get_site_url( 'other-events.php?action=view&id=' . $event->ID ); ?>" title="Dettagli">
						<?php echo isset( $event->nome ) ? $event->nome : ''; ?>
					</a>
				</td>
				<td><?php echo isset( $event->data_inizio ) ? $event->data_inizio : ''; ?></td>
				<td><?php echo isset( $event->data_fine ) ? $event->data_fine : ''; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</li>

<?php
}
$hook->add( 'dashboard_widgets', 'upcoming_events' );

/**
 * Print list of upcoming courses.
 *
 * @since  0.13.0
 */
function upcoming_courses() {
	$courses = KYSS_Course::get_upcoming();

	if ( is_kyss_error( $courses ) ) {
		trigger_error( $courses->get_error_message(), E_USER_WARNING );
		return false;
	}
	if ( ! $courses )
		return;
?>
<li>
	<h4 class="text-center">Prossimi corsi</h4>
	<table>
		<thead>
			<tr>
				<th>Nome</th>
				<th>Data inizio</th>
				<th>Data fine</th>
				<th>Livello</th>
			</tr>
		</thead>
		<tbody>
	<?php
		foreach ( $courses as $course ) : ?>
			<tr>
				<td>
					<a href="<?php echo get_site_url( 'courses.php?action=view&id=' . $course->ID ); ?>" title="Dettagli">
						<?php echo isset( $course->nome ) ? $course->nome : ''; ?>
					</a>
				</td>
				<td><?php echo isset( $course->data_inizio ) ? date( 'd/m/Y', strtotime( $course->data_inizio ) ) : ''; ?></td>
				<td><?php echo isset( $course->data_fine ) ? date( 'd/m/Y', strtotime( $course->data_fine ) ) : ''; ?></td>
				<td><?php echo isset( $course->livello ) ? $course->livello : ''; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</li>
<?php
}
$hook->add('dashboard_widgets', 'upcoming_courses');

/**
 * Print list of upcoming meetings, according to user group.
 *
 * @since  0.13.0
 */
function upcoming_meetings() {
	$meetings = KYSS_Meeting::get_upcoming();

	if ( is_kyss_error( $meetings ) ) {
		trigger_error( $meetings->get_error_message(), E_USER_WARNING );
		return false;
	}
	if ( ! $meetings )
		return;
?>
<li>
	<h4 class="text-center">Prossime riunioni</h4>
	<table>
		<thead>
			<tr>
				<th>Nome</th>
				<th>Data</th>
				<th>Ora</th>
				<th>Tipo</th>
			</tr>
		</thead>
		<tbody>
	<?php
		foreach ( $meetings as $meeting ) : ?>
			<tr>
				<td>
					<a href="<?php echo get_site_url( 'meetings.php?action=view&id=' . $meeting->ID ); ?>" title="Dettagli">
						<?php echo isset( $meeting->nome ) ? $meeting->nome : ''; ?>
					</a>
				</td>
				<td>
					<?php echo isset( $meeting->data_inizio ) ? date( 'd/m/Y', strtotime( $meeting->data_inizio ) ) : ''; ?>
				</td>
				<td>
					<?php echo isset( $meeting->ora_inizio ) ? date( 'H:i', strtotime( $meeting->ora_inizio ) ) : '';
					?> - <?php echo isset( $meeting->ora_fine ) ? date( 'H:i', strtotime( $meeting->ora_fine ) ) : ''; ?>
				</td>
				<td><?php echo isset( $meeting->tipo ) ? $meeting->tipo : ''; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</li>
<?php
}
$hook->add('dashboard_widgets', 'upcoming_meetings');