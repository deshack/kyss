<?php
/**
 * Render KYSS Practices, Reports and Budgets table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */
?>

<h1 class="page-title">Documenti</h1>

	<div class="row">
		<div class="medium-12 columns">
			<?php require( VIEWS . '/partials/_practice_table.php' ); ?>
		</div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
			<?php require( VIEWS . '/partials/_report_table.php' ); ?>
		</div>

		<div class="medium-12 columns">
			<?php require( VIEWS . '/partials/_budget_table.php' ); ?>
		</div>
	</div>