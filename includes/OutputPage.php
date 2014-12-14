<?php
/**
 * Preparation for the final page rendering.
 *
 */

class OutputPage {

    static $templates = array(
        'deals' => 'DealsTemplate'
    );
	/// Should be private. Used with addMeta() which adds "<meta>"
	var $mMetatags = array();

	var $mLinktags = array();
	var $mCanonicalUrl = false;

	/// Should be private - has getter and setter. Contains the HTML title
	var $mPagetitle = '';


	var $mRedirect = '';
	var $mStatusCode;

	/**
	 * mLastModified and mEtag are used for sending cache control.
	 * The whole caching system should probably be moved into its own class.
	 */
	var $mLastModified = '';

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

	//
	var $mLinkColours;

	/**
	 * Used by skin template.
	 * Example: $tpl->set( 'displaytitle', $out->mPageLinkTitle );
	 */
	var $mPageLinkTitle = '';

	/// Array of elements in "<head>". Parser might add its own headers!
	var $mHeadItems = array();

	// Gwicke work on squid caching? Roughly from 2003.
	var $mEnableClientCache = true;

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

	private $mVaryHeader = array(
		'Accept-Encoding' => array( 'list-contains=gzip' ),
	);

	/**
	 * Constructor for OutputPage. This should not be called directly.
	 */
	public function __construct() {
        $this->template = null;
        $this->data = array();
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
	 * Add a new \<link\> tag to the page header.
	 *
	 * Note: use setCanonicalUrl() for rel=canonical.
	 *
	 * @param array $linkarr associative array of attributes.
	 */
	function addLink( $linkarr ) {
		array_push( $this->mLinktags, $linkarr );
	}

	/**
	 * Add a new \<link\> with "rel" attribute set to "meta"
	 *
	 * @param array $linkarr associative array mapping attribute names to their
	 *                 values, both keys and values will be escaped, and the
	 *                 "rel" attribute will be automatically added
	 */
	function addMetadataLink( $linkarr ) {
		$linkarr['rel'] = $this->getMetadataAttribute();
		$this->addLink( $linkarr );
	}

	/**
	 * Set the URL to be used for the <link rel=canonical>. This should be used
	 * in preference to addLink(), to avoid duplicate link tags.
	 */
	function setCanonicalUrl( $url ) {
		$this->mCanonicalUrl = $url;
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
		global $wgStylePath, $wgStyleVersion;
		// See if $file parameter is an absolute URL or begins with a slash
		if ( substr( $file, 0, 1 ) == '/' || preg_match( '#^[a-z]*://#i', $file ) ) {
			$path = $file;
		} else {
			$path = "{$wgStylePath}/common/{$file}";
		}
		if ( is_null( $version ) ) {
			$version = $wgStyleVersion;
		}
		$this->addScript( Html::linkedScript( wfAppendQuery( $path, $version ) ) );
	}

	/**
	 * Add a self-contained script tag with the given contents
	 *
	 * @param string $script JavaScript text, no "<script>" tags
	 */
	public function addInlineScript( $script ) {
		$this->mScripts .= Html::inlineScript( "\n$script\n" ) . "\n";
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

	/**
	 * Check if the header item $name is already set
	 *
	 * @param string $name item name
	 * @return Boolean
	 */
	public function hasHeadItem( $name ) {
		return isset( $this->mHeadItems[$name] );
	}

	/**
	 * Set the value of the ETag HTTP header, only used if $wgUseETag is true
	 *
	 * @param string $tag value of "ETag" header
	 */
	function setETag( $tag ) {
		$this->mETag = $tag;
	}

	/**
	 * checkLastModified tells the client to use the client-cached page if
	 * possible. If successful, the OutputPage is disabled so that
	 * any future call to OutputPage->output() have no effect.
	 *
	 * Side effect: sets mLastModified for Last-Modified header
	 *
	 * @param $timestamp string
	 *
	 * @return Boolean: true if cache-ok headers was sent.
	 */
	public function checkLastModified( $timestamp ) {
		global $wgCachePages, $wgCacheEpoch, $wgUseSquid, $wgSquidMaxage;

		if ( !$timestamp || $timestamp == '19700101000000' ) {
			wfDebug( __METHOD__ . ": CACHE DISABLED, NO TIMESTAMP\n" );
			return false;
		}
		if ( !$wgCachePages ) {
			wfDebug( __METHOD__ . ": CACHE DISABLED\n", false );
			return false;
		}
		if ( $this->getUser()->getOption( 'nocache' ) ) {
			wfDebug( __METHOD__ . ": USER DISABLED CACHE\n", false );
			return false;
		}

		$timestamp = wfTimestamp( TS_MW, $timestamp );
		$modifiedTimes = array(
			'page' => $timestamp,
			'user' => $this->getUser()->getTouched(),
			'epoch' => $wgCacheEpoch
		);
		if ( $wgUseSquid ) {
			// bug 44570: the core page itself may not change, but resources might
			$modifiedTimes['sepoch'] = wfTimestamp( TS_MW, time() - $wgSquidMaxage );
		}
		wfRunHooks( 'OutputPageCheckLastModified', array( &$modifiedTimes ) );

		$maxModified = max( $modifiedTimes );
		$this->mLastModified = wfTimestamp( TS_RFC2822, $maxModified );

		$clientHeader = $this->getRequest()->getHeader( 'If-Modified-Since' );
		if ( $clientHeader === false ) {
			wfDebug( __METHOD__ . ": client did not send If-Modified-Since header\n", false );
			return false;
		}

		# IE sends sizes after the date like this:
		# Wed, 20 Aug 2003 06:51:19 GMT; length=5202
		# this breaks strtotime().
		$clientHeader = preg_replace( '/;.*$/', '', $clientHeader );

		wfSuppressWarnings(); // E_STRICT system time bitching
		$clientHeaderTime = strtotime( $clientHeader );
		wfRestoreWarnings();
		if ( !$clientHeaderTime ) {
			wfDebug( __METHOD__ . ": unable to parse the client's If-Modified-Since header: $clientHeader\n" );
			return false;
		}
		$clientHeaderTime = wfTimestamp( TS_MW, $clientHeaderTime );

		# Make debug info
		$info = '';
		foreach ( $modifiedTimes as $name => $value ) {
			if ( $info !== '' ) {
				$info .= ', ';
			}
			$info .= "$name=" . wfTimestamp( TS_ISO_8601, $value );
		}

		wfDebug( __METHOD__ . ": client sent If-Modified-Since: " .
			wfTimestamp( TS_ISO_8601, $clientHeaderTime ) . "\n", false );
		wfDebug( __METHOD__ . ": effective Last-Modified: " .
			wfTimestamp( TS_ISO_8601, $maxModified ) . "\n", false );
		if ( $clientHeaderTime < $maxModified ) {
			wfDebug( __METHOD__ . ": STALE, $info\n", false );
			return false;
		}

		# Not modified
		# Give a 304 response code and disable body output
		wfDebug( __METHOD__ . ": NOT MODIFIED, $info\n", false );
		ini_set( 'zlib.output_compression', 0 );
		$this->getRequest()->response()->header( "HTTP/1.1 304 Not Modified" );
		$this->sendCacheControl();
		$this->disable();

		// Don't output a compressed blob when using ob_gzhandler;
		// it's technically against HTTP spec and seems to confuse
		// Firefox when the response gets split over two packets.
		wfClearOutputBuffers();

		return true;
	}

	/**
	 * Override the last modified timestamp
	 *
	 * @param string $timestamp new timestamp, in a format readable by
	 *        wfTimestamp()
	 */
	public function setLastModified( $timestamp ) {
		$this->mLastModified = wfTimestamp( TS_RFC2822, $timestamp );
	}

	/**
	 * Prepend $text to the body HTML
	 *
	 * @param string $text HTML
	 */
	public function prependHTML( $text ) {
		$this->mBodytext = $text . $this->mBodytext;
	}

	/**
	 * Append $text to the body HTML
	 *
	 * @param string $text HTML
	 */
	public function addHTML( $text ) {
		$this->mBodytext .= $text;
	}

	/**
	 * Shortcut for adding an Html::element via addHTML.
	 *
	 * @since 1.19
	 *
	 * @param $element string
	 * @param $attribs array
	 * @param $contents string
	 */
	public function addElement( $element, $attribs = array(), $contents = '' ) {
		$this->addHTML( Html::element( $element, $attribs, $contents ) );
	}

	/**
	 * Use enableClientCache(false) to force it to send nocache headers
	 *
	 * @param $state bool
	 *
	 * @return bool
	 */
	public function enableClientCache( $state ) {
		return wfSetVar( $this->mEnableClientCache, $state );
	}

	/**
	 * Get the list of cookies that will influence on the cache
	 *
	 * @return Array
	 */
	function getCacheVaryCookies() {
		global $wgCookiePrefix, $wgCacheVaryCookies;
		static $cookies;
		if ( $cookies === null ) {
			$cookies = array_merge(
				array(
					"{$wgCookiePrefix}Token",
					"{$wgCookiePrefix}LoggedOut",
					"forceHTTPS",
					session_name()
				),
				$wgCacheVaryCookies
			);
			wfRunHooks( 'GetCacheVaryCookies', array( $this, &$cookies ) );
		}
		return $cookies;
	}

	/**
	 * Check if the request has a cache-varying cookie header
	 * If it does, it's very important that we don't allow public caching
	 *
	 * @return Boolean
	 */
	function haveCacheVaryCookies() {
		$cookieHeader = $this->getRequest()->getHeader( 'cookie' );
		if ( $cookieHeader === false ) {
			return false;
		}
		$cvCookies = $this->getCacheVaryCookies();
		foreach ( $cvCookies as $cookieName ) {
			# Check for a simple string match, like the way squid does it
			if ( strpos( $cookieHeader, $cookieName ) !== false ) {
				wfDebug( __METHOD__ . ": found $cookieName\n" );
				return true;
			}
		}
		wfDebug( __METHOD__ . ": no cache-varying cookies found\n" );
		return false;
	}

	/**
	 * Add an HTTP header that will influence on the cache
	 *
	 * @param string $header header name
	 * @param $option Array|null
	 * @todo FIXME: Document the $option parameter; it appears to be for
	 *        X-Vary-Options but what format is acceptable?
	 */
	public function addVaryHeader( $header, $option = null ) {
		if ( !array_key_exists( $header, $this->mVaryHeader ) ) {
			$this->mVaryHeader[$header] = (array)$option;
		} elseif ( is_array( $option ) ) {
			if ( is_array( $this->mVaryHeader[$header] ) ) {
				$this->mVaryHeader[$header] = array_merge( $this->mVaryHeader[$header], $option );
			} else {
				$this->mVaryHeader[$header] = $option;
			}
		}
		$this->mVaryHeader[$header] = array_unique( (array)$this->mVaryHeader[$header] );
	}

	/**
	 * Return a Vary: header on which to vary caches. Based on the keys of $mVaryHeader,
	 * such as Accept-Encoding or Cookie
	 *
	 * @return String
	 */
	public function getVaryHeader() {
		return 'Vary: ' . join( ', ', array_keys( $this->mVaryHeader ) );
	}

	/**
	 * Get a complete X-Vary-Options header
	 *
	 * @return String
	 */
	public function getXVO() {
		$cvCookies = $this->getCacheVaryCookies();

		$cookiesOption = array();
		foreach ( $cvCookies as $cookieName ) {
			$cookiesOption[] = 'string-contains=' . $cookieName;
		}
		$this->addVaryHeader( 'Cookie', $cookiesOption );

		$headers = array();
		foreach ( $this->mVaryHeader as $header => $option ) {
			$newheader = $header;
			if ( is_array( $option ) && count( $option ) > 0 ) {
				$newheader .= ';' . implode( ';', $option );
			}
			$headers[] = $newheader;
		}
		$xvo = 'X-Vary-Options: ' . implode( ',', $headers );

		return $xvo;
	}

	/**
	 * bug 21672: Add Accept-Language to Vary and XVO headers
	 * if there's no 'variant' parameter existed in GET.
	 *
	 * For example:
	 *   /w/index.php?title=Main_page should always be served; but
	 *   /w/index.php?title=Main_page&variant=zh-cn should never be served.
	 */
	function addAcceptLanguage() {
		$lang = $this->getTitle()->getPageLanguage();
		if ( !$this->getRequest()->getCheck( 'variant' ) && $lang->hasVariants() ) {
			$variants = $lang->getVariants();
			$aloption = array();
			foreach ( $variants as $variant ) {
				if ( $variant === $lang->getCode() ) {
					continue;
				} else {
					$aloption[] = 'string-contains=' . $variant;

					// IE and some other browsers use BCP 47 standards in
					// their Accept-Language header, like "zh-CN" or "zh-Hant".
					// We should handle these too.
					$variantBCP47 = wfBCP47( $variant );
					if ( $variantBCP47 !== $variant ) {
						$aloption[] = 'string-contains=' . $variantBCP47;
					}
				}
			}
			$this->addVaryHeader( 'Accept-Language', $aloption );
		}
	}

	/**
	 * Send cache control HTTP headers
	 */
	public function sendCacheControl() {
		global $wgUseSquid, $wgUseESI, $wgUseETag, $wgSquidMaxage, $wgUseXVO;

		$response = $this->getRequest()->response();
		if ( $wgUseETag && $this->mETag ) {
			$response->header( "ETag: $this->mETag" );
		}

		$this->addVaryHeader( 'Cookie' );
		$this->addAcceptLanguage();

		# don't serve compressed data to clients who can't handle it
		# maintain different caches for logged-in users and non-logged in ones
		$response->header( $this->getVaryHeader() );

		if ( $wgUseXVO ) {
			# Add an X-Vary-Options header for Squid with Wikimedia patches
			$response->header( $this->getXVO() );
		}

		if ( $this->mEnableClientCache ) {
			if (
				$wgUseSquid && session_id() == '' && !$this->isPrintable() &&
				$this->mSquidMaxage != 0 && !$this->haveCacheVaryCookies()
			) {
				if ( $wgUseESI ) {
					# We'll purge the proxy cache explicitly, but require end user agents
					# to revalidate against the proxy on each visit.
					# Surrogate-Control controls our Squid, Cache-Control downstream caches
					wfDebug( __METHOD__ . ": proxy caching with ESI; {$this->mLastModified} **\n", false );
					# start with a shorter timeout for initial testing
					# header( 'Surrogate-Control: max-age=2678400+2678400, content="ESI/1.0"');
					$response->header( 'Surrogate-Control: max-age=' . $wgSquidMaxage . '+' . $this->mSquidMaxage . ', content="ESI/1.0"' );
					$response->header( 'Cache-Control: s-maxage=0, must-revalidate, max-age=0' );
				} else {
					# We'll purge the proxy cache for anons explicitly, but require end user agents
					# to revalidate against the proxy on each visit.
					# IMPORTANT! The Squid needs to replace the Cache-Control header with
					# Cache-Control: s-maxage=0, must-revalidate, max-age=0
					wfDebug( __METHOD__ . ": local proxy caching; {$this->mLastModified} **\n", false );
					# start with a shorter timeout for initial testing
					# header( "Cache-Control: s-maxage=2678400, must-revalidate, max-age=0" );
					$response->header( 'Cache-Control: s-maxage=' . $this->mSquidMaxage . ', must-revalidate, max-age=0' );
				}
			} else {
				# We do want clients to cache if they can, but they *must* check for updates
				# on revisiting the page.
				wfDebug( __METHOD__ . ": private caching; {$this->mLastModified} **\n", false );
				$response->header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', 0 ) . ' GMT' );
				$response->header( "Cache-Control: private, must-revalidate, max-age=0" );
			}
			if ( $this->mLastModified ) {
				$response->header( "Last-Modified: {$this->mLastModified}" );
			}
		} else {
			wfDebug( __METHOD__ . ": no caching **\n", false );

			# In general, the absence of a last modified header should be enough to prevent
			# the client from using its cache. We send a few other things just to make sure.
			$response->header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', 0 ) . ' GMT' );
			$response->header( 'Cache-Control: no-cache, no-store, max-age=0, must-revalidate' );
			$response->header( 'Pragma: no-cache' );
		}
	}


    public function getTemplate() {
        global $hdRequestType;

        if ( isset( $templates[$hdRequestType] ) ) {
            return new $templates[$hdRequestType];
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
			$message = HttpStatus::getMessage( $this->mStatusCode );
			if ( $message ) {
				$response->header( 'HTTP/1.1 ' . $this->mStatusCode . ' ' . $message );
			}
		}

		# Buffer output; final headers may depend on later processing
		ob_start();

        # Headers
		$response->header( "Content-type: text/html; charset=UTF-8" );
		//$this->sendCacheControl();

        $template = $this->getTemplate();
        $template->setData( $this->data );
        $template->render();
		ob_end_flush();
	}


	public function headElement() {
		global $hdContLang, $hdMimeType, $hdTitle;

		$ret = Html::htmlHeader( array( 'lang' => 'en', 'dir' => 'ltr' ) );

		if ( $this->getHTMLTitle() == '' ) {
			$this->setHTMLTitle( $this->msg( 'pagetitle', $this->getPageTitle() ) );
		}

		$openHead = Html::openElement( 'head' );
		if ( $openHead ) {
			# Don't bother with the newline if $head == ''
			$ret .= "$openHead\n";
		}

			$ret .= Html::element( 'meta', array( 'charset' => 'UTF-8' ) );

		$ret .= Html::element( 'title', null, $hdTitle ) . "\n";

		$ret .= implode( "\n", array(
			$this->getHeadLinks(),
			$this->buildCssLinks(),
			$this->getHeadScripts(),
			$this->getHeadItems()
		) );

		$closeHead = Html::closeElement( 'head' );
		if ( $closeHead ) {
			$ret .= "$closeHead\n";
		}

		$ret .= Html::openElement( 'body', $bodyAttrs ) . "\n";

		return $ret;
	}

	/**
	 * JS stuff to put in the "<head>". This is the startup module, config
	 * vars and modules marked with position 'top'
	 *
	 * @return String: HTML fragment
	 */
	function getHeadScripts() {
		return $scripts;
	}


	/**
	 * JS stuff to put at the bottom of the "<body>"
	 * @return string
	 */
	function getBottomScripts() {
		return $html;
	}

	/**
	 * @return array in format "link name or number => 'link html'".
	 */
	public function getHeadLinksArray() {
		$tags = array();

		foreach ( $this->mMetatags as $tag ) {
			if ( 0 == strcasecmp( 'http:', substr( $tag[0], 0, 5 ) ) ) {
				$a = 'http-equiv';
				$tag[0] = substr( $tag[0], 5 );
			} else {
				$a = 'name';
			}
			$tagName = "meta-{$tag[0]}";
			if ( isset( $tags[$tagName] ) ) {
				$tagName .= $tag[1];
			}
			$tags[$tagName] = Html::element( 'meta',
				array(
					$a => $tag[0],
					'content' => $tag[1]
				)
			);
		}

		foreach ( $this->mLinktags as $tag ) {
			$tags[] = Html::element( 'link', $tag );
		}

		return $tags;
	}

	/**
	 * @return string HTML tag links to be put in the header.
	 */
	public function getHeadLinks() {
		return implode( "\n", $this->getHeadLinksArray() );
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
	 * Generate \<link\> tags for stylesheets
	 *
	 * @param string $style URL to the file
	 * @param array $options option, can contain 'condition', 'dir', 'media'
	 *                 keys
	 * @return String: HTML fragment
	 */
	protected function styleLink( $style, $options ) {
        global $hdStylePath;
        $media = 'all';
        $url = $hdStylePath . '/' . $style;

		$link = Html::linkedStyle( $url, $media );

		return $link;
	}

	/**
	 * Include jQuery core. Use this to avoid loading it multiple times
	 * before we get a usable script loader.
	 *
	 * @param array $modules list of jQuery modules which should be loaded
	 * @return Array: the list of modules which were not loaded.
	 * @since 1.16
	 * @deprecated since 1.17
	 */
	public function includeJQuery( $modules = array() ) {
		return array();
	}

}
