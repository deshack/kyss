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

<footer class="bottom-bar text-right small-only-text-center">
<span class="text">KYSS v<?php echo $kyss_version; ?></span>
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