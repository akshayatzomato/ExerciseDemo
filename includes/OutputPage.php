<?php
/**
 * Preparation for the final page rendering.
 *
 */

class OutputPage {

	// List of void elements from HTML5, section 8.1.2 as of 2011-08-12
	private static $voidElements = array(
		'area',
		'base',
		'br',
		'col',
		'command',
		'embed',
		'hr',
		'img',
		'input',
		'keygen',
		'link',
		'meta',
		'param',
		'source',
		'track',
		'wbr',
	);

	// Boolean attributes, which may have the value omitted entirely.  Manually
	// collected from the HTML5 spec as of 2011-08-12.
	private static $boolAttribs = array(
		'async',
		'autofocus',
		'autoplay',
		'checked',
		'controls',
		'default',
		'defer',
		'disabled',
		'formnovalidate',
		'hidden',
		'ismap',
		'itemscope',
		'loop',
		'multiple',
		'muted',
		'novalidate',
		'open',
		'pubdate',
		'readonly',
		'required',
		'reversed',
		'scoped',
		'seamless',
		'selected',
		'truespeed',
		'typemustmatch',
		// HTML5 Microdata
		'itemscope',
	);

    static $templates = array(
        'deals' => 'DealsTemplate'
    );
	/// Should be private. Used with addMeta() which adds "<meta>"
	var $mMetatags = array();

	var $mLinktags = array();
	var $mStatusCode;

	/**
	 * Should be private. Used for JavaScript (pre resource loader)
	 * We should split js / css.
	 * mScripts content is inserted as is in "<head>" by Skin. This might
	 * contains either a link to a stylesheet or inline css.
	 */
	var $mScripts = '';

	/**
	 * Inline CSS styles. Use addInlineStyle() sparingly
	 */
	var $mInlineStyles = '';

	/// Array of elements in "<head>". Parser might add its own headers!
	var $mHeadItems = array();

    private $data;

	/**
	 * An array of stylesheet filenames (relative from skins path), with options
	 * for CSS media, IE conditions, and RTL/LTR direction.
	 * For internal use; add settings in the skin via $this->addStyle()
	 *
	 * Style again! This seems like a code duplication since we already have
	 * mStyles. This is what makes OpenSource amazing.
	 */
	var $styles = array();

	/**
	 * Constructor for OutputPage. This should not be called directly.
	 */
	public function __construct() {
        $this->template = null;
        $this->data = array();
	}

	/**
	 * Get the message associated with HTTP response code $code
	 * @param $code Integer: status code
	 * @return String or null: message or null if $code is not in the list of
	 *         messages
	 */
	public static function getMessage( $code ) {
		static $statusMessage = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			416 => 'Request Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			507 => 'Insufficient Storage'
		);
		return isset( $statusMessage[$code] ) ? $statusMessage[$code] : null;
	}

	/**
	 * Add a new "<meta>" tag
	 * To add an http-equiv meta tag, precede the name with "http:"
	 *
	 * @param string $name tag name
	 * @param string $val tag value
	 */
	function addMeta( $name, $val ) {
		array_push( $this->mMetatags, array( $name, $val ) );
	}

	/**
	 * Get the value of the "rel" attribute for metadata links
	 *
	 * @return String
	 */
	public function getMetadataAttribute() {
		# note: buggy CC software only reads first "meta" link
		static $haveMeta = false;
		if ( $haveMeta ) {
			return 'alternate meta';
		} else {
			$haveMeta = true;
			return 'meta';
		}
	}

	/**
	 * Returns "</$element>", except if $hdWellFormedXml is off, in which case
	 * it returns the empty string when that's guaranteed to be safe.
	 *
	 * @param string $element Name of the element, e.g., 'a'
	 * @return string A closing tag, if required
	 */
	public static function closeElement( $element ) {
		global $hdWellFormedXml;

		$element = strtolower( $element );

		// Reference:
		// http://www.whatwg.org/html/syntax.html#optional-tags
		if ( !$hdWellFormedXml && in_array( $element, array(
			'html',
			'head',
			'body',
			'li',
			'dt',
			'dd',
			'tr',
			'td',
			'th',
		) ) ) {
			return '';
		}
		return "</$element>";
	}

	/**
	 * Identical to rawElement(), but has no third parameter and omits the end
	 * tag (and the self-closing '/' in XML mode for empty elements).
	 *
	 * @param $element string
	 * @param $attribs array
	 *
	 * @return string
	 */
	public static function openElement( $element, $attribs = array() ) {
		global $hdWellFormedXml;
		$attribs = (array)$attribs;
		// This is not required in HTML5, but let's do it anyway, for
		// consistency and better compression.
		$element = strtolower( $element );

		// In text/html, initial <html> and <head> tags can be omitted under
		// pretty much any sane circumstances, if they have no attributes.  See:
		// <http://www.whatwg.org/html/syntax.html#optional-tags>
		if ( !$hdWellFormedXml && !$attribs
		&& in_array( $element, array( 'html', 'head' ) ) ) {
			return '';
		}

		// Remove invalid input types
		if ( $element == 'input' ) {
			$validTypes = array(
				'hidden',
				'text',
				'password',
				'checkbox',
				'radio',
				'file',
				'submit',
				'image',
				'reset',
				'button',

				// HTML input types
				'datetime',
				'datetime-local',
				'date',
				'month',
				'time',
				'week',
				'number',
				'range',
				'email',
				'url',
				'search',
				'tel',
				'color',
			);
			if ( isset( $attribs['type'] )
			&& !in_array( $attribs['type'], $validTypes ) ) {
				unset( $attribs['type'] );
			}
		}

		// According to standard the default type for <button> elements is "submit".
		// Depending on compatibility mode IE might use "button", instead.
		// We enforce the standard "submit".
		if ( $element == 'button' && !isset( $attribs['type'] ) ) {
			$attribs['type'] = 'submit';
		}

		return "<$element" . self::expandAttributes(
			self::dropDefaults( $element, $attribs ) ) . '>';
	}

	/**
	 * Returns an HTML element in a string.  The major advantage here over
	 * manually typing out the HTML is that it will escape all attribute
	 * values.  If you're hardcoding all the attributes, or there are none, you
	 * should probably just type out the html element yourself.
	 *
	 * @param string $element The element's name, e.g., 'a'
	 * @param array $attribs  Associative array of attributes, e.g., array(
	 *   'href' => 'http://www.mediawiki.org/' ). See expandAttributes() for
	 *   further documentation.
	 * @param string $contents The raw HTML contents of the element: *not*
	 *   escaped!
	 * @return string Raw HTML
	 */
	public static function rawElement( $element, $attribs = array(), $contents = '' ) {
		global $hdWellFormedXml;
		$start = self::openElement( $element, $attribs );
		if ( in_array( $element, self::$voidElements ) ) {
			if ( $hdWellFormedXml ) {
				// Silly XML.
				return substr( $start, 0, -1 ) . ' />';
			}
			return $start;
		} else {
			return "$start$contents" . self::closeElement( $element );
		}
	}

	/**
	 * Identical to rawElement(), but HTML-escapes $contents
	 *
	 * @param $element string
	 * @param $attribs array
	 * @param $contents string
	 *
	 * @return string
	 */
	public static function element( $element, $attribs = array(), $contents = '' ) {
		return self::rawElement( $element, $attribs, strtr( $contents, array(
			// There's no point in escaping quotes, >, etc. in the contents of
			// elements.
			'&' => '&amp;',
			'<' => '&lt;'
		) ) );
	}

	/**
	 * Output a "<script>" tag linking to the given URL, e.g.,
	 * "<script src=foo.js></script>".
	 *
	 * @param $url string
	 * @return string Raw HTML
	 */
	public static function linkedScript( $url ) {
		$attrs = array( 'src' => $url );

		return self::element( 'script', $attrs );
	}

	/**
	 * Add raw HTML to the list of scripts (including \<script\> tag, etc.)
	 *
	 * @param string $script raw HTML
	 */
	function addScript( $script ) {
		$this->mScripts .= $script . "\n";
	}


	/**
	 * Add a JavaScript file out of skins/common, or a given relative path.
	 *
	 * @param string $file filename in skins/common or complete on-server path
	 *              (/foo/bar.js)
	 * @param string $version style version of the file. Defaults to $wgStyleVersion
	 */
	public function addScriptFile( $file, $version = null ) {
		global $hdScriptPath;
        $path = "$hdScriptPath" . $file; 
		$this->addScript( self::linkedScript( hdAppendQuery( $path, $version ) ) );
	}

	/**
	 * Add a self-contained script tag with the given contents
	 *
	 * @param string $script JavaScript text, no "<script>" tags
	 */
	public function addInlineScript( $script ) {
		$this->mScripts .= self::inlineScript( "\n$script\n" ) . "\n";
	}

	/**
	 * Output a "<script>" tag with the given contents.
	 *
	 * @param string $contents JavaScript
	 * @return string Raw HTML
	 */
	public static function inlineScript( $contents ) {
		global $hdWellFormedXml;

		$attrs = array();

		if ( $hdWellFormedXml && preg_match( '/[<&]/', $contents ) ) {
			$contents = "/*<![CDATA[*/$contents/*]]>*/";
		}

		return self::rawElement( 'script', $attrs, $contents );
	}

	/**
	 * Get all registered JS and CSS tags for the header.
	 *
	 * @return String
	 */
	function getScript() {
		return $this->mScripts . $this->getHeadItems();
	}


	/**
	 * Get an array of head items
	 *
	 * @return Array
	 */
	function getHeadItemsArray() {
		return $this->mHeadItems;
	}

	/**
	 * Get all header items in a string
	 *
	 * @return String
	 */
	function getHeadItems() {
		$s = '';
		foreach ( $this->mHeadItems as $item ) {
			$s .= $item;
		}
		return $s;
	}

	/**
	 * Add or replace an header item to the output
	 *
	 * @param string $name item name
	 * @param string $value raw HTML
	 */
	public function addHeadItem( $name, $value ) {
		$this->mHeadItems[$name] = $value;
	}

    public function getTemplate() {
        global $hdRequestType;

        if ( isset( self::$templates[$hdRequestType] ) ) {
            $className = self::$templates[$hdRequestType];
            $template =  new $className( $this );
            return $template;
        }
        return null;
        //return isset ( $templates[$hdRequestType] ) ? $templates[$hdRequestType] : null;
    }

    public function setData( $data ) {
        $this->data = $data; 
    }


	/**
	 * Finally, all the text has been munged and accumulated into
	 * the object, let's actually output it:
	 */
	public function output() {
		$response = new WebResponse();
        if ( $this->mStatusCode ) {
			$message = self::getMessage( $this->mStatusCode );
			if ( $message ) {
				$response->header( 'HTTP/1.1 ' . $this->mStatusCode . ' ' . $message );
			}
		}

		# Buffer output; final headers may depend on later processing
		ob_start();

        # Headers
		$response->header( "Content-type: text/html; charset=UTF-8" );
		//$this->sendCacheControl();
        $response->header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', 0 ) . ' GMT' );
        $response->header( 'Cache-Control: no-cache, no-store, max-age=0, must-revalidate' );
        $response->header( 'Pragma: no-cache' );

        $template = $this->getTemplate();
        $template->setData( $this->data );
        $template->render();
		ob_end_flush();
	}


	/**
	 * Given an element name and an associative array of element attributes,
	 * return an array that is functionally identical to the input array, but
	 * possibly smaller.  In particular, attributes might be stripped if they
	 * are given their default values.
	 *
	 * @param string $element Name of the element, e.g., 'a'
	 * @param array $attribs  Associative array of attributes, e.g., array(
	 *   'href' => 'http://www.mediawiki.org/' ).  See expandAttributes() for
	 *   further documentation.
	 * @return array An array of attributes functionally identical to $attribs
	 */
	private static function dropDefaults( $element, $attribs ) {

		// Whenever altering this array, please provide a covering test case
		// in HtmlTest::provideElementsWithAttributesHavingDefaultValues
		static $attribDefaults = array(
			'area' => array( 'shape' => 'rect' ),
			'button' => array(
				'formaction' => 'GET',
				'formenctype' => 'application/x-www-form-urlencoded',
			),
			'canvas' => array(
				'height' => '150',
				'width' => '300',
			),
			'command' => array( 'type' => 'command' ),
			'form' => array(
				'action' => 'GET',
				'autocomplete' => 'on',
				'enctype' => 'application/x-www-form-urlencoded',
			),
			'input' => array(
				'formaction' => 'GET',
				'type' => 'text',
			),
			'keygen' => array( 'keytype' => 'rsa' ),
			'link' => array( 'media' => 'all' ),
			'menu' => array( 'type' => 'list' ),
			// Note: the use of text/javascript here instead of other JavaScript
			// MIME types follows the HTML5 spec.
			'script' => array( 'type' => 'text/javascript' ),
			'style' => array(
				'media' => 'all',
				'type' => 'text/css',
			),
			'textarea' => array( 'wrap' => 'soft' ),
		);

		$element = strtolower( $element );

		foreach ( $attribs as $attrib => $value ) {
			$lcattrib = strtolower( $attrib );
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			} else {
				$value = strval( $value );
			}

			// Simple checks using $attribDefaults
			if ( isset( $attribDefaults[$element][$lcattrib] ) &&
			$attribDefaults[$element][$lcattrib] == $value ) {
				unset( $attribs[$attrib] );
			}

			if ( $lcattrib == 'class' && $value == '' ) {
				unset( $attribs[$attrib] );
			}
		}

		// More subtle checks
		if ( $element === 'link' && isset( $attribs['type'] )
		&& strval( $attribs['type'] ) == 'text/css' ) {
			unset( $attribs['type'] );
		}
		if ( $element === 'input' ) {
			$type = isset( $attribs['type'] ) ? $attribs['type'] : null;
			$value = isset( $attribs['value'] ) ? $attribs['value'] : null;
			if ( $type === 'checkbox' || $type === 'radio' ) {
				// The default value for checkboxes and radio buttons is 'on'
				// not ''. By stripping value="" we break radio boxes that
				// actually wants empty values.
				if ( $value === 'on' ) {
					unset( $attribs['value'] );
				}
			} elseif ( $type === 'submit' ) {
				// The default value for submit appears to be "Submit" but
				// let's not bother stripping out localized text that matches
				// that.
			} else {
				// The default value for nearly every other field type is ''
				// The 'range' and 'color' types use different defaults but
				// stripping a value="" does not hurt them.
				if ( $value === '' ) {
					unset( $attribs['value'] );
				}
			}
		}
		if ( $element === 'select' && isset( $attribs['size'] ) ) {
			if ( in_array( 'multiple', $attribs )
				|| ( isset( $attribs['multiple'] ) && $attribs['multiple'] !== false )
			) {
				// A multi-select
				if ( strval( $attribs['size'] ) == '4' ) {
					unset( $attribs['size'] );
				}
			} else {
				// Single select
				if ( strval( $attribs['size'] ) == '1' ) {
					unset( $attribs['size'] );
				}
			}
		}

		return $attribs;
	}
	/**
	 * Given an associative array of element attributes, generate a string
	 * to stick after the element name in HTML output.  Like array( 'href' =>
	 * 'http://www.mediawiki.org/' ) becomes something like
	 * ' href="http://www.mediawiki.org"'.  Again, this is like
	 * Xml::expandAttributes(), but it implements some HTML-specific logic.
	 * For instance, it will omit quotation marks if $hdWellFormedXml is false,
	 * and will treat boolean attributes specially.
	 *
	 * Attributes that should contain space-separated lists (such as 'class') array
	 * values are allowed as well, which will automagically be normalized
	 * and converted to a space-separated string. In addition to a numerical
	 * array, the attribute value may also be an associative array. See the
	 * example below for how that works.
	 *
	 * @par Numerical array
	 * @code
	 *     Html::element( 'em', array(
	 *         'class' => array( 'foo', 'bar' )
	 *     ) );
	 *     // gives '<em class="foo bar"></em>'
	 * @endcode
	 *
	 * @par Associative array
	 * @code
	 *     Html::element( 'em', array(
	 *         'class' => array( 'foo', 'bar', 'foo' => false, 'quux' => true )
	 *     ) );
	 *     // gives '<em class="bar quux"></em>'
	 * @endcode
	 *
	 * @param array $attribs Associative array of attributes, e.g., array(
	 *   'href' => 'http://www.mediawiki.org/' ).  Values will be HTML-escaped.
	 *   A value of false means to omit the attribute.  For boolean attributes,
	 *   you can omit the key, e.g., array( 'checked' ) instead of
	 *   array( 'checked' => 'checked' ) or such.
	 * @return string HTML fragment that goes between element name and '>'
	 *   (starting with a space if at least one attribute is output)
	 */
	public static function expandAttributes( $attribs ) {
		global $hdWellFormedXml;

		$ret = '';
		$attribs = (array)$attribs;
		foreach ( $attribs as $key => $value ) {
			// Support intuitive array( 'checked' => true/false ) form
			if ( $value === false || is_null( $value ) ) {
				continue;
			}

			// For boolean attributes, support array( 'foo' ) instead of
			// requiring array( 'foo' => 'meaningless' ).
			if ( is_int( $key )
			&& in_array( strtolower( $value ), self::$boolAttribs ) ) {
				$key = $value;
			}

			// Not technically required in HTML5 but we'd like consistency
			// and better compression anyway.
			$key = strtolower( $key );

			// Bug 23769: Blacklist all form validation attributes for now.  Current
			// (June 2010) WebKit has no UI, so the form just refuses to submit
			// without telling the user why, which is much worse than failing
			// server-side validation.  Opera is the only other implementation at
			// this time, and has ugly UI, so just kill the feature entirely until
			// we have at least one good implementation.

			// As the default value of "1" for "step" rejects decimal
			// numbers to be entered in 'type="number"' fields, allow
			// the special case 'step="any"'.

			if ( in_array( $key, array( 'max', 'min', 'pattern', 'required' ) ) ||
				 $key === 'step' && $value !== 'any' ) {
				continue;
			}

			// http://www.w3.org/TR/html401/index/attributes.html ("space-separated")
			// http://www.w3.org/TR/html5/index.html#attributes-1 ("space-separated")
			$spaceSeparatedListAttributes = array(
				'class', // html4, html5
				'accesskey', // as of html5, multiple space-separated values allowed
				// html4-spec doesn't document rel= as space-separated
				// but has been used like that and is now documented as such
				// in the html5-spec.
				'rel',
			);

			// Specific features for attributes that allow a list of space-separated values
			if ( in_array( $key, $spaceSeparatedListAttributes ) ) {
				// Apply some normalization and remove duplicates

				// Convert into correct array. Array can contain space-separated
				// values. Implode/explode to get those into the main array as well.
				if ( is_array( $value ) ) {
					// If input wasn't an array, we can skip this step
					$newValue = array();
					foreach ( $value as $k => $v ) {
						if ( is_string( $v ) ) {
							// String values should be normal `array( 'foo' )`
							// Just append them
							if ( !isset( $value[$v] ) ) {
								// As a special case don't set 'foo' if a
								// separate 'foo' => true/false exists in the array
								// keys should be authoritative
								$newValue[] = $v;
							}
						} elseif ( $v ) {
							// If the value is truthy but not a string this is likely
							// an array( 'foo' => true ), falsy values don't add strings
							$newValue[] = $k;
						}
					}
					$value = implode( ' ', $newValue );
				}
				$value = explode( ' ', $value );

				// Normalize spacing by fixing up cases where people used
				// more than 1 space and/or a trailing/leading space
				$value = array_diff( $value, array( '', ' ' ) );

				// Remove duplicates and create the string
				$value = implode( ' ', array_unique( $value ) );
			}

			// See the "Attributes" section in the HTML syntax part of HTML5,
			// 9.1.2.3 as of 2009-08-10.  Most attributes can have quotation
			// marks omitted, but not all.  (Although a literal " is not
			// permitted, we don't check for that, since it will be escaped
			// anyway.)
			#
			// See also research done on further characters that need to be
			// escaped: http://code.google.com/p/html5lib/issues/detail?id=93
			$badChars = "\\x00- '=<>`/\x{00a0}\x{1680}\x{180e}\x{180F}\x{2000}\x{2001}"
				. "\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}"
				. "\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}";
			if ( $hdWellFormedXml || $value === ''
			|| preg_match( "![$badChars]!u", $value ) ) {
				$quote = '"';
			} else {
				$quote = '';
			}

			if ( in_array( $key, self::$boolAttribs ) ) {
				// In HTML5, we can leave the value empty. If we don't need
				// well-formed XML, we can omit the = entirely.
				if ( !$hdWellFormedXml ) {
					$ret .= " $key";
				} else {
					$ret .= " $key=\"\"";
				}
			} else {
				// Apparently we need to entity-encode \n, \r, \t, although the
				// spec doesn't mention that.  Since we're doing strtr() anyway,
				// and we don't need <> escaped here, we may as well not call
				// htmlspecialchars().
				// @todo FIXME: Verify that we actually need to
				// escape \n\r\t here, and explain why, exactly.
				#
				// We could call Sanitizer::encodeAttribute() for this, but we
				// don't because we're stubborn and like our marginal savings on
				// byte size from not having to encode unnecessary quotes.
				$map = array(
					'&' => '&amp;',
					'"' => '&quot;',
					"\n" => '&#10;',
					"\r" => '&#13;',
					"\t" => '&#9;'
				);
				if ( $hdWellFormedXml ) {
					// This is allowed per spec: <http://www.w3.org/TR/xml/#NT-AttValue>
					// But reportedly it breaks some XML tools?
					// @todo FIXME: Is this really true?
					$map['<'] = '&lt;';
				}
				$ret .= " $key=$quote" . strtr( $value, $map ) . $quote;
			}
		}
		return $ret;
	}
	/**
	 * Determines if the given mime type is xml.
	 *
	 * @param $mimetype    string MimeType
	 * @return Boolean
	 */
	public static function isXmlMimeType( $mimetype ) {
		# http://www.whatwg.org/html/infrastructure.html#xml-mime-type
		# * text/xml
		# * application/xml
		# * Any mimetype with a subtype ending in +xml (this implicitly includes application/xhtml+xml)
		return (bool)preg_match( '!^(text|application)/xml$|^.+/.+\+xml$!', $mimetype );
	}

	/**
	 * Constructs the opening html-tag with necessary doctypes depending on
	 * global variables.
	 *
	 * @param array $attribs  Associative array of miscellaneous extra
	 *   attributes, passed to Html::element() of html tag.
	 * @return string  Raw HTML
	 */
	public static function htmlHeader( $attribs = array() ) {
		$ret = '';

		global $wgHtml5Version, $wgMimeType, $wgXhtmlNamespaces;

		$isXHTML = self::isXmlMimeType( $wgMimeType );

		if ( $isXHTML ) { // XHTML5
			// XML mimetyped markup should have an xml header.
			// However a DOCTYPE is not needed.
			$ret .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?" . ">\n";

			// Add the standard xmlns
			$attribs['xmlns'] = 'http://www.w3.org/1999/xhtml';

			// And support custom namespaces
			foreach ( $wgXhtmlNamespaces as $tag => $ns ) {
				$attribs["xmlns:$tag"] = $ns;
			}
		} else { // HTML5
			// DOCTYPE
			$ret .= "<!DOCTYPE html>\n";
		}

		if ( $wgHtml5Version ) {
			$attribs['version'] = $wgHtml5Version;
		}

		$html = self::openElement( 'html', $attribs );

		if ( $html ) {
			$html .= "\n";
		}

		$ret .= $html;

		return $ret;
	}

	public function headElement() {
		global $hdContLang, $hdMimeType, $hdTitle;

		$ret = self::htmlHeader( array( 'lang' => 'en', 'dir' => 'ltr' ) );

        //$this->setHTMLTitle( $this->msg( 'pagetitle', $hdTitle ) );

		$openHead = self::openElement( 'head' );
		if ( $openHead ) {
			# Don't bother with the newline if $head == ''
			$ret .= "$openHead\n";
		}

			$ret .= self::element( 'meta', array( 'charset' => 'UTF-8' ) );

		$ret .= self::element( 'title', null, $hdTitle ) . "\n";

		$ret .= implode( "\n", array(
			$this->buildCssLinks(),
			$this->getHeadScripts(),
			$this->getHeadItems()
		) );

		$closeHead = self::closeElement( 'head' );
		if ( $closeHead ) {
			$ret .= "$closeHead\n";
		}

        $bodyAttrs = array();
		$ret .= self::openElement( 'body', $bodyAttrs ) . "\n";

		return $ret;
	}

	/**
	 * JS stuff to put in the "<head>". This is the startup module, config
	 * vars and modules marked with position 'top'
	 *
	 * @return String: HTML fragment
	 */
	function getHeadScripts() {
		return $this->mScripts;
	}


	/**
	 * JS stuff to put at the bottom of the "<body>"
	 * @return string
	 */
	function getBottomScripts() {
		return $html;
	}

	/**
	 * Add a local or specified stylesheet, with the given media options.
	 * Meant primarily for internal use...
	 *
	 * @param string $style URL to the file
	 */
	public function addStyle( $style, $options ) {
		$this->styles[$style] = $options;
	}

	/**
	 * Build a set of "<link>" elements for the stylesheets specified in the $this->styles array.
	 * These will be applied to various media & IE conditionals.
	 *
	 * @return string
	 */
	public function buildCssLinks() {
        $ret = "";
		$ret .= implode( "\n", $this->buildCssLinksArray() );
		return $ret;
	}

	/**
	 * @return Array
	 */
	public function buildCssLinksArray() {
		foreach ( $this->styles as $file => $options ) {
			$link = $this->styleLink( $file, $options );
			if ( $link ) {
				$links[$file] = $link;
			}
		}
		return $links;
	}

	/**
	 * Output a "<link rel=stylesheet>" linking to the given URL for the given
	 * media type (if any).
	 *
	 * @param $url string
	 * @param $media mixed A media type string, like 'screen'
	 * @return string Raw HTML
	 */
	public static function linkedStyle( $url, $media = 'all' ) {
		return self::element( 'link', array(
			'rel' => 'stylesheet',
			'href' => $url,
			'type' => 'text/css',
			'media' => $media,
		) );
	}

	/**
	 * Generate \<link\> tags for stylesheets
	 *
	 * @param string $style URL to the file
	 * @param array $options option, can contain 'condition', 'dir', 'media'
	 *                 keys
	 * @return String: HTML fragment
	 */
	protected function styleLink( $style, $options ) {
        global $hdStylePath, $IP;
        $media = 'all';
        $url = $hdStylePath . $style;

		$link = self::linkedStyle( $url, $media );

		return $link;
	}

}
