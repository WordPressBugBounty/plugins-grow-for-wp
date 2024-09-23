<?php

namespace Grow;

use WP_Post;
use WP_Term;

/**
 * The WordPress class serves as a facade for any WordPress function to be able to mock and alter core WordPress functions
 *
 * This should be the only place where we call core WordPress functions directly, everything else should pass through this class
 *
 * @since 0.0.1
 */
class WordPress implements OptionProviderInterface {
	/**
	 * Get the metadata for the plugin from the main plugin bootstrap file
	 *
	 * @return array<string, string>
	 * @since 0.0.1
	 */
	public static function get_plugin_data() : array {
		if ( ! function_exists('get_plugin_data') ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return array_map(function ( $val ) {
			return strval( $val );
		}, get_plugin_data(GROW_PLUGIN_DIR . '/grow-for-wp.php', false, true));
	}

	/**
	 * Register a script
	 *
	 * @param string           $handle Name used to identify this script
	 * @param string           $src URL to the asset
	 * @param string[]         $deps an array of string handles of scripts to be required by this script
	 * @param bool|string|null $ver A Version to include in the query string for the source, version number will be input if string, false will use the WordPress version, and null will  add no query string
	 * @param bool             $in_footer If the script tag should be added in the footer of the document ( as opposed to the head )
	 * @return bool
	 */
	public static function register_script( string $handle, string $src, array $deps = [], $ver = null, bool $in_footer = false ) : bool {
		return wp_register_script($handle, $src, $deps, $ver, $in_footer);
	}

	/**
	 * Register a script
	 *
	 * @param string           $handle Name used to identify this style
	 * @param string           $src URL to the asset
	 * @param string[]         $deps an array of string handles of scripts to be required by this style
	 * @param bool|string|null $ver A Version to include in the query string for the source, version number will be input if string, false will use the WordPress version, and null will  add no query string
	 * @param string           $media Media type that this style should apply for
	 * @return bool
	 */
	public static function register_style( string $handle, string $src, array $deps = [], $ver = null, string $media = 'all' ) : bool {
		return wp_register_style($handle, $src, $deps, $ver, $media);
	}

	/**
	 * @param string           $handle Name used to identify this script
	 * @param string           $src URL to the asset
	 * @param string[]         $deps an array of string handles of scripts to be required by this script
	 * @param bool|string|null $ver A Version to include in the query string for the source, version number will be input if string, false will use the WordPress version, and null will  add no query string
	 * @param bool             $in_footer If the script tag should be added in the footer of the document ( as opposed to the head )
	 * @return void
	 */
	public static function enqueue_script( string $handle, string $src = '', array $deps = [], $ver = null, bool $in_footer = false ) : void {
		wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
	}

	/**
	 * @param string           $handle Name used to identify this style
	 * @param string           $src URL to the asset
	 * @param string[]         $deps an array of string handles of scripts to be required by this style
	 * @param bool|string|null $ver A Version to include in the query string for the source, version number will be input if string, false will use the WordPress version, and null will  add no query string
	 * @param string           $media Media type that this style should apply for
	 * @return void
	 */
	public static function enqueue_style( string $handle, string $src = '', array $deps = [], $ver = null, string $media = 'all' ): void {
		wp_enqueue_style($handle, $src, $deps, $ver, $media);
	}

	/**
	 * @param string                                     $hook_name Name to identify hook
	 * @param callable|array<string>|(callable(): mixed) $callback Callback to fire when hook is run
	 * @param int                                        $priority The order the hook should be fired in
	 * @param int                                        $accepted_args How many arguments the callback takes
	 * @return bool|true|void
	 */
	public static function add_action( string $hook_name, $callback, int $priority = 10, int $accepted_args = 1 ) {
		return add_action($hook_name, $callback, $priority, $accepted_args); // @phpstan-ignore-line
	}

	/**
	 * @param string                                     $hook_name Name to identify hook
	 * @param callable|array<string>|(callable(): mixed) $callback Callback to fire when hook is run
	 * @param int                                        $priority The order the hook should be fired in
	 * @param int                                        $accepted_args How many arguments the callback takes
	 * @return bool
	 */
	public static function add_filter( string $hook_name, $callback, int $priority, int $accepted_args ) {
		add_filter($hook_name, $callback, $priority, $accepted_args); // @phpstan-ignore-line
		return true;
	}

	/**
	 * Apply Filters
	 *
	 * Note that this implementation requires all arguments in an array in a single parameter, unlike the WP Core
	 * This function iS NOT Variadic
	 *
	 * @param string $hook_name The name of the filter to apply
	 * @param mixed  $value The starting value to be filtered
	 * @param array  $args Other data to be passed to the callbacks registered with this filter
	 * @return mixed|void
	 */
	public static function apply_filters( string $hook_name, $value, array $args = [] ) {
		return apply_filters($hook_name, $value, ...$args); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}

	/**
	 * Execute a custom action
	 *
	 * Note that this implementation requires all arguments in an array in a single parameter, unlike the WP Core
	 * This function iS NOT Variadic
	 *
	 * @param string $hook_name The name of the action to run
	 * @param array  $args  data to be passed to the callbacks registered with this action
	 *
	 * @return void
	 */
	public static function do_action( string $hook_name, array $args = [] ) : void {
		do_action($hook_name, ...$args); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
	}


	/**
	 * @param string|string[] $post_types If present, this function will additionally check if the post is singular of that post type
	 * @return bool
	 */
	public static function is_singular( $post_types = '' ) : bool {
		return is_singular($post_types);
	}

	/**
	 * @param int $post_id Id of the post to get category for
	 * @return WP_Term[]
	 */
	public static function get_the_category( int $post_id ) {
		return get_the_category($post_id);
	}

	/**
	 * @param int|WP_Post|null $post The id of the post to get if something other than the current post is desired
	 *
	 * @return WP_Post|null
	 */
	public static function get_post( $post = null ) {
		return get_post($post);
	}

	/**
	 * Get an option from the database, will return default if the option doesn't exist or has a null value
	 *
	 * @param string $key Key of the option to get
	 * @param mixed  $default Value to be returned if no value present in the options table
	 *
	 * @return false|mixed|void
	 */
	public static function get_option( string $key, $default = null ) {
		return get_option($key, $default);
	}

	/**
	 * Updates an option with the given value, returns whether the update was a success
	 *
	 * @param string $key Key of the option to update
	 * @param mixed  $value Value to set
	 * @param bool   $autoload Loads the option into memory when WordPress loads, has performance implications and should usually be false
	 *
	 * @return bool If the option was updated, if the value here is the same as the current value in the db, this will return false
	 */
	public static function update_option( string $key, $value, bool $autoload = false ) : bool {
		return update_option($key, $value, $autoload);
	}

	/**
	 * Escape URLS for HTML attributes
	 *
	 * @param string $url URL to escape
	 *
	 * @return string
	 */
	public static function esc_url( string $url ) : string {
		return esc_url($url);
	}

	/**
	 * Will call the callback when the plugin at the passed file is activated
	 *
	 * @param string   $file Bootstrap file to watch activation for
	 * @param callable $callback Callback to fire if the plugin is activated
	 *
	 * @return void
	 */
	public static function register_activation_hook( string $file, callable $callback ) : void {
		register_activation_hook($file, $callback);
	}

	/**
	 * Will call the callback when the plugin at the passed file is deactivated
	 *
	 * @param string   $file Bootstrap file to watch deactivation for
	 * @param callable $callback Callback to fire if the plugin is deactivated
	 *
	 * @return void
	 */
	public static function register_deactivation_hook( string $file, callable $callback ) : void {
		register_deactivation_hook($file, $callback);
	}

	/**
	 * Add Inline Data for javascript, must be attached to an already registered script
	 *
	 * @param string $handle Handle of the script the inline data should be output with
	 * @param string $data The data to output
	 * @param string $position If the script should be output before or after the main script
	 *
	 * @return bool
	 */
	public static function add_inline_script( string $handle, string $data, string $position = 'after' ) {
		return wp_add_inline_script($handle, $data, $position);
	}

	/**
	 * Create a nonce for the given action
	 *
	 * @param string $action The action to create the nonce for, must match the name used when verifying
	 *
	 * @return string
	 */
	public static function create_nonce( string $action ) {
		return wp_create_nonce($action);
	}

	/**
	 * Get the Rest URL for the current Site
	 *
	 * @param string $path The additional path after the rest base to append
	 * @param string $scheme The sanitization scheme for the result
	 *
	 * @return string
	 */
	public static function get_rest_url( string $path = '/', string $scheme = 'rest' ) {
		return get_rest_url(null, $path, $scheme);
	}

	/**
	 * Determine if the current user has a given capability
	 *
	 * @param string $capability Capability to check
	 * @param array  $args Additional arguments to pass to check specific posts or category permissions
	 *
	 * @return bool
	 */
	public static function current_user_can( string $capability, array $args = [] ) {
		return current_user_can($capability, ...$args);
	}

	/**
	 * @param string   $page_title Title to appear on the page itself
	 * @param string   $menu_title Title to appear in the menu
	 * @param string   $capability Capabillity required to access page
	 * @param string   $menu_slug Slug used to identify this page amongst other menu items
	 * @param callable $callback Callback to render page
	 * @param string   $icon_url URL for icon to show next to menu item, can be base64 data URI, dashicons token, or none to leave empty for css
	 * @param int|null $position Where this menu item should appear
	 *
	 * @return string
	 */
	public static function add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback, string $icon_url = '', int $position = null ) : string {
		return add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position );
	}

	/**
	 * Get the sanitized domain for a site
	 *
	 * @return string
	 */
	public static function site_domain() : string {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST ) ?: '';
		return substr( $domain, 0, 4 ) === 'www.' ? substr_replace( $domain, '', 0, 4 ) : $domain;
	}

	/**
	 * Get the sanitized domain for a site
	 *
	 * @return string
	 */
	public static function site_path() : string {
		$path = wp_parse_url(  home_url(), PHP_URL_PATH ) ?: '';
		return $path;
	}

	/**
	 * Get the site title
	 *
	 * @return string
	 */
	public static function site_title() : string {
		return get_bloginfo( 'name' );
	}

	/**
	 * @param string $context Context to sanitize for
	 *
	 * @return array
	 */
	public static function wp_kses_allowed_html( string $context = 'post' ) : array {
		return wp_kses_allowed_html( $context );
	}

	/**
	 * @param string $path Path relative to base admin url
	 * @param string $scheme Scheme to use for url, admin, https, http
	 *
	 * @return string
	 */
	public static function admin_url( string $path = '', string $scheme = 'admin' ) : string {
		return admin_url( $path, $scheme );
	}

	/**
	 * Performs a safe (local) redirect, using wp_redirect().
	 *
	 * Checks whether the $location is using an allowed host, if it has an absolute
	 * path. A plugin can therefore set or remove allowed host(s) to or from the
	 * list.
	 *
	 * If the host is not allowed, then the redirect defaults to wp-admin on the siteurl
	 * instead. This prevents malicious redirects which redirect to another host,
	 * but only used in a few places.
	 *
	 * Note: wp_safe_redirect() does not exit automatically, and should almost always be
	 * followed by a call to `exit;`:
	 *
	 *     wp_safe_redirect( $url );
	 *     exit;
	 *
	 * Exiting can also be selectively manipulated by using wp_safe_redirect() as a conditional
	 * in conjunction with the {@see 'wp_redirect'} and {@see 'wp_redirect_status'} filters:
	 *
	 *     if ( wp_safe_redirect( $url ) ) {
	 *         exit;
	 *     }
	 *
	 * @since 2.3.0
	 * @since 5.1.0 The return value from wp_redirect() is now passed on, and the `$x_redirect_by` parameter was added.
	 *
	 * @param string $location      The path or URL to redirect to.
	 * @param int    $status        Optional. HTTP response status code to use. Default '302' (Moved Temporarily).
	 * @param string $x_redirect_by Optional. The application doing the redirect. Default 'WordPress'.
	 * @return bool False if the redirect was canceled, true otherwise.
	 */
	public static function wp_safe_redirect( $location, $status = 302, $x_redirect_by = 'WordPress' ) { //phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExitInConditional
		//phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
		return wp_safe_redirect( $location, $status, $x_redirect_by );
	}

	/**
	 * Determines whether the current request is for an administrative interface page.
	 *
	 * Does not check if the user is an administrator; use current_user_can()
	 * for checking roles and capabilities.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @since 1.5.1
	 *
	 * @global \WP_Screen $current_screen WordPress current screen object.
	 *
	 * @return bool True if inside WordPress administration interface, false otherwise.
	 */
	public static function is_admin() {
		return is_admin();
	}

	/**
	 * Determines whether the current request is for the network administrative interface.
	 *
	 * Does not check if the user is an administrator; use current_user_can()
	 * for checking roles and capabilities.
	 *
	 * Does not check if the site is a Multisite network; use is_multisite()
	 * for checking if Multisite is enabled.
	 *
	 * @since 3.1.0
	 *
	 * @global \WP_Screen $current_screen WordPress current screen object.
	 *
	 * @return bool True if inside WordPress network administration pages.
	 */
	public static function is_network_admin() {
		return is_network_admin();
	}

	/**
	 * Determines whether a script has been added to the queue.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $handle Name of the script.
	 * @param string $status Optional. Status of the script to check. Default 'enqueued'.
	 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
	 *
	 * @return bool Whether the script is queued.
	 * @since 3.5.0 'enqueued' added as an alias of the 'queue' list.
	 *
	 * @since 2.8.0
	 */
	public static function wp_script_is( $handle, $status = 'enqueued' ): bool {
		return wp_script_is( $handle, $status );
	}

	/**
	 * Retrieves the URL for the current site where the front end is accessible.
	 *
	 * Returns the 'home' option with the appropriate protocol. The protocol will be 'https'
	 * if is_ssl() evaluates to true; otherwise, it will be the same as the 'home' option.
	 * If `$scheme` is 'http' or 'https', is_ssl() is overridden.
	 *
	 * @since 3.0.0
	 *
	 * @param string      $path   Optional. Path relative to the home URL. Default empty.
	 * @param string|null $scheme Optional. Scheme to give the home URL context. Accepts
	 *                            'http', 'https', 'relative', 'rest', or null. Default null.
	 * @return string Home URL link with optional path appended.
	 */
	public static function home_url( $path = '', $scheme = null ) {
		return home_url( $path, $scheme );
	}

	/**
	 * Unschedules all events attached to the hook with the specified arguments.
	 *
	 * Warning: This function may return Boolean FALSE, but may also return a non-Boolean
	 * value which evaluates to FALSE. For information about casting to booleans see the
	 * {@link https://www.php.net/manual/en/language.types.boolean.php PHP documentation}. Use
	 * the `===` operator for testing the return value of this function.
	 *
	 * @since 2.1.0
	 * @since 5.1.0 Return value modified to indicate success or failure,
	 *              {@see 'pre_clear_scheduled_hook'} filter added to short-circuit the function.
	 * @since 5.7.0 The `$wp_error` parameter was added.
	 *
	 * @param string $hook     Action hook, the execution of which will be unscheduled.
	 * @param array  $args     Optional. Array containing each separate argument to pass to the hook's callback function.
	 *                         Although not passed to a callback, these arguments are used to uniquely identify the
	 *                         event, so they should be the same as those used when originally scheduling the event.
	 *                         Default empty array.
	 * @param bool   $wp_error Optional. Whether to return a WP_Error on failure. Default false.
	 * @return false|int|\WP_Error On success an integer indicating number of events unscheduled (0 indicates no
	 *                            events were registered with the hook and arguments combination), false or WP_Error
	 *                            if unscheduling one or more events fail.
	 */
	public static function wp_clear_scheduled_hook( $hook, $args = array(), $wp_error = false ) {
		return wp_clear_scheduled_hook( $hook, $args, $wp_error );
	}

	/**
	 * Kills WordPress execution and displays HTML page with an error message.
	 *
	 * This function complements the `die()` PHP function. The difference is that
	 * HTML will be displayed to the user. It is recommended to use this function
	 * only when the execution should not continue any further. It is not recommended
	 * to call this function very often, and try to handle as many errors as possible
	 * silently or more gracefully.
	 *
	 * As a shorthand, the desired HTTP response code may be passed as an integer to
	 * the `$title` parameter (the default title would apply) or the `$args` parameter.
	 *
	 * @since 2.0.4
	 * @since 4.1.0 The `$title` and `$args` parameters were changed to optionally accept
	 *              an integer to be used as the response code.
	 * @since 5.1.0 The `$link_url`, `$link_text`, and `$exit` arguments were added.
	 * @since 5.3.0 The `$charset` argument was added.
	 * @since 5.5.0 The `$text_direction` argument has a priority over get_language_attributes()
	 *              in the default handler.
	 *
	 * @global \WP_Query $wp_query WordPress Query object.
	 *
	 * @param string|\WP_Error $message Optional. Error message. If this is a WP_Error object,
	 *                                  and not an Ajax or XML-RPC request, the error's messages are used.
	 *                                  Default empty string.
	 * @param string|int       $title   Optional. Error title. If `$message` is a `WP_Error` object,
	 *                                  error data with the key 'title' may be used to specify the title.
	 *                                  If `$title` is an integer, then it is treated as the response code.
	 *                                  Default empty string.
	 * @param string|array|int $args {
	 *     Optional. Arguments to control behavior. If `$args` is an integer, then it is treated
	 *     as the response code. Default empty array.
	 *
	 *     @type int    $response       The HTTP response code. Default 200 for Ajax requests, 500 otherwise.
	 *     @type string $link_url       A URL to include a link to. Only works in combination with $link_text.
	 *                                  Default empty string.
	 *     @type string $link_text      A label for the link to include. Only works in combination with $link_url.
	 *                                  Default empty string.
	 *     @type bool   $back_link      Whether to include a link to go back. Default false.
	 *     @type string $text_direction The text direction. This is only useful internally, when WordPress is still
	 *                                  loading and the site's locale is not set up yet. Accepts 'rtl' and 'ltr'.
	 *                                  Default is the value of is_rtl().
	 *     @type string $charset        Character set of the HTML output. Default 'utf-8'.
	 *     @type string $code           Error code to use. Default is 'wp_die', or the main error code if $message
	 *                                  is a WP_Error.
	 *     @type bool   $exit           Whether to exit the process after completion. Default true.
	 * }
	 *
	 * @return void
	 *
	 * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	 */
	public static function wp_die( $message = '', $title = '', $args = array() ) {
		//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die( $message, $title, $args ); // @phpstan-ignore-line
	}

	/**
	 * Retrieves the URL for a given site where the front end is accessible.
	 *
	 * Returns the 'home' option with the appropriate protocol. The protocol will be 'https'
	 * if is_ssl() evaluates to true; otherwise, it will be the same as the 'home' option.
	 * If `$scheme` is 'http' or 'https', is_ssl() is overridden.
	 *
	 * @since 3.0.0
	 *
	 * @param int|null    $blog_id Optional. Site ID. Default null (current site).
	 * @param string      $path    Optional. Path relative to the home URL. Default empty.
	 * @param string|null $scheme  Optional. Scheme to give the home URL context. Accepts
	 *                             'http', 'https', 'relative', 'rest', or null. Default null.
	 * @return string Home URL link with optional path appended.
	 */
	public static function get_home_url( $blog_id = null, $path = '', $scheme = null ) {
		return get_home_url( $blog_id, $path, $scheme );
	}

	/**
	 * Checks whether the given variable is a WordPress Error.
	 *
	 * Returns whether `$thing` is an instance of the `WP_Error` class.
	 *
	 * @since 2.1.0
	 *
	 * @param mixed $thing The variable to check.
	 * @return bool Whether the variable is an instance of WP_Error.
	 */
	public static function is_wp_error( $thing ) {
		return is_wp_error($thing);
	}

	/**
	 * Retrieve the raw response from a safe HTTP request using the GET method.
	 *
	 * This function is ideal when the HTTP request is being made to an arbitrary
	 * URL. The URL is validated to avoid redirection and request forgery attacks.
	 *
	 * @since 3.6.0
	 *
	 * @see wp_remote_request() For more information on the response array format.
	 * @see WP_Http::request() For default arguments information.
	 *
	 * @param string $url  URL to retrieve.
	 * @param array  $args Optional. Request arguments. Default empty array.
	 * @return array|\WP_Error The response or WP_Error on failure.
	 */
	public static function wp_safe_remote_get( $url, $args = array() ) {
		return wp_safe_remote_get( $url, $args );
	}

	/**
	 * Retrieve only the response code from the raw response.
	 *
	 * Will return an empty string if incorrect parameter value is given.
	 *
	 * @since 2.7.0
	 *
	 * @param array|\WP_Error $response HTTP response.
	 * @return int|string The response code as an integer. Empty string if incorrect parameter given.
	 */
	public static function wp_remote_retrieve_response_code( $response ) {
		return wp_remote_retrieve_response_code( $response );
	}

	/**
	 * Deletes a file.
	 *
	 * @since 4.2.0
	 *
	 * @param string $file The path to the file to delete.
	 * @return void
	 */
	public static function wp_delete_file( $file ) {
		wp_delete_file( $file );
	}

	/**
	 * Retrieve only the body from the raw response.
	 *
	 * @since 2.7.0
	 *
	 * @param array|\WP_Error $response HTTP response.
	 * @return string The body of the response. Empty string if no body or incorrect parameter given.
	 */
	public static function wp_remote_retrieve_body( $response ) {
		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Removes a callback function from an action hook.
	 *
	 * This can be used to remove default functions attached to a specific action
	 * hook and possibly replace them with a substitute.
	 *
	 * To remove a hook, the `$callback` and `$priority` arguments must match
	 * when the hook was added. This goes for both filters and actions. No warning
	 * will be given on removal failure.
	 *
	 * @since 1.2.0
	 *
	 * @param string                $hook_name The action hook to which the function to be removed is hooked.
	 * @param callable|string|array $callback  The name of the function which should be removed.
	 *                                         This function can be called unconditionally to speculatively remove
	 *                                         a callback that may or may not exist.
	 * @param int                   $priority  Optional. The exact priority used when adding the original
	 *                                         action callback. Default 10.
	 * @return bool Whether the function is removed.
	 */
	public static function remove_action( $hook_name, $callback, $priority = 10 ) {
		return remove_action( $hook_name, $callback, $priority );
	}

	/**
	 * Retrieves the next timestamp for an event.
	 *
	 * @since 2.1.0
	 *
	 * @param string $hook Action hook of the event.
	 * @param array  $args Optional. Array containing each separate argument to pass to the hook's callback function.
	 *                     Although not passed to a callback, these arguments are used to uniquely identify the
	 *                     event, so they should be the same as those used when originally scheduling the event.
	 *                     Default empty array.
	 * @return int|false The Unix timestamp of the next time the event will occur. False if the event doesn't exist.
	 */
	public static function wp_next_scheduled( $hook, $args = array() ) {
		return wp_next_scheduled( $hook, $args );
	}

	/**
	 * Schedules a recurring event.
	 *
	 * Schedules a hook which will be triggered by WordPress at the specified interval.
	 * The action will trigger when someone visits your WordPress site if the scheduled
	 * time has passed.
	 *
	 * Valid values for the recurrence are 'hourly', 'daily', and 'twicedaily'. These can
	 * be extended using the {@see 'cron_schedules'} filter in wp_get_schedules().
	 *
	 * Use wp_next_scheduled() to prevent duplicate events.
	 *
	 * Use wp_schedule_single_event() to schedule a non-recurring event.
	 *
	 * @since 2.1.0
	 * @since 5.1.0 Return value modified to boolean indicating success or failure,
	 *              {@see 'pre_schedule_event'} filter added to short-circuit the function.
	 * @since 5.7.0 The `$wp_error` parameter was added.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_schedule_event/
	 *
	 * @param int    $timestamp  Unix timestamp (UTC) for when to next run the event.
	 * @param string $recurrence How often the event should subsequently recur.
	 *                           See wp_get_schedules() for accepted values.
	 * @param string $hook       Action hook to execute when the event is run.
	 * @param array  $args       Optional. Array containing arguments to pass to the
	 *                           hook's callback function. Each value in the array
	 *                           is passed to the callback as an individual parameter.
	 *                           The array keys are ignored. Default empty array.
	 * @param bool   $wp_error   Optional. Whether to return a WP_Error on failure. Default false.
	 * @return bool|\WP_Error True if event successfully scheduled. False or WP_Error on failure.
	 */
	public static function wp_schedule_event( $timestamp, $recurrence, $hook, $args = array(), $wp_error = false ) {
		return wp_schedule_event( $timestamp, $recurrence, $hook, $args, $wp_error );
	}

	/**
	 * Whether the site is being previewed in the Customizer.
	 *
	 * @since 4.0.0
	 *
	 * @global \WP_Customize_Manager $wp_customize Customizer instance.
	 *
	 * @return bool True if the site is being previewed in the Customizer, false otherwise.
	 */
	public static function is_customize_preview() {
		return is_customize_preview();
	}

	/**
	 * Adds a submenu page.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @since 1.5.0
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @global array $submenu
	 * @global array $menu
	 * @global array $_wp_real_parent_file
	 * @global bool $_wp_submenu_nopriv
	 * @global array $_registered_pages
	 * @global array $_parent_pages
	 *
	 * @param string          $parent_slug The slug name for the parent menu (or the file name of a standard
	 *                                        WordPress admin page).
	 * @param string          $page_title The text to be displayed in the title tags of the page when the menu
	 *                                        is selected.
	 * @param string          $menu_title The text to be used for the menu.
	 * @param string          $capability The capability required for this menu to be displayed to the user.
	 * @param string          $menu_slug The slug name to refer to this menu by. Should be unique for this menu
	 *                                        and only include lowercase alphanumeric, dashes, and underscores characters
	 *                                        to be compatible with sanitize_key().
	 * @param callable|string $callback Optional. The function to be called to output the content for this page.
	 * @param int|float       $position Optional. The position in the menu order this item should appear.
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
	 */
	public static function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {
		return add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback, $position ); // @phpstan-ignore-line
	}

	/**
	 * Removes rewrite rules and then recreate rewrite rules.
	 *
	 * @since 3.0.0
	 *
	 * @global \WP_Rewrite $wp_rewrite WordPress rewrite component.
	 *
	 * @param bool $hard Whether to update .htaccess (hard flush) or just update
	 *                   rewrite_rules option (soft flush). Default is true (hard).
	 *
	 * @return void
	 *
	 * phpcs:disable WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
	 */
	public static function flush_rewrite_rules( $hard = true ) {
		//phpcs:disable WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
		flush_rewrite_rules( $hard );
	}

	/**
	 * Appends a trailing slash.
	 *
	 * Will remove trailing forward and backslashes if it exists already before adding
	 * a trailing forward slash. This prevents double slashing a string or path.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @since 1.2.0
	 *
	 * @param string $value Value to which trailing slash will be added.
	 * @return string String with trailing slash added.
	 */
	public static function trailingslashit( $value ) {
		return trailingslashit( $value );
	}

	/**
	 * Filters text content and strips out disallowed HTML.
	 *
	 * This function makes sure that only the allowed HTML element names, attribute
	 * names, attribute values, and HTML entities will occur in the given text string.
	 *
	 * This function expects unslashed data.
	 *
	 * @see wp_kses_post() for specifically filtering post content and fields.
	 * @see wp_allowed_protocols() for the default allowed protocols in link URLs.
	 *
	 * @since 1.0.0
	 *
	 * @param string         $content           Text content to filter.
	 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
	 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
	 *                                          for the list of accepted context names.
	 * @param string[]       $allowed_protocols Optional. Array of allowed URL protocols.
	 *                                          Defaults to the result of wp_allowed_protocols().
	 * @return string Filtered content containing only the allowed HTML.
	 */
	public static function wp_kses( $content, $allowed_html, $allowed_protocols = array() ) {
		return wp_kses( $content, $allowed_html, $allowed_protocols );
	}

	/**
	 * Verifies the Ajax request to prevent processing requests external of the blog.
	 *
	 * @since 2.0.3
	 *
	 * @param int|string   $action    Action nonce.
	 * @param false|string $query_arg Optional. Key to check for the nonce in `$_REQUEST` (since 2.5). If false,
	 *                                `$_REQUEST` values will be evaluated for '_ajax_nonce', and '_wpnonce'
	 *                                (in that order). Default false.
	 * @param bool         $stop      Optional. Whether to stop early when the nonce cannot be verified.
	 *                                Default true.
	 * @return int|false 1 if the nonce is valid and generated between 0-12 hours ago,
	 *                   2 if the nonce is valid and generated between 12-24 hours ago.
	 *                   False if the nonce is invalid.
	 */
	public static function check_ajax_referer( $action = -1, $query_arg = false, $stop = true ) {
		return check_ajax_referer( $action, $query_arg, $stop );
	}

	/**
	 * Sends a JSON response back to an Ajax request, indicating success.
	 *
	 * @since 3.5.0
	 * @since 4.7.0 The `$status_code` parameter was added.
	 * @since 5.6.0 The `$options` parameter was added.
	 *
	 * @param mixed $data        Optional. Data to encode as JSON, then print and die. Default null.
	 * @param int   $status_code Optional. The HTTP status code to output. Default null.
	 * @param int   $options     Optional. Options to be passed to json_encode(). Default 0.
	 *
	 * @return void
	 */
	public static function wp_send_json_success( $data = null, $status_code = null, $options = 0 ) {
		wp_send_json_success( $data, $status_code, $options );
	}

	/**
	 * Sanitizes content for allowed HTML tags for post content.
	 *
	 * Post content refers to the page contents of the 'post' type and not `$_POST`
	 * data from forms.
	 *
	 * This function expects unslashed data.
	 *
	 * @since 2.9.0
	 *
	 * @param string $data Post content to filter.
	 * @return string Filtered post content with allowed HTML tags and attributes intact.
	 */
	public static function wp_kses_post( $data ) {
		return wp_kses_post( $data );
	}

	/**
	 * Checks if any action has been registered for a hook.
	 *
	 * When using the `$callback` argument, this function may return a non-boolean value
	 * that evaluates to false (e.g. 0), so use the `===` operator for testing the return value.
	 *
	 * @since 2.5.0
	 *
	 * @see has_filter() This function is an alias of has_filter().
	 *
	 * @param string                      $hook_name The name of the action hook.
	 * @param callable|string|array|false $callback  Optional. The callback to check for.
	 *                                               This function can be called unconditionally to speculatively check
	 *                                               a callback that may or may not exist. Default false.
	 * @return bool|int If `$callback` is omitted, returns boolean for whether the hook has
	 *                  anything registered. When checking a specific function, the priority
	 *                  of that hook is returned, or false if the function is not attached.
	 */
	public static function has_action( $hook_name, $callback = false ) {
		return has_action( $hook_name, $callback );
	}

}
