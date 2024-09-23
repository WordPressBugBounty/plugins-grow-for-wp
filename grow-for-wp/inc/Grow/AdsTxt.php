<?php

namespace Grow;

/**
 * Handles management of ads.txt.
 *
 * @since 6.4.0
 */
class AdsTxt implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var string $grow_site_uuid Grow Site UUID */
	private string $grow_site_uuid;

	/** @var string $grow_journey_status Journey enabled/disabled status */
	private string $grow_journey_status;

	/** @var string $ads_txt_method Cached ads.txt retrieval method */
	private string $ads_txt_method = '';

	/** @var OptionsInterface Provides access to environment related information */
	private OptionsInterface $options;

	/** @var EnvironmentInterface Provides access to environment related information */
	private EnvironmentInterface $environment;

	/**
	 * Sets up the class.
	 *
	 * @param Repository $repository Gives access to config and options.
	 */
	public function __construct( Repository $repository ) {
		$this->options             = $repository->get_options();
		$this->environment         = $repository->get_environment();
		$this->grow_site_uuid      = $this->options->get_grow_site_uuid();
		$this->grow_journey_status = $this->options->get_grow_journey_status();

		// Ads.txt is only managed if we have all the required information and Journey is enabled.
		if ( empty( $this->grow_site_uuid ) ) {
			return;
		}

		if ( empty( $this->grow_journey_status ) ) {
			return;
		}

		// @todo: Check if MCP is enabled, and adjust functionality to defer to it when relevant.

		$this->actions = [
			new HookArguments( 'grow_get_ads_txt_cron_event', 'write_ads_txt_file', 11 ),
			new HookArguments( 'parse_request', 'handle_parse_ads_txt_request', 11 ),
			new HookArguments( 'grow_journey_enabled', 'handle_journey_enabled' ),
			new HookArguments( 'grow_journey_disabled', 'handle_journey_disabled' ),
			new HookArguments( 'grow_journey_troubleshoot', 'handle_journey_troubleshoot' ),
		];
		$this->filters = [
			new HookArguments( 'allowed_redirect_hosts', 'allowed_hosts', 11 ),
			// Prevents Redirection plugin from overriding /ads.txt redirects.
			new HookArguments( 'redirection_url_target', 'remove_redirection_ads_txt', 11, 2 ),
		];

		// Unhooks Ads.txt Manager plugin from affecting Ads.txt redirect.
		WordPress::remove_action( 'init', 'tenup_display_ads_txt' );

		// Respond to the redirect check if current site supports redirecting .txt files.
		if ( ! empty( $this->options->get_grow_ads_txt_redirect_check_in_progress() ) ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['grow'] ) && 'checking-redirection' === $_GET['grow'] ) {
				WordPress::wp_die( 'Ads.txt redirection check in progress.' );
			}
			// phpcs:enable
		}
	}

	/**
	 * Handles enabling Journey related functionality and values.
	 *
	 * @return void
	 */
	public function handle_journey_enabled() {
		// Update values from DB and bypass/clear any caches.
		$this->grow_site_uuid      = $this->options->get_grow_site_uuid();
		$this->grow_journey_status = $this->options->get_grow_journey_status();

		unset( $this->ads_txt_method );
		$this->options->set_grow_ads_txt_method('');
		$this->ads_txt_method = $this->get_ads_txt_method();

		if ( 'write' === $this->ads_txt_method ) {
			$this->write_ads_txt_file();
			$this->add_ads_txt_write_event();
		}

		if ( 'redirect' === $this->ads_txt_method ) {
			$this->remove_ads_txt();
			WordPress::wp_clear_scheduled_hook( 'grow_get_ads_txt_cron_event' );
		}

		WordPress::flush_rewrite_rules();
	}

	/**
	 * Handles disabling Journey related functionality and values.
	 *
	 * @return void
	 */
	public function handle_journey_disabled() {
		unset( $this->ads_txt_method );
		$this->options->set_grow_ads_txt_method('');
		$this->remove_ads_txt();
		WordPress::wp_clear_scheduled_hook( 'grow_get_ads_txt_cron_event' );
		WordPress::flush_rewrite_rules();
	}

	/**
	 * Handles troubleshooting Journey related functionality and values.
	 *
	 * @return void
	 */
	public function handle_journey_troubleshoot() {
		// Update values from DB and bypass/clear any caches.
		$this->grow_site_uuid      = $this->options->get_grow_site_uuid();
		$this->grow_journey_status = $this->options->get_grow_journey_status();

		unset( $this->ads_txt_method );
		$this->options->set_grow_ads_txt_method('');
		$this->ads_txt_method = $this->get_ads_txt_method();

		if ( 'write' === $this->ads_txt_method ) {
			$this->write_ads_txt_file();
			$this->add_ads_txt_write_event();
		}

		if ( 'redirect' === $this->ads_txt_method ) {
			$this->remove_ads_txt();
			WordPress::wp_clear_scheduled_hook( 'grow_get_ads_txt_cron_event' );
		}

		WordPress::flush_rewrite_rules();
	}

	/**
	 * Gets the ads.txt method of retrieval.
	 *
	 * 'none' means G4WP is not handling ads.txt.
	 * 'redirect' uses a 301 method.
	 * 'write' writes the ads.txt file to the domain root and schedules an event
	 *   to check source server and update ads.txt info accordingly.
	 *
	 * @return string The ads.txt retrieval method. Valid values are 'none', 'redirect', or 'write'.
	 */
	public function get_ads_txt_method() {
		// @todo: check if MCP enabled, and, if so, fallback to MCP's method check.
		// Limit the number of concurrent checks to 1 to avoid too many check requests at once.
		if ( ! empty( $this->options->get_grow_ads_txt_redirect_check_in_progress() ) ) {
			// Default to redirect while redirect check is in progress to avoid writing ads.txt to server.
			// This will return a 404 for ads.txt temporarily (at most 12 seconds).
			return 'redirect';
		}

		if ( empty( $this->grow_site_uuid ) || empty( $this->grow_journey_status ) ) {
			// Persist to cached option value if not already set.
			if ( 'none' !== $this->ads_txt_method ) {
				$this->cache_and_persist_method( 'none' );
			}

			return $this->ads_txt_method;
		}

		// Return cached method, if already set.
		if ( ! empty( $this->ads_txt_method ) ) {
			return $this->ads_txt_method;
		}

		// Retrieve database value, if available.
		$this->ads_txt_method = $this->options->get_grow_ads_txt_method();
		if ( ! empty( $this->ads_txt_method ) ) {
			return $this->ads_txt_method;
		}

		// Redirect does not work if the home URL is in a subdirectory.
		if ( ! empty( WordPress::get_home_url( null, '', 'relative' ) ) ) {
			$this->cache_and_persist_method( 'write' );
			return $this->ads_txt_method;
		}

		// Confirm redirect is a valid option.
		if ( ! $this->can_txt_files_be_redirected() ) {
			$this->cache_and_persist_method( 'write' );
			return $this->ads_txt_method;
		}

		$this->cache_and_persist_method( 'redirect' );

		// We are using redirect so delete any ads.txt file on the server.
		$this->remove_ads_txt();

		// Handle edge cases when going from write to redirect method.
		WordPress::wp_clear_scheduled_hook( 'grow_get_ads_txt_cron_event' );

		return $this->ads_txt_method;
	}

	/**
	 * Checks if the site has the ability to redirect txt files.
	 *
	 * Potentially expensive procedure, so we store the result as an option.
	 *
	 * @return boolean
	 */
	public function can_txt_files_be_redirected() {
		// Limit the number of concurrent checks to 1 to avoid too many check requests at once.
		if ( ! empty( $this->options->get_grow_ads_txt_redirect_check_in_progress() ) ) {
			// Default to true while check is in progress to avoid writing ads.txt to server.
			// This will return a 404 for ads.txt temporarily (at most 8 seconds).
			return true;
		}

		// Track that we currently have a check in progress and prevent other
		// concurrent requests from triggering the check again.
		$this->options->set_grow_ads_txt_redirect_check_in_progress('1');

		// Check that the server is not intercepting txt files before WordPress.
		$unique_nonexistent_file = $this->environment->get_home_url() . uniqid( '/this-will-404-' ) . '.txt?grow=checking-redirection';
		$response_code           = $this->get_response_code( $unique_nonexistent_file );

		/**
		 * Filters the ads.txt redirect check response code. Use non-500 to force write method.
		 *
		 * @param int|string $response_code
		 */
		$response_code = WordPress::apply_filters( 'grow_ads_txt_redirect_check_response_code', $response_code );

		$this->options->set_grow_ads_txt_redirect_check_in_progress('');

		// If the url doesn't return a 500, then WP redirects don't work with txt files.
		// We purposefully exit the page with `wp_die`, so we know it should be 500.
		if ( 500 !== $response_code ) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the response code of a URL.
	 *
	 * @param string $url The URL to retrieve.
	 *
	 * @return int|string
	 */
	public function get_response_code( $url ) {
		$response = WordPress::wp_safe_remote_get($url, [
			'timeout' => 2,
		]);
		if ( WordPress::is_wp_error($response) ) {
			return 0;
		}
		return WordPress::wp_remote_retrieve_response_code($response);
	}

	/**
	 * Removes the ads.txt file from the filesystem.
	 *
	 * @return bool
	 */
	public function remove_ads_txt() {
		$real_path = realpath( $this->environment->get_root_path() . 'ads.txt' );
		if ( empty( $real_path ) ) {
			return false;
		}
		if ( file_exists( $real_path ) ) {
			WordPress::wp_delete_file( $real_path );
			return file_exists( $real_path );
		}
		return false;
	}

	/**
	 * Defines a callback that redirects ads.txt when settings are valid.
	 *
	 * @param \WP $query Current WordPress environment instance (passed by reference).
	 *
	 * @return void
	 */
	public function handle_parse_ads_txt_request( $query ) {
		if ( empty( $this->grow_site_uuid ) ) {
			return;
		}

		if ( empty( $this->grow_journey_status ) ) {
			return;
		}

		if ( 'redirect' !== $this->get_ads_txt_method() ) {
			return;
		}

		if ( ! $this->check_parse_route( 'ads.txt', $query ) ) {
			return;
		}

		$url = 'https://adstxt.journeymv.com/sites/' . $this->grow_site_uuid . '/ads.txt';

		//phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
		WordPress::wp_safe_redirect($url, 301);
		if ( ! defined( 'GROW_TEST_MODE' ) ) {
			exit; // @codeCoverageIgnore
		}
	}

	/**
	 * Checks that the parsed route matches a string
	 *
	 * @param string $needle Value to search for.
	 * @param \WP    $query Current WordPress environment instance (passed by reference).
	 * @return bool Matching route
	 */
	public function check_parse_route( $needle, $query ) {
		if ( ! property_exists( $query, 'query_vars' ) || ! is_array( $query->query_vars ) ) {
			return false;
		}

		$query_vars_as_string = $this->multi_implode( '', $query->query_vars );
		$query_request        = ( ! empty( $query->request ) ) ? $query->request : '';

		if ( in_array( $needle, array( $query_vars_as_string, $query_request ), true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Safely implodes an array that may contain nested arrays
	 *
	 * @param string $glue what to place between imploded values.
	 * @param array  $orig_array the array to be imploded, may contain nested arrays.
	 * @return string   safely imploded array/multi-dimensional array
	 */
	public function multi_implode( $glue = '', $orig_array = array() ) {
		foreach ( $orig_array as $ind => $value ) {
			if ( is_array( $value ) ) {
				$orig_array[ $ind ] = $this->multi_implode( '', $value );
			}
		}

		return implode( $glue, $orig_array );
	}

	/**
	 * Adds scheduled event to write ads.txt file.
	 *
	 * @return void
	 */
	public function add_ads_txt_write_event() {
		// Only proceed if scheduled event doesn't already exist.
		if ( false !== WordPress::wp_next_scheduled( 'grow_get_ads_txt_cron_event' ) ) {
			return;
		}

		WordPress::wp_schedule_event( time(), 'twicedaily', 'grow_get_ads_txt_cron_event' );
	}

	/**
	 * Writes content to the ads.txt file on the filesystem.
	 *
	 * @return bool|string|void
	 */
	public function write_ads_txt_file() {
		// @todo: check if MCP enabled, and, if so, defer to MCP's method of ads.txt mgmt instead.

		$ads_txt_file_contents = $this->get_ads_txt();

		if ( false === $ads_txt_file_contents ) {
			return __( 'Cannot connect to Ads.txt file.', 'grow-for-wp' );
		}
		if ( empty( $ads_txt_file_contents ) ) {
			return __( 'Ads.txt file empty.', 'grow-for-wp' );
		}

		// phpcs:disable WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fwrite, WordPress.WP.AlternativeFunctions.file_system_read_fopen, WordPress.WP.AlternativeFunctions.file_system_read_fclose
		$fp = fopen( $this->environment->get_root_path() . 'ads.txt', 'w' );
		if ( false === $fp ) {
			return __( 'Unable to write Ads.txt.', 'grow-for-wp' );
		}
		fwrite( $fp, $ads_txt_file_contents );
		fclose( $fp );

		return true;
	}

	/**
	 * Retrieves ads.txt file contents from Mediavine Dashboard server.
	 *
	 * @return false|string
	 */
	public function get_ads_txt() {
		if ( empty( $this->grow_site_uuid ) ) {
			return false;
		}

		$url     = 'https://adstxt.journeymv.com/sites/' . $this->grow_site_uuid . '/ads.txt';
		$request = WordPress::wp_safe_remote_get( $url );

		// Try again with non-https if error (prevent cURL error 35: SSL connect error).
		if ( WordPress::is_wp_error( $request ) && ! empty( $request->errors['http_request_failed'] ) ) {
			$url     = 'http://adstxt.journeymv.com/sites/' . $this->grow_site_uuid . '/ads.txt';
			$request = WordPress::wp_safe_remote_get( $url );
		}

		$response_code = WordPress::wp_remote_retrieve_response_code( $request );
		if ( $response_code >= 200 && $response_code < 400 ) {
			return WordPress::wp_remote_retrieve_body( $request );
		}

		return false;
	}

	/**
	 * Adds ads.txt domain to allowed hosts for redirects.
	 *
	 * @param array $hosts Existing list of allowed hosts.
	 * @return array Hosts
	 */
	public function allowed_hosts( $hosts ) {
		$hosts[] = 'adstxt.journeymv.com';
		return $hosts;
	}

	/**
	 * Removes the /ads.txt redirect from the Redirection plugin if it exists.
	 *
	 * @param string $target_url Destination URL for a redirect.
	 * @param string $source_url Matched URL that triggers redirect.
	 * @return bool|string False if source is /ads.txt. Initial target if no match.
	 */
	public function remove_redirection_ads_txt( $target_url, $source_url ) {
		if ( '/ads.txt' === $source_url ) {
			$target_url = false;
		}

		return $target_url;
	}

	/**
	 * Defines a helper method to save cached ads.txt method to DB and property.
	 *
	 * @param string $method The ads.txt management method to save.
	 *
	 * @return void
	 */
	private function cache_and_persist_method( $method ) {
		$this->options->set_grow_ads_txt_method( $method );
		$this->ads_txt_method = $method;
	}


}
