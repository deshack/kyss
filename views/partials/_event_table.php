<?php
/**
 * Render KYSS Events, Meetings and Courses table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.11.0
 */
?>

<h1 class="page-title">Eventi</h1>

<?php
require( VIEWS . '/partials/_meeting_table.php' );
?>

<?php
require( VIEWS . '/partials/_course_table.php' );
?>

<?php
require( VIEWS . '/partials/_other_event_table.php' );
