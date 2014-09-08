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
	});
})(jQuery);
</script>

<?php $hook->run( 'kyss_footer' ); ?>

</body>
</html>