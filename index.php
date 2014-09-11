<?php
/**
 * Front to the KYSS application.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.1.0
 */

require_once( 'load.php' );

$hook->add( 'kyss_title', function( $title ) {
	return $title . ' &rsaquo; ' . get_option( 'sitename' );
});

get_header();

get_sidebar();
?>

<div class="row">
	<div class="medium-10 medium-offset-1 columns end">
		<div class="panel">
			<h3>Benvenuto <?php echo $current_user->nome; ?>!</h3>
			<p>Di seguito trovi alcune informazioni che ti possono tornare utili.</p>
		</div>
	</div>
</div>

<ul class="medium-block-grid-2 large-block-grid-3">
	<?php $hook->run('dashboard_widgets'); ?>
</ul>

<?php
get_footer();