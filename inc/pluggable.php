<?php
/**
 * Function that may be overridden.
 *
 * Wrap all functions in this file in an `if( !function_exists() )` conditional to be
 * able to override them.
 *
 * @package  KYSS
 */

if ( !function_exists('generate_password') ) :
/**
 * Generate strong password.
 *
 * Generates a random password drawn from the defined set of characters.
 *
 * @since 0.3.0
 *
 * @param  int $length Length of the password to generate. Default <10>.
 * @param  bool $special_chars Whether to include standard special characters. Default <true>.
 * @param  bool $extra_chars Whether to include other special characters. Used when
 *                           generating secret keys and salts. Default <false>.
 * @return string The generated password.
 */
function generate_password( $length = 10, $special_chars = true, $extra_chars = false ) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if ( $special_chars )
		$chars .= '!@#$%^&*()';
	if ( $extra_chars )
		$chars .= '-_ []{}<>~`+=,.;:/?|';

	$password = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$password .= substr($chars, randomize(0, strlen($chars) - 1), 1);
	}

	return $password;
}
endif; // generate_password()

if ( !function_exists('randomize') ) :
/**
 * Generate random number.
 *
 * @since  0.3.0
 * 
 * @param  integer $min Lower limit for the generated number.
 * @param  integer $max Upper limit for the generated number.
 * @return integer A random number between min and max.
 */
function randomize( $min = 0, $max = 0 ) {
	global $rnd;

	// Reset $rnd after 14 uses.
	// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd.
	if ( strlen($rnd) < 8 ) {
		static $seed = '';
		$rnd = md5( uniqid(microtime() . mt_rand(), true ) . $seed );
		$rnd .= sha1($rnd);
		$rnd .= sha1( $rnd . $seed );
		$seed = md5($seed . $rnd);
	}

	// Take the first 8 digits of $rnd
	$value = substr($rnd, 0, 8);

	// Strip the first eight, leaving the remainder for the next call to randomize().
	$rnd = substr($rnd, 8);

	$value = abs(hexdec($value));

	// Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers larger
	// than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
	// Credit: WordPress.
	$max_random_number = 3000000000 === 2147483647 ? (float) "4294967295" : 4294967295; // 4294967295 = 0xffffffff

	// Reduce the value to be within the min-max range.
	if ( $max != 0 )
		$value = $min + ($max - $min + 1) * $value / ( $max_random_number + 1 );

	return abs(intval($value));
}
endif; // randomize()

if ( ! function_exists( 'kyss_mail' ) ) :
/**
 * Send email, similar to PHP's mail.
 *
 * A true return value does not automatically mean that the user received the
 * email successfully. It just only means that the method used was able to
 * process the request without any errors.
 *
 * Using the two `mail_from` and `mail_from_name` hooks allow creating a from
 * address like 'Name <email@address.com>' when both are set. If just `mail_from`
 * is set, then just the email address will be used with no name.
 *
 * The default content type is 'text/plain' which does not allow using HTML.
 * However, you can set the content type of the email by using the
 * `mail_content_type` hook.
 *
 * The default charset is based on the charset used on the application. The charset
 * can be set using the `mail_charset` hook.
 *
 * @since 0.14.0
 *
 * @uses  PHPMailer
 *
 * @global  hook
 * @global  mailer
 *
 * @param string|array $to Array or comma-separated list of email addresses to send message to.
 * @param string $subject Email subject.
 * @param string $message Message contents.
 * @param string|array $headers Optional. Additional headers.
 * @param string|array $attachments Optional. Files to attach.
 * @return bool Whether the email contents were sent successfully.
 */
function kyss_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	global $hook;

	// Compact the input, apply the filters, and extract them back out.
	
	/**
	 * Filter the kyss_mail() arguments.
	 *
	 * @since  0.14.0
	 *
	 * @param  array $args A compacted array of kyss_mail() arguments, including the "to" email,
	 * subject, message, headers, and attachments values.
	 */
	extract( $hook->run( 'kyss_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) ) );

	if ( ! is_array( $attachments ) )
		$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );

	global $mailer;

	// (Re)create it, if it's gone missing.
	if ( ! is_object( $mailer ) || ! is_a( $mailer, 'PHPMailer' ) ) {
		require_once CLASSES . 'mailer/PHPMailerAutoload.php';
		$mailer = new PHPMailer( true );
	}

	// Headers
	if ( empty( $headers ) ) {
		$headers = array();
	} else {
		if ( ! is_array( $headers ) ) {
			// Explode the headers out, so this function can take both
			// string headers and an array of headers.
			$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		} else {
			$tempheaders = $headers;
		}
		$headers = array();
		$cc = array();
		$bcc = array();

		// If it's actually got contents.
		if ( ! empty( $tempheaders ) ) {
			// Iterate through the raw headers.
			foreach ( (array) $tempheaders as $header ) {
				if ( strpos( $header, ':' ) === false ) {
					$parts = preg_split( '/boundary=/i', trim( $header ) );
					$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
				}
				continue;
			}
			// Explode them out.
			list( $name, $content ) = explode( ':', trim( $header ), 2 );

			// Cleanup crew.
			$name = trim( $name );
			$content = trim( $content );

			switch ( strtolower( $name ) ) {
				case 'content-type':
					if ( strpos( $content, ';' ) !== false ) {
						list( $type, $charset ) = explode( ';', $content );
						$content_type = trim( $type );
						if ( false !== stripos( $charset, 'charset=' ) ) {
							$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset ) );
						} elseif ( false !== stripos( $charset, 'boundary=' ) ) {
							$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset ) );
							$charset = '';
						}
					} else {
						$content_type = trim( $content );
					}
					break;
				case 'cc':
					$cc = array_merge( (array) $cc, explode( ',', $content ) );
					break;
				case 'bcc':
					$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
					break;
				default:
					// Add it to our grand headers array.
					$headers[trim( $name )] = trim( $content );
					break;
			}
		}
	}

	// Empty out the values that may be set.
	$mailer->ClearAllRecipients();
	$mailer->ClearAttachments();
	$mailer->ClearCustomHeaders();
	$mailer->ClearReplyTos();

	// From email and name.
	// If we don't have a name from the input headers.
	if ( ! isset( $from_name ) )
		$from_name = 'KYSS';

	/**
	 * If we don't have an email from the input headers, default to kyss@$sitename.
	 * Some hosts will block outgoing mail from this address if it doesn't exist,
	 * but there's no easy alternative. Default to admin_email if the current domain
	 * is localhost.
	 */
	
	if ( ! isset( $from_email ) ) {
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		if ( $sitename == 'localhost' || true )
			$from_email = get_option( 'admin_email' );
		else
			$from_email = 'kyss@' . $sitename;
	}

	/**
	 * Filter the email address to send from.
	 *
	 * @since  0.14.0
	 *
	 * @param  string $from_email Email address to send from.
	 */
	$mailer->From = $hook->run( 'mail_from', $from_email );

	/**
	 * Filter the name to associate with the "from" email address.
	 *
	 * @since  0.14.0
	 *
	 * @param  string $from_name Name associated with the "from" email address.
	 */
	$mailer->FromName = $hook->run( 'mail_from_name', $from_name );

	// Set destination addresses.
	if ( ! is_array( $to ) )
		$to = explode( ',', $to );

	foreach ( (array) $to as $recipient ) {
		try {
			// Break $recipient into name and address parts if in the format "Foo <bar@baz.com".
			$recipient_name = '';
			if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
				if ( count( $matches ) == 3 ) {
					$recipient_name = $matches[1];
					$recipient = $matches[2];
				}
			}
			$mailer->AddAddress( $recipient, $recipient_name );
		} catch ( phpmailerException $e ) {
			continue;
		}
	}

	// Set mail's subject and body.
	$mailer->Subject = $subject;
	$mailer->Body = $message;

	// Add any CC and BCC recipients.
	if ( ! empty( $cc ) ) {
		foreach ( (array) $cc as $recipient ) {
			try {
				// Break $recipient into name and address parts if in the format "Foo <bar@baz.com".
				$recipient_name = '';
				if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
					if ( count( $matches ) == 3 ) {
						$recipient_name = $matches[1];
						$recipient = $matches[2];
					}
				}
				$mailer->AddCc( $recipient, $recipient_name );
			} catch ( phpmailerException $e ) {
				continue;
			}
		}
	}

	if ( ! empty( $bcc ) ) {
		foreach ( (array) $bcc as $recipient ) {
			try {
				// Break $recipient into name and address parts if in the format "Foo <bar@baz.com".
				$recipient_name = '';
				if ( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
					if ( count( $matches ) == 3 ) {
						$recipient_name = $matches[1];
						$recipient = $matches[2];
					}
				}
				$mailer->AddBcc( $recipient, $recipient_name );
			} catch ( phpmailerException $e ) {
				continue;
			}
		}
	}

	// Set to use sendmail.
	$mailer->IsSendmail();

	// Set Content-Type and charset.
	// If we don't have a content-type from the input headers.
	if ( ! isset( $content_type ) )
		$content_type = 'text/plain';

	/**
	 * Filter the kyss_mail() content type.
	 *
	 * @since  0.14.0
	 *
	 * @param  string $content_type Default kyss_mail() content type.
	 */
	$content_type = $hook->run( 'mail_content_type', $content_type );

	$mailer->ContentType = $content_type;

	// Set whether it's plaintext, depending on $content_type.
	if ( 'text/html' == $content_type )
		$mailer->IsHTML( true );

	// If we don't have a charset from the input headers.
	if ( ! isset( $charset ) )
		$charset = 'utf-8';

	/**
	 * Filter the default kyss_mail() charset.
	 *
	 * @since  0.14.0
	 *
	 * @param  string $charset Default email charset.
	 */
	$mailer->CharSet = $hook->run( 'mail_charset', $charset );

	// Set custom headers.
	if ( ! empty( $headers ) ) {
		foreach ( (array) $hedaers as $name => $content )
			$mailer->AddCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );

		if ( false !== stripos( $content_type, 'multipart' ) && ! empty( $boundary ) )
			$mailer->AddCustomHeader( sprintf( "Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary ) );
	}

	if ( ! empty( $attachments ) ) {
		foreach ( $attachments as $attachment ) {
			try {
				$mailer->AddAttachment( $attachment );
			} catch ( phpmailerException $e ) {
				continue;
			}
		}
	}

	/**
	 * Fires after PHPMailer is initialized.
	 *
	 * @since  0.14.0
	 *
	 * @param  PHPMailer &$mailer The PHPMailer instance, passed by reference.
	 */
	$hook->run( 'mailer_init', array( &$mailer ) );

	// Send!
	try {
		return $mailer->Send();
	} catch ( phpmailerException $e ) {
		return false;
	}
}
endif; // kyss_mail()

if ( ! function_exists( 'ping' ) ) :
/**
 * Ping host.
 *
 * If only the `$host` parameter is given, uses `exec()` to
 * execute a standard ping, otherwise uses `fsockopen()`.
 *
 * @since  0.15.0
 *
 * @param  string $host Host to ping.
 * @param  int $port Optional. Specific port number to ping.
 * @param  int $timeout Optional. Ping timeout in seconds. Default 6.
 * @return bool Whether the host is up or not.
 */
function ping( $host, $port = null, $timeout = 6 ) {
	if ( is_null( $port ) ) {
		exec( sprintf( 'ping -c 1 -W 5 %s', escapeshellarg( $host ) ), $res, $rval );
		return $rval === 0;
	} else {
		$fsock = fsockopen( $host, $port, $errno, $errstr, $timeout );
		if ( ! $fsock )
			return false;
		else
			return true;
	}
}
endif; // ping()