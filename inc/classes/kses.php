<?php
/**
 * KYSS kses.
 *
 * This is a fork of kses5 (PHP5 OOP version of kses) adapted to work with KYSS.
 *
 * @package KYSS
 * @subpackage  kses
 * @link(kses, http://sourceforge.net/projects/kses)
 */

/*
 * ==========================================================================================
 *
 * This program is free software and open source software; you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
 * http://www.gnu.org/licenses/gpl.html
 *
 * ==========================================================================================
 */

if(substr(phpversion(), 0, 1) < 5)
{
	kyss_die("Class kses requires PHP 5 or higher.");
}

/**
*	Only install KSES once
*/
if ( ! defined('KSES') ) :

define('KSES', true);

/**
* Kses strips evil scripts!
*
* This class provides the capability for removing unwanted HTML/XHTML, attributes from
* tags, and protocols contained in links.  The net result is a much more powerful tool
* than the PHP internal strip_tags()
*
* This is a fork of PHP5 OOP kses version 1.0.2 by Richard R. Vásquez, Jr.
* The original work was a slick piece of procedural code called 'kses' written by Ulf Harnhammar.
*
* Note that this version is not compatible with Richard's class.
*
*	@author     Mattia Migliorini <deshack@ubuntu.com>
*	@link       http://sourceforge.net/projects/kses/ Home Page for Kses
*	@link       http://chaos.org/contact/ Contact page with current email address for Richard Vasquez
*	@copyright  Mattia Migliorini, 2014
*	@version    1.0.0
*	@license    http://www.gnu.org/licenses/gpl.html GNU Public License
*	@package    KYSS
*	@subpackage kses
*/
class KSES {
	/**#@+
	 *	@access private
	 *	@var array
	 */
	private $allowed_protocols;
	private $allowed_html;
	/**#@-*/

	/**
	 *	Constructor for kses.
	 *
	 *	This sets a default collection of protocols allowed in links, and creates an
	 *	empty set of allowed HTML tags.
	 *	@since 1.0.0
	 */
	public function __construct()
	{
		/**
		 *	You could add protocols such as ftp, new, gopher, mailto, irc, etc.
		 *
		 *	The base values the original kses provided were:
		 *		'http', 'https', 'ftp', 'news', 'nntp', 'telnet', 'gopher', 'mailto'
		 */
		$this->allowed_protocols = array('http', 'ftp', 'mailto');
		$this->allowed_html      = array();
	}

	/**
	 *	Basic task of kses - parses $string and strips it as required.
	 *
	 *	This method strips all the disallowed (X)HTML tags, attributes
	 *	and protocols from the input $string.
	 *
	 *	@access public
	 *	@param string $string String to be stripped of 'evil scripts'
	 *	@return string The stripped string
	 *	@since 0.0.1
	 */
	public function parse($string = "")
	{
		if (get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}
		$string = $this->remove_nulls($string);
		//	Remove JavaScript entities from early Netscape 4 versions
		$string = preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $string);
		$string = $this->normalize_entities($string);
		$string = $this->filter_kses_text_hook($string);
		$string = preg_replace_callback('%(<' . '[^>]*' . '(>|$)' . '|>)', $this->stripTags('\\1'), $string);
		return $string;
	}

	/**
	 *	Allows for single/batch addition of protocols
	 *
	 *	This method accepts one argument that can be either a string
	 *	or an array of strings.  Invalid data will be ignored.
	 *
	 *	The argument will be processed, and each string will be added
	 *	via add_protocol().
	 *
	 *	@access public
	 *	@param mixed , A string or array of protocols that will be added to the internal list of allowed protocols.
	 *	@return bool Status of adding valid protocols.
	 *	@see AddProtocol()
	 *	@since 1.0.0
	 */
	public function add_protocols()
	{
		$c_args = func_num_args();
		if($c_args != 1)
		{
			trigger_error("kses::add_protocols() did not receive an argument.", E_USER_WARNING);
			return false;
		}

		$protocol_data = func_get_arg(0);

		if(is_array($protocol_data) && count($protocol_data) > 0)
		{
			foreach($protocol_data as $protocol)
			{
				$this->add_protocol($protocol);
			}
			return true;
		}
		elseif(is_string($protocol_data))
		{
			$this->add_protocol($protocol_data);
			return true;
		}
		else
		{
			trigger_error("kses::add_protocols() did not receive a string or an array.", E_USER_WARNING);
			return false;
		}
	}

	/**
	 *	Allows for single/batch addition of protocols
	 *
	 *	@deprecated Use add_protocols()
	 *	@see add_protocols()
	 *	@return bool
	 *	@since 0.0.1
	 */
	public function protocols()
	{
		$c_args = func_num_args();
		if($c_args != 1)
		{
			trigger_error("kses::protocols() did not receive an argument.", E_USER_WARNING);
			return false;
		}

		return $this->add_protocols(func_get_arg(0));
	}

	/**
	 *	Adds a single protocol to $this->allowed_protocols.
	 *
	 *	This method accepts a string argument and adds it to
	 *	the list of allowed protocols to keep when performing
	 *	Parse().
	 *
	 *	@access public
	 *	@param string $protocol The name of the protocol to be added.
	 *	@return bool Status of adding valid protocol.
	 *	@since 0.0.1
	 */
	public function add_protocol($protocol = "")
	{
		if(!is_string($protocol))
		{
			trigger_error("kses::add_protocol() requires a string.", E_USER_WARNING);
			return false;
		}

		// Remove any inadvertent ':' at the end of the protocol.
		if(substr($protocol, strlen($protocol) - 1, 1) == ":")
		{
			$protocol = substr($protocol, 0, strlen($protocol) - 1);
		}

		$protocol = strtolower(trim($protocol));
		if($protocol == "")
		{
			trigger_error("kses::add_protocol() tried to add an empty/NULL protocol.", E_USER_WARNING);
			return false;
		}

		//	prevent duplicate protocols from being added.
		if(!in_array($protocol, $this->allowed_protocols))
		{
			array_push($this->allowed_protocols, $protocol);
			sort($this->allowed_protocols);
		}
		return true;
	}

	/**
	 *	Removes a single protocol from $this->allowed_protocols.
	 *
	 *	This method accepts a string argument and removes it from
	 *	the list of allowed protocols to keep when performing
	 *	parse().
	 *
	 *	@access public
	 *	@param string $protocol The name of the protocol to be removed.
	 *	@return bool Status of removing valid protocol.
	 *	@since 1.0.0
	 */
	public function remove_protocol($protocol = "")
	{
		if(!is_string($protocol))
		{
			trigger_error("kses::remove_protocol() requires a string.", E_USER_WARNING);
			return false;
		}

		// Remove any inadvertent ':' at the end of the protocol.
		if(substr($protocol, strlen($protocol) - 1, 1) == ":")
		{
			$protocol = substr($protocol, 0, strlen($protocol) - 1);
		}

		$protocol = strtolower(trim($protocol));
		if($protocol == "")
		{
			trigger_error("kses::remove_protocol() tried to remove an empty/NULL protocol.", E_USER_WARNING);
			return false;
		}

		//	Ensures that the protocol exists before removing it.
		if(in_array($protocol, $this->allowed_protocols))
		{
			$this->allowed_protocols = array_diff($this->allowed_protocols, array($protocol));
			sort($this->allowed_protocols);
		}

		return true;
	}

	/**
	 *	Allows for single/batch removal of protocols
	 *
	 *	This method accepts one argument that can be either a string
	 *	or an array of strings.  Invalid data will be ignored.
	 *
	 *	The argument will be processed, and each string will be removed
	 *	via remove_protocol().
	 *
	 *	@access public
	 *	@param mixed , A string or array of protocols that will be removed from the internal list of allowed protocols.
	 *	@return bool Status of removing valid protocols.
	 *	@see remove_protocol()
	 *	@since 1.0.0
	 */
	public function remove_protocols()
	{
		$c_args = func_num_args();
		if($c_args != 1)
		{
			return false;
		}

		$protocol_data = func_get_arg(0);

		if(is_array($protocol_data) && count($protocol_data) > 0)
		{
			foreach($protocol_data as $protocol)
			{
				$this->remove_protocol($protocol);
			}
		}
		elseif(is_string($protocol_data))
		{
			$this->remove_protocol($protocol_data);
			return true;
		}
		else
		{
			trigger_error("kses::remove_protocols() did not receive a string or an array.", E_USER_WARNING);
			return false;
		}
	}

	/**
	 *	Allows for single/batch replacement of protocols
	 *
	 *	This method accepts one argument that can be either a string
	 *	or an array of strings.  Invalid data will be ignored.
	 *
	 *	Existing protocols will be removed, then the argument will be
	 *	processed, and each string will be added via add_protocol().
	 *
	 *	@access public
	 *	@param mixed , A string or array of protocols that will be the new internal list of allowed protocols.
	 *	@return bool Status of replacing valid protocols.
	 *	@since 1.0.1
	 *	@see add_protocol()
	 */
	public function set_protocols()
	{
		$c_args = func_num_args();
		if($c_args != 1)
		{
			trigger_error("kses::set_protocols() did not receive an argument.", E_USER_WARNING);
			return false;
		}

		$protocol_data = func_get_arg(0);

		if(is_array($protocol_data) && count($protocol_data) > 0)
		{
			$this->allowed_protocols = array();
			foreach($protocol_data as $protocol)
			{
				$this->AddProtocol($protocol);
			}
			return true;
		}
		elseif(is_string($protocol_data))
		{
			$this->allowed_protocols = array();
			$this->add_protocol($protocol_data);
			return true;
		}
		else
		{
			trigger_error("kses::set_protocols() did not receive a string or an array.", E_USER_WARNING);
			return false;
		}
	}

	/**
	 *	Raw dump of allowed protocols
	 *
	 *	This returns an indexed array of allowed protocols for a particular KSES
	 *	instantiation.
	 *
	 *	@access public
	 *	@return array The list of allowed protocols.
	 *	@since 1.0.2
	 */
	public function dump_protocols()
	{
		return $this->allowed_protocols;
	}

	/**
	 *	Raw dump of allowed (X)HTML elements
	 *
	 *	This returns an indexed array of allowed (X)HTML elements and attributes
	 *	for a particular KSES instantiation.
	 *
	 *	@access public
	 *	@return array The list of allowed elements.
	 *	@since 1.0.2
	 */
	public function dump_elements()
	{
		return $this->allowed_html;
	}


	/**
	 *	Adds valid (X)HTML with corresponding attributes that will be kept when stripping 'evil scripts'.
	 *
	 *	This method accepts one argument that can be either a string
	 *	or an array of strings.  Invalid data will be ignored.
	 *
	 *	@access public
	 *	@param string $tag (X)HTML tag that will be allowed after stripping text.
	 *	@param array $attribs Associative array of allowed attributes - key => attribute name - value => attribute parameter
	 *	@return bool Status of Adding (X)HTML and attributes.
	 *	@since 0.0.1
	 */
	public function add_HTML($tag = "", $attribs = array())
	{
		if(!is_string($tag))
		{
			trigger_error("kses::add_HTML() requires the tag to be a string", E_USER_WARNING);
			return false;
		}

		$tag = strtolower(trim($tag));
		if($tag == "")
		{
			trigger_error("kses::add_HTML() tried to add an empty/NULL tag", E_USER_WARNING);
			return false;
		}

		if(!is_array($attribs))
		{
			trigger_error("kses::add_HTML() requires an array (even an empty one) of attributes for '$tag'", E_USER_WARNING);
			return false;
		}

		$new_attribs = array();
		if(is_array($attribs) && count($attribs) > 0)
		{
			foreach($attribs as $idx1 => $val1)
			{
				$new_idx1 = strtolower($idx1);
				$new_val1 = $attribs[$idx1];

				if(is_array($new_val1) && count($attribs) > 0)
				{
					$tmp_val = array();
					foreach($new_val1 as $idx2 => $val2)
					{
						$new_idx2 = strtolower($idx2);
						$tmp_val[$new_idx2] = $val2;
					}
					$new_val1 = $tmp_val;
				}

				$new_attribs[$new_idx1] = $new_val1;
			}
		}

		$this->allowed_html[$tag] = $new_attribs;
		return true;
	}

	/**
	 *	This method removes any NULL characters in $string.
	 *
	 *	@access private
	 *	@param string $string
	 *	@return string String without any NULL/chr(173)
	 *	@since 0.0.1
	 */
	private function remove_nulls($string)
	{
		$string = preg_replace('/\0+/', '', $string);
		$string = preg_replace('/(\\\\0)+/', '', $string);
		return $string;
	}

	/**
	 *	Normalizes HTML entities
	 *
	 *	This function normalizes HTML entities. It will convert "AT&T" to the correct
	 *	"AT&amp;T", "&#00058;" to "&#58;", "&#XYZZY;" to "&amp;#XYZZY;" and so on.
	 *
	 *	@access private
	 *	@param string $string
	 *	@return string String with normalized entities
	 *	@since 0.0.1
	 */
	private function normalize_entities($string)
	{
		# Disarm all entities by converting & to &amp;
		$string = str_replace('&', '&amp;', $string);

		#	TODO: Change back (Keep?) the allowed entities in our entity white list

		#	Keeps entities that start with [A-Za-z]
		$string = preg_replace(
			'/&amp;([A-Za-z][A-Za-z0-9]{0,19});/',
			'&\\1;',
			$string
		);

		#	Change numeric entities to valid 16 bit values

		$string = preg_replace_callback(
			'/&amp;#0*([0-9]{1,5});',
			$this->normalize_entities_16bit("\\1"),
			$string
		);

		#	Change &XHHHHHHH (Hex digits) to 16 bit hex values
		$string = preg_replace(
			'/&amp;#([Xx])0*(([0-9A-Fa-f]{2}){1,2});/',
			'&#\\1\\2;',
			$string
		);

		return $string;
	}

	/**
	 *	Helper method used by normalize_entites()
	 *
	 *	This method helps normalize_entities() to only accept 16 bit values
	 *	and nothing more for &#number; entities.
	 *
	 *	This method helps normalize_entities() during a preg_replace()
	 *	where a &#(0)*XXXXX; occurs.  The '(0)*XXXXXX' value is converted to
	 *	a number and the result is returned as a numeric entity if the number
	 *	is less than 65536.  Otherwise, the value is returned 'as is'.
	 *
	 *	@access private
	 *	@param string $i
	 *	@return string Normalized numeric entity
	 *	@see normalize_entities()
	 *	@since 0.0.1
	 */
	private function normalize_entities_16bit($i)
	{
	  return (($i > 65535) ? "&amp;#$i;" : "&#$i;");
	}

	/**
	 *	Allows for additional user defined modifications to text.
	 *
	 *	This method allows for additional modifications to be performed on
	 *	a string that's being run through parse().  Currently, it returns the
	 *	input string 'as is'.
	 *
	 *	This method is provided for users to extend the kses class for their own
	 *	requirements.
	 *
	 *	@access public
	 *	@param string $string String to perfrom additional modifications on.
	 *	@return string User modified string.
	 *	@see parse()
	 *	@since 1.0.0
	 */
	private function filter_kses_text_hook($string)
	{
	  return $string;
	}

	/**
	 *	Allows for additional user defined modifications to text.
	 *
	 *	@deprecated use filter_kses_text_hook()
	 *	@param string $string
	 *	@return string
	 *	@see filter_kses_text_hook()
	 *	@since 0.0.1
	 */
	private function _hook($string)
	{
		return $this->filter_kses_text_hook($string);
	}

	/**
	 *	This method goes through an array, and changes the keys to all lower case.
	 *
	 *	@access private
	 *	@param array $in_array Associative array
	 *	@return array Modified array
	 *	@since 0.0.1
	 */
	private function make_array_keys_lower_case($in_array)
	{
		$out_array = array();

		if(is_array($in_array) && count($in_array) > 0)
		{
			foreach ($in_array as $in_key => $in_val)
			{
				$out_key = strtolower($in_key);
				$out_array[$out_key] = array();

				if(is_array($in_val) && count($in_val) > 0)
				{
					foreach ($in_val as $in_key2 => $in_val2)
					{
						$out_key2 = strtolower($in_key2);
						$out_array[$out_key][$out_key2] = $in_val2;
					}
				}
			}
		}

		return $out_array;
	}

	/**
	 *	This method strips out disallowed and/or mangled (X)HTML tags along with assigned attributes.
	 *
	 *	This method does a lot of work. It rejects some very malformed things
	 *	like <:::>. It returns an empty string if the element isn't allowed (look
	 *	ma, no strip_tags()!). Otherwise it splits the tag into an element and an
	 *	allowed attribute list.
	 *
	 *	@access private
	 *	@param string $string
	 *	@return string Modified string minus disallowed/mangled (X)HTML and attributes
	 *	@since 0.0.1
	 */
	private function strip_tags($string)
	{
		$string = preg_replace('%\\\\"%', '"', $string);

		if (substr($string, 0, 1) != '<')
		{
			# It matched a ">" character
			return '&gt;';
		}

		if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $string, $matches))
		{
			# It's seriously malformed
			return '';
		}

		$slash    = trim($matches[1]);
		$elem     = $matches[2];
		$attrlist = $matches[3];

		if (
			!isset($this->allowed_html[strtolower($elem)]) ||
			!is_array($this->allowed_html[strtolower($elem)]))
		{
			#	Found an HTML element not in the white list
			return '';
		}

		if ($slash != '')
		{
			return "<$slash$elem>";
		}
		# No attributes are allowed for closing elements

		return $this->strip_attributes("$slash$elem", $attrlist);
	}

	/**
	 *	This method strips out disallowed attributes for (X)HTML tags.
	 *
	 *	This method removes all attributes if none are allowed for this element.
	 *	If some are allowed it calls comb_attributes() to split them further, and then it
	 *	builds up new HTML code from the data that comb_attributes() returns. It also
	 *	removes "<" and ">" characters, if there are any left. One more thing it
	 *	does is to check if the tag has a closing XHTML slash, and if it does,
	 *	it puts one in the returned code as well.
	 *
	 *	@access private
	 *	@param string $element (X)HTML tag to check
	 *	@param string $attr Text containing attributes to check for validity.
	 *	@return string Resulting valid (X)HTML or ''
	 *	@see comb_attributes()
	 *	@since 0.0.1
	 */
	private function strip_attributes($element, $attr)
	{
		# Is there a closing XHTML slash at the end of the attributes?
		$xhtml_slash = '';
		if (preg_match('%\s/\s*$%', $attr))
		{
			$xhtml_slash = ' /';
		}

		# Are any attributes allowed at all for this element?
		if (
			!isset($this->allowed_html[strtolower($element)]) ||
			count($this->allowed_html[strtolower($element)]) == 0
		)
		{
			return "<$element$xhtml_slash>";
		}

		# Split it
		$attrarr = $this->comb_attributes($attr);

		# Go through $attrarr, and save the allowed attributes for this element
		# in $attr2
		$attr2 = '';
		if(is_array($attrarr) && count($attrarr) > 0)
		{
			foreach ($attrarr as $arreach)
			{
				if(!isset($this->allowed_html[strtolower($element)][strtolower($arreach['name'])]))
				{
					continue;
				}

				$current = $this->allowed_html[strtolower($element)][strtolower($arreach['name'])];

				if (!is_array($current))
				{
					# there are no checks
					$attr2 .= ' '.$arreach['whole'];
				}
				else
				{
					# there are some checks
					$ok = true;
					if(is_array($current) && count($current) > 0)
					{
						foreach ($current as $currkey => $currval)
						{
							if (!$this->check_attribute_value($arreach['value'], $arreach['vless'], $currkey, $currval))
							{
								$ok = false;
								break;
							}
						}
					}

					if ($ok)
					{
						# it passed them
						$attr2 .= ' '.$arreach['whole'];
					}
				}
			}
		}

		# Remove any "<" or ">" characters
		$attr2 = preg_replace('/[<>]/', '', $attr2);
		return "<$element$attr2$xhtml_slash>";
	}

	/**
	 *	This method combs through an attribute list string and returns an associative array of attributes and values.
	 *
	 *	This method does a lot of work. It parses an attribute list into an array
	 *	with attribute data, and tries to do the right thing even if it gets weird
	 *	input. It will add quotes around attribute values that don't have any quotes
	 *	or apostrophes around them, to make it easier to produce HTML code that will
	 *	conform to W3C's HTML specification. It will also remove bad URL protocols
	 *	from attribute values.
	 *
	 *	@access private
	 *	@param string $attr Text containing tag attributes for parsing
	 *	@return array Associative array containing data on attribute and value
	 *	@since 0.0.1
	 */
	private function comb_attributes($attr)
	{
		$attrarr  = array();
		$mode     = 0;
		$attrname = '';

		# Loop through the whole attribute list

		while (strlen($attr) != 0)
		{
			# Was the last operation successful?
			$working = 0;

			switch ($mode)
			{
				case 0:	# attribute name, href for instance
					if (preg_match('/^([-a-zA-Z]+)/', $attr, $match))
					{
						$attrname = $match[1];
						$working = $mode = 1;
						$attr = preg_replace('/^[-a-zA-Z]+/', '', $attr);
					}
					break;
				case 1:	# equals sign or valueless ("selected")
					if (preg_match('/^\s*=\s*/', $attr)) # equals sign
					{
						$working = 1;
						$mode    = 2;
						$attr    = preg_replace('/^\s*=\s*/', '', $attr);
						break;
					}
					if (preg_match('/^\s+/', $attr)) # valueless
					{
						$working   = 1;
						$mode      = 0;
						$attrarr[] = array(
							'name'  => $attrname,
							'value' => '',
							'whole' => $attrname,
							'vless' => 'y'
						);
						$attr      = preg_replace('/^\s+/', '', $attr);
					}
					break;
				case 2: # attribute value, a URL after href= for instance
					if (preg_match('/^"([^"]*)"(\s+|$)/', $attr, $match)) # "value"
					{
						$thisval   = $this->remove_bad_protocols($match[1]);
						$attrarr[] = array(
							'name'  => $attrname,
							'value' => $thisval,
							'whole' => $attrname . '="' . $thisval . '"',
							'vless' => 'n'
						);
						$working   = 1;
						$mode      = 0;
						$attr      = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
						break;
					}
					if (preg_match("/^'([^']*)'(\s+|$)/", $attr, $match)) # 'value'
					{
						$thisval   = $this->remove_bad_protocols($match[1]);
						$attrarr[] = array(
							'name'  => $attrname,
							'value' => $thisval,
							'whole' => "$attrname='$thisval'",
							'vless' => 'n'
						);
						$working   = 1;
						$mode      = 0;
						$attr      = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
						break;
					}
					if (preg_match("%^([^\s\"']+)(\s+|$)%", $attr, $match)) # value
					{
						$thisval   = $this->remove_bad_protocols($match[1]);
						$attrarr[] = array(
							'name'  => $attrname,
							'value' => $thisval,
							'whole' => $attrname . '="' . $thisval . '"',
							'vless' => 'n'
						);
						# We add quotes to conform to W3C's HTML spec.
						$working   = 1;
						$mode      = 0;
						$attr      = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
					}
					break;
			}

			if ($working == 0) # not well formed, remove and try again
			{
				$attr = preg_replace('/^("[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*/', '', $attr);
				$mode = 0;
			}
		}

		# special case, for when the attribute list ends with a valueless
		# attribute like "selected"
		if ($mode == 1)
		{
			$attrarr[] = array(
				'name'  => $attrname,
				'value' => '',
				'whole' => $attrname,
				'vless' => 'y'
			);
		}

		return $attrarr;
	}

	/**
	 *	This method removes disallowed protocols.
	 *
	 *	This method removes all non-allowed protocols from the beginning of
	 *	$string. It ignores whitespace and the case of the letters, and it does
	 *	understand HTML entities. It does its work in a while loop, so it won't be
	 *	fooled by a string like "javascript:javascript:alert(57)".
	 *
	 *	@access private
	 *	@param string $string String to check for protocols
	 *	@return string String with removed protocols
	 *	@since 0.0.1
	 */
	private function remove_bad_protocols($string)
	{
		$string  = $this->remove_nulls($string);
		$string = preg_replace('/\xad+/', '', $string); # deals with Opera "feature"
		$string2 = $string . 'a';

		while ($string != $string2)
		{
			$string2 = $string;
			$string  =  preg_replace(
								'/^((&[^;]*;|[\sA-Za-z0-9])*)'.
								'(:|&#58;|&#[Xx]3[Aa];)\s*/e',
								'\$this->filterProtocols("\\1")',
								$string
							);
		}

		return $string;
	}

	/**
	 *	Helper method used by remove_bad_protocols()
	 *
	 *	This function processes URL protocols, checks to see if they're in the white-
	 *	list or not, and returns different data depending on the answer.
	 *
	 *	@access private
	 *	@param string $string String to check for protocols
	 *	@return string String with removed protocols
	 *	@see remove_bad_protocols()
	 *	@since 0.0.1
	 */
	private function filter_protocols($string)
	{
		$string = $this->decode_entities($string);
		$string = preg_replace('/\s/', '', $string);
		$string = $this->remove_nulls($string);
		$string = preg_replace('/\xad+/', '', $string2); # deals with Opera "feature"
		$string = strtolower($string);

		if(is_array($this->allowed_protocols) && count($this->allowed_protocols) > 0)
		{
			foreach ($this->allowed_protocols as $one_protocol)
			{
				if (strtolower($one_protocol) == $string)
				{
					return "$string:";
				}
			}
		}

		return '';
	}

	/**
	 *	Controller method for performing checks on attribute values.
	 *
	 *	This method calls the appropriate method as specified by $checkname with
	 *	the parameters $value, $vless, and $checkvalue, and returns the result
	 *	of the call.
	 *
	 *	This method's functionality can be expanded by creating new methods
	 *	that would match check_attribute_value[$checkname].
	 *
	 *	Current checks implemented are: "maxlen", "minlen", "maxval", "minval" and "valueless"
	 *
	 *	@access private
	 *	@param string $value The value of the attribute to be checked.
	 *	@param string $vless Indicates whether the the value is supposed to be valueless
	 *	@param string $checkname The check to be performed
	 *	@param string $checkvalue The value that is to be checked against
	 *	@return bool Indicates whether the check passed or not
	 *	@since 1.0.0
	 */
	private function check_attribute_value($value, $vless, $checkname, $checkvalue)
	{
		$ok = true;
		$check_attribute_method_name  = 'check_attribute_value' . ucfirst(strtolower($checkname));
		if(method_exists($this, $check_attribute_method_name))
		{
			$ok = $this->$check_attribute_method_name($value, $checkvalue, $vless);
		}

		return $ok;
	}

	/**
	 *	Helper method invoked by check_attribute_value().
	 *
	 *	The maxlen check makes sure that the attribute value has a length not
	 *	greater than the given value. This can be used to avoid Buffer Overflows
	 *	in WWW clients and various Internet servers.
	 *
	 *	@access private
	 *	@param string $value The value of the attribute to be checked.
	 *	@param int $checkvalue The maximum value allowed
	 *	@return bool Indicates whether the check passed or not
	 *	@see check_attribute_value()
	 *	@since 1.0.0
	 */
	private function check_attribute_value_maxlen($value, $checkvalue)
	{
		if (strlen($value) > intval($checkvalue))
		{
			return false;
		}
		return true;
	}

	/**
	 *	Helper method invoked by check_attribute_value().
	 *
	 *	The minlen check makes sure that the attribute value has a length not
	 *	smaller than the given value.
	 *
	 *	@access private
	 *	@param string $value The value of the attribute to be checked.
	 *	@param int $checkvalue The minimum value allowed
	 *	@return bool Indicates whether the check passed or not
	 *	@see check_attribute_value()
	 *	@since 1.0.0
	 */
	private function check_attribute_value_minlen($value, $checkvalue)
	{
		if (strlen($value) < intval($checkvalue))
		{
			return false;
		}
		return true;
	}

	/**
	 *	Helper method invoked by check_attribute_value().
	 *
	 *	The maxval check does two things: it checks that the attribute value is
	 *	an integer from 0 and up, without an excessive amount of zeroes or
	 *	whitespace (to avoid Buffer Overflows). It also checks that the attribute
	 *	value is not greater than the given value.
	 *
	 *	This check can be used to avoid Denial of Service attacks.
	 *
	 *	@access private
	 *	@param int $value The value of the attribute to be checked.
	 *	@param int $checkvalue The maximum numeric value allowed
	 *	@return bool Indicates whether the check passed or not
	 *	@see check_attribute_value()
	 *	@since 1.0.0
	 */
	private function check_attribute_value_maxval($value, $checkvalue)
	{
		if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
		{
			return false;
		}
		if (intval($value) > intval($checkvalue))
		{
			return false;
		}
		return true;
	}

	/**
	 *	Helper method invoked by check_attribute_value().
	 *
	 *	The minval check checks that the attribute value is a positive integer,
	 *	and that it is not smaller than the given value.
	 *
	 *	@access private
	 *	@param int $value The value of the attribute to be checked.
	 *	@param int $checkvalue The minimum numeric value allowed
	 *	@return bool Indicates whether the check passed or not
	 *	@see check_attribute_value()
	 *	@since 1.0.0
	 */
	private function check_attribute_value_minval($value, $checkvalue)
	{
		if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value))
		{
			return false;
		}
		if (intval($value) < ($checkvalue))
		{
			return false;
		}
		return true;
	}

	/**
	 *	Helper method invoked by check_attribute_value().
	 *
	 *	The valueless check checks if the attribute has a value
	 *	(like <a href="blah">) or not (<option selected>). If the given value
	 *	is a "y" or a "Y", the attribute must not have a value.
	 *
	 *	If the given value is an "n" or an "N", the attribute must have one.
	 *
	 *	@access private
	 *	@param int $value The value of the attribute to be checked.
	 *	@param mixed $checkvalue This variable is ignored for this test
	 *	@param string $vless Flag indicating if this attribute is not supposed to have an attribute
	 *	@return bool Indicates whether the check passed or not
	 *	@see check_attribute_value()
	 *	@since 1.0.0
	 */
	private function check_attribute_value_valueless($value, $checkvalue, $vless)
	{
		if (strtolower($checkvalue) != $vless)
		{
			return false;
		}
		return true;
	}

	/**
	 *	Decodes numeric HTML entities
	 *
	 *	This method decodes numeric HTML entities (&#65; and &#x41;). It doesn't
	 *	do anything with other entities like &auml;, but we don't need them in the
	 *	URL protocol white listing system anyway.
	 *
	 *	@access private
	 *	@param string $value The entitiy to be decoded.
	 *	@return string Decoded entity
	 *	@since 0.0.1
	 */
	private function decode_entities($string)
	{
		$string = preg_replace('/&#([0-9]+);/e', 'chr("\\1")', $string);
		$string = preg_replace('/&#[Xx]([0-9A-Fa-f]+);/e', 'chr(hexdec("\\1"))', $string);
		return $string;
	}

	/**
	 *	Returns version # of kses.
	 *
	 *	Since this class has been refactored and documented and proven to work,
	 *	I'm fixing the version number at 1.0.0.
	 *
	 *	This version is syntax compatible with version 0.0.2.  Future
	 *	versions may not be syntax compatible.
	 *
	 *	@access public
	 *	@return string Version number
	 *	@since 0.0.1
	 */
	public function version()
	{
		return '1.0.2';
	}
}

// Instantiate kses object
$kses = new KSES();
endif;