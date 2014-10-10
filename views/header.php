<?php
/**
 * Render KYSS page header.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since 0.11.0
 */

global $hook, $current_user, $updates;

$title = 'KYSS';

/**
 * Filter page title.
 *
 * @since  0.11.0
 *
 * @param  string $title The default title.
 * @param  string $page The script currently being run.
 * @return  string The maybe filtered title.
 */
$title = $hook->run( 'kyss_title', $title, $_SERVER['PHP_SELF'] );

header( 'Content-Type: text/html; charset=utf-8' );
?>

<!doctype html>
<!--[if IE 8]>
<html class="ie8" lang="it">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html lang="it">
<head>
	<title><?php echo $title; ?></title>
	<?php kyss_css( 'dashicons', true ); ?>
	<?php kyss_css( 'jquery-ui', true ); ?>
	<?php kyss_css( 'jquery-ui-structure', true ); ?>
	<?php kyss_css( 'jquery-ui-theme', true ); ?>
	<?php kyss_css( 'kyss', true ); ?>
	<?php kyss_js( 'modernizr', true ); ?>
	<?php kyss_js( 'jquery', true ); ?>
	<?php kyss_js( 'jquery-ui', true ); ?>
	<?php kyss_js( 'datetimepicker', true ); ?>
	<?php kyss_js( 'datepicker-it', true ); ?>
	<?php kyss_js( 'fastclick', true ); ?>
	<?php kyss_js( 'foundation', true ); ?>
	<?php kyss_js( 'kyss', true ); ?>

	<?php
	/**
	 * Fires in the KYSS page header.
	 *
	 * @since  0.11.0
	 */
	$hook->run( 'kyss_head' );
	?>
</head>
<?php $body_classes = $hook->run('body_class', array( 'page' ) );
	$body_class = (!empty($body_classes)) ? 'class="' : '';
	foreach ( $body_classes as $class )
		$body_class .= " $class";
	if ( ! empty( $body_class ) )
		$body_class .= '"';
	unset( $body_classes );
?>
<body <?php echo $body_class; unset( $body_class ); ?>>

<nav class="top-bar" data-topbar>
	<ul class="title-area">
		<li class="name">
			<h1><a href="<?php echo get_option( 'siteurl' ); ?>"><?php echo get_option( 'sitename' ); ?></a></h1>
		</li>
	</ul>
	<section class="top-bar-section">
		<ul class="right">
		<?php if ( isset( $updates ) && $updates->has_updates() ) : ?>
			<li>
				<a href="<?php echo get_site_url( 'admin/update.php' ); ?>" title="Aggiornamenti disponibili" class="tooltip-bottom" data-tooltip aria-haspopup="true">
					<span class="dashicons dashicons-update"></span>
				</a>
			</li>
		<?php endif; ?>
			<li>
				<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $current_user->ID ); ?>" title="Profilo utente" class="tooltip-bottom" data-tooltip aria-haspopup="true">
					<span class="dashicons dashicons-admin-users"></span> <?php echo $current_user->nome . ' ' . $current_user->cognome; ?>
				</a>
			</li>
			<li class="has-form">
				<a href="<?php echo get_site_url( 'logout.php' ); ?>" class="button ghost">Logout</a>
			</li>
		</ul>
	</section>
</nav>

<div class="wrapper row">