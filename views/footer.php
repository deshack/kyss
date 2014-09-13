<?php
/**
 * Render KYSS page footer.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.11.0
 */

global $hook, $kyss_version;
?>

</main>
</div><!-- .row -->

<footer class="bottom-bar">
	<div class="row">
		<div class="medium-6 columns text-left small-only-text-center">
			<span class="text">Copyright &copy; 2014 Dalla Costa Nicola &amp; Migliorini Mattia</span>
		</div>
		<div class="medium-6 columns text-right small-only-text-center">
			<span class="text">KYSS <?php echo $kyss_version; ?> (Alpha1)</span>
		</div>
	</div>
</footer>

<?php $hook->run( 'kyss_footer' ); ?>

<script>
(function($) {
	$(document).foundation();
	$(function(){
		$.datepicker.setDefaults({
			showOn: "focus",
			dateFormat: 'yy-mm-dd',
			regional: 'it'
		});
		$( ".datepicker" ).datepicker();
		$( ".datetimepicker" ).datetimepicker();
		$( ".timepicker" ).timepicker();
	});
})(jQuery);
</script>
</body>
</html>