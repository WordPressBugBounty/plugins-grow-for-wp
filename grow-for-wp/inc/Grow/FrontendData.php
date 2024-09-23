<?php

namespace Grow;

/**
 * Handles outputting data to the front end
 *
 * @since 0.0.1
 */
class FrontendData implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var string Name for the Filter used to collect data for the public front end */
	public const GET_DATA_FILTER = 'grow_frontend_data';

	/** @var string Name for the filter used to collect data for the admin front end */
	public const GET_ADMIN_DATA_FILTER = 'grow_frontend_admin_data';

	/**
	 * Register the actions
	 */
	public function __construct() {
		$this->actions = [
			new HookArguments('wp_footer', 'output' ),
			new HookArguments('admin_enqueue_scripts', 'output_admin', 20 ),
			new HookArguments(self::GET_ADMIN_DATA_FILTER, 'with_wp_api_data' ),
		];
	}

	/**
	 * @return array<string, mixed>|mixed
	 */
	public function get_data() {
		return WordPress::apply_filters( self::GET_DATA_FILTER, [] );
	}

	/**
	 * @return mixed|array<string, mixed>
	 */
	public function get_admin_data() {
		return WordPress::apply_filters( self::GET_ADMIN_DATA_FILTER, [] );
	}

	/**
	 * Output data as data attribute on div.
	 *
	 * Here we are using a json encoded string in a data attribute on an empty div.
	 * This is done because many Themes or Plugins will cause the stripping of any script data for security or
	 * optimization purposes This method is one of the few that we are relatively sure won't get stripped out
	 *
	 * @return void
	 */
	public function output() : void {
		if ( ! WordPress::is_singular() ) {
			// If this is an archive or a non static front page, don't output data
			return;
		}

		$data = htmlspecialchars( json_encode( $this->get_data() ) ?: '{}', ENT_QUOTES ); // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
		echo wp_kses( '<div id="grow-wp-data" data-grow=\'' . $data . '\'></div>', 'post' );
	}

	/**
	 * Output the admin data
	 *
	 * On the admin side we are not as worried about third parties interfering with data, so we use a more standard
	 * method to insert the data
	 *
	 * @return void
	 */
	public function output_admin() {
		WordPress::add_inline_script( AssetLoader::ADMIN_SCRIPT_SLUG, 'const growWPAdminData = ' . json_encode( $this->get_admin_data() ), 'before' );
	}

	/**
	 * Add the wp_rest nonce to the admin data
	 * This nonce allows the REST callbacks access to the current user context
	 *
	 * @param mixed|array<string, string|mixed> $data Existing data to add to
	 *
	 * @return mixed
	 */
	public static function with_wp_api_data( $data ) {
		if ( is_array ($data) && ! isset( $data['api'] ) ) {
			$data['wpApi'] = [
				'nonce' => WordPress::create_nonce( 'wp_rest' ),
				'root'  => WordPress::get_rest_url(),
			];
		}
		return $data;
	}
}
