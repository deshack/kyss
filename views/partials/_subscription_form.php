<?php
/**
 * Render KYSS Subscription add form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

$action = $_GET['action'];

if ( $action == 'add' ) : 

	$users = KYSS_User::get_users_list();
?>

<h1 class="page-title">Aggiungi iscrizione</h1>

<form id="<?php echo $action; ?>-course" method="get" action="<?php echo get_site_url('views/partials/_subscription_form.php?action=save') ?>">
	<div class="row">
		<div class="medium-6 columns"></div>
			<label for="utente">Utente</label>
			<select name="utente">
			<?php foreach ( $users as $user ) : ?>
				<option value="<?php echo $user->ID; ?>"><?php echo $user->nome . ' ' . $user->cognome; ?>
 				</option>
			<?php endforeach; ?>
			</select>	
		<div class="medium-6 columns"></div>
			<label for="corso">Corso</label>
			<select name="corso"><?php 
				$course = KYSS_Course::get_course_by_id( $course->ID );
				echo isset( $course->nome ) ? $course->nome : 'corso' . $course->ID ?>
			</select>	
	</div>

	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'courses.php?action=view&id=' . $course->ID ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php elseif ( $action == 'save' ) :
	$data = array();
	foreach ($_GET as $key => $value) {
		if ( $key == 'submit' || empty( $value ) )
			continue;
		$data[$key] = $value;
	}

	KYSS_Subscription::create( $data );
	kyss_redirect( get_site_url( '/courses.php?action=view&id=' . $course->ID ) );

else :
	trigger_error( 'Unrecognised action' . $action, E_USER_ERROR );
endif;
?>