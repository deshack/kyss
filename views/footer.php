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
	jQuery(document).foundation();
</script>

<?php $hook->run( 'kyss_footer' ); ?>

</body>
</html>