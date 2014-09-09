<?php
/**
 * Render KYSS page footer.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.11.0
 */

global $hook;
?>

</main>
</div><!-- .row -->

<footer class="top-bar">
Something
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