<?php
/**
 * The main KYSS API
 *
 * @package KYSS
 * @subpackage API
 */

/**
 * Kill KYSS execution and display HTML with error message.
 *
 * This function complements the die() PHP function. The difference is that
 * HTML will be displayed to the user. It is recommended to use this function
 * only when the execution should nnot continue any further. It is not
 * recommended to call this function very often and try to handle as may errors
 * as possible silently.
 *
 * @since  0.1.0
 *
 * @param  string $message Error message.
 * @param  string $title Error title.
 * @param  string|array $args Optional. Arguments to control behavior.
 */
function kyss_die( $message = '', $title = '', $args = array() ) {
	$defaults = array( 'response' => 500 );
	$r = kyss_parse_args($args, $defaults);

	if ( function_exists( 'is_kyss_error' ) && is_kyss_error( $message ) ) {
		if ( empty( $title ) ) {
			$error_data = $message->get_error_data();
			if ( is_array( $error_data ) && isset( $error_data['title'] ) )
				$title = $error_data['title'];
		}
		$errors = $message->get_error_messages();
		switch ( count( $errors ) ) :
		case 0 :
			$message = '';
			break;
		case 1 :
			$message = "<p>{$errors[0]}</p>";
			break;
		default :
			$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
			break;
		endswitch;
	} elseif ( is_string( $message ) ) {
		$message = "<p>$message</p>";
	}

	if ( isset( $r['back_link'] ) && $r['back_link'] ) {
		$message .= "\n<p><a href='javascript:history.back()'>&laquo; Back</a></p>";
	}

	if ( !headers_sent() ) {
		status_header( $r['response'] );
		nocache_headers();
		header( 'Content-Type: text/html; charset=utf-8' );
	}

	if ( empty($title) )
		$title = 'KYSS &rsaquo; Error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>
	<style type="text/css">
		html {
			background: #f7f7f7;
		}
		body {
			background: #fff;
			color: #444;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			margin: 2em auto;
			padding: 1em 2em;
			max-width: 700px;
		}
		h1 {
			border-bottom: 1px solid #dadada;
			clear: both;
			color: #666;
			font-size: 24px;
			margin: 30px 0 0 0;
			padding: 0;
			padding-bottom: 7px;
		}
		#error-page {
			margin-top: 50px;
		}
		#error-page p {
			font-size: 16px;
			line-height: 1.5;
			margin: 25px 0 20px;
		}
		#error-page code {
			font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
		}
		ul li {
			margin-bottom: 10px;
			font-size: 16px;
		}
		a {
			color: #dd4814;
			text-decoration: none;
		}
		a:hover,
		a:focus {
			color: #bc3d11;
			text-decoration: underline;
		}
		.button {
			background: #f7f7f7;
			border: 1px solid #ccc;
			color: #555;
			display: inline-block;
			text-decoration: none;
			font-size: 14px;
			line-height: 28px;
			height: 32px;
			margin: 0;
			padding: 0 10px 1px;
			cursor: pointer;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
			-webkit-appearance: none;
			white-space: nowrap;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
			vertical-align: top;
		}
		.button:hover,
		.button:focus {
			background: #fafafa;
			border-color: #999;
			color: #333;
		}
		.button:active {
			background: #eee;
			border-color: #999;
			color: #333;
		}
	</style>
</head>
<body id="error-page">
	<?php echo $message; ?>
</body>
</html>
<?php
	die();
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function allows an array to be merged into another array.
 *
 * @since  0.1.0
 *
 * @param  array $args Array to merge with $defaults.
 * @param  array $defaults Array that serves as the defaults.
 * @return  array Merged user defined values with defaults.
 */
function kyss_parse_args( $args, $defaults = '' ) {
	if ( is_object( $args ) )
		$r = get_object_vars( $args );
	elseif ( is_array( $args ) )
		$r =& $args;

	if ( is_array( $defaults ) )
		return array_merge( $defaults, $r );
	return $r;
}