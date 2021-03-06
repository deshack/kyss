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

	<div class="row">
		<div class="medium-12 columns">
			<?php require( VIEWS . '/partials/_other-event_table.php' ); ?>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<?php require( VIEWS . '/partials/_meeting_table.php' ); ?>
		</div>

		<div class="medium-6 columns">
			<?php require( VIEWS . '/partials/_course_table.php' ); ?>
		</div>
	</div>
