<?php

namespace Grow;

/**
 * Handles setting the content security header.
 *
 * @since 6.4.0
 */
class SecurityHeader implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var EnvironmentInterface Provides access to the environment-related information. */
	private EnvironmentInterface $environment;

	/**
	 * Set up the class
	 *
	 * @param Repository $repository Gives access to config and options
	 */
	public function __construct( Repository $repository ) {
		$this->environment = $repository->get_environment();

		$this->actions = [
			new HookArguments( 'send_headers', 'send_headers', 11 ),
		];
	}

	/**
	 * Adds a CSP header to pages.
	 *
	 * @return bool
	 */
	public function send_headers() {
		// Don't add if on Customizer.
		if ( function_exists( 'is_customize_preview' ) && WordPress::is_customize_preview() ) {
			return false;
		}

		// Don't add header again if it has already been added.
		$headers = $this->environment->get_headers_list();
		if ( in_array( 'Content-Security-Policy: block-all-mixed-content', $headers, true ) ) {
			return true;
		}

		// Don't attempt to send a header if it is too late.
		if ( $this->environment->headers_sent() ) {
			return false;
		}

		$this->environment->set_header( 'Content-Security-Policy: block-all-mixed-content' );

		return true;
	}
}
