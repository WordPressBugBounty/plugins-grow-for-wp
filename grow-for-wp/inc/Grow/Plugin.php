<?php

namespace Grow;

use Grow\API\V1\Settings;
use Grow\API\V1\Status;
use Grow\Pages\AdminPage;
use Grow\Pages\JourneyConfirm;
use Grow\Views\ViewLoader;
use Grow\Views\ViewLoaderInterface;

/**
 * Entry class for the plugin
 * Sets up everything to be run
 */
class Plugin {

	/** @var string|null VERSION */
	const VERSION = '1.5.0';

	/** @var Repository */
	public Repository $repository;

	/**
	 * @var WordPressLifecycleInterface
	 */
	public WordPressLifecycleInterface $wordpress_lifecycle;

	/**
	 * Fires the main method
	 */
	public function __construct() {
		WordPress::register_activation_hook( GROW_PLUGIN_FILE, [ $this, 'register_activation' ] );
		WordPress::register_deactivation_hook( GROW_PLUGIN_FILE, [ $this, 'register_deactivation' ] );
		// Set priority to 11 so that MCP is triggered first, if installed.
		WordPress::add_action('plugins_loaded', [ $this, 'main' ], 11);
	}

	/**
	 * Actually does the actions of the plugin
	 *
	 * @return void
	 */
	public function main() : void {
		$this->repository          = new Repository( self::setup_config(), self::setup_environment(), self::setup_options(), self::setup_grow_remote(), self::setup_view_loader() );
		$asset_loader              = new AssetLoader( $this->repository );
		$status_api_controller     = new StatusAPIController( $this->repository, Status::schema(), Status::args() );
		$settings_api_controller   = new SettingsAPIController( $this->repository, Settings::schema(), Settings::args() );
		$frontend_data             = new FrontendData();
		$content                   = new Content( $this->repository );
		$activation                = new Activation( $this->repository );
		$update                    = new Update( $this->repository );
		$admin_page                = new AdminPage( $this->repository );
		$ads_script                = new AdsScript( $this->repository );
		$journey_confirm           = new JourneyConfirm( $this->repository );
		$ads_txt                   = new AdsTxt( $this->repository );
		$security_header           = new SecurityHeader( $this->repository );
		$this->wordpress_lifecycle = new WordPressLifecycle( $this->repository );
		$this->wordpress_lifecycle->collect_many( [
			$activation,
			$update,
			$asset_loader,
			$status_api_controller,
			$settings_api_controller,
			$frontend_data,
			$content,
			$admin_page,
			$journey_confirm,
			$ads_script,
			$ads_txt,
			$security_header,
		] );
	}

	/**
	 * Gets the data from the bootstrap file and loads it in
	 *
	 * @return PluginConfigInterface
	 */
	private static function setup_config() : PluginConfigInterface {
		$plugin_data = WordPress::get_plugin_data();
		$version = is_null( self::VERSION ) ? $plugin_data[ 'Version' ] : self::VERSION; // @codingStandardsIgnoreLine @phpstan-ignore-line
		return new PluginConfig( 'grow/v1', $plugin_data['Name'] ?? '', $version, $plugin_data['RequiresWP'] ?? '', $plugin_data['RequiresPHP'] ?? '' );
	}

	/**
	 * Reads information about the environment and saves it
	 *
	 * @return EnvironmentInterface
	 */
	private static function setup_environment() : EnvironmentInterface {
		return new Environment();
	}

	/**
	 * Set up the access point for the options in the database
	 *
	 * @return OptionsInterface
	 */
	private static function setup_options() : OptionsInterface {
		return new Options( new WordPress() );
	}

	/**
	 * Set up the information about the Grow Remote Server
	 *
	 * @return GrowRemoteInterface
	 */
	private static function setup_grow_remote() : GrowRemoteInterface {
		return new GrowRemote();
	}

	/**
	 * Set up the information about the Grow Remote Server
	 *
	 * @return ViewLoaderInterface
	 */
	private static function setup_view_loader() : ViewLoaderInterface {
		return new ViewLoader();
	}

	/**
	 * Handles plugin activation.
	 *
	 * @return void
	 */
	public function register_activation() {
		if ( empty( $this->wordpress_lifecycle ) ) {
			$this->main();
		}
		$this->wordpress_lifecycle->register_activation();
	}

	/**
	 * Handles plugin deactivation.
	 *
	 * @return void
	 */
	public function register_deactivation() {
		if ( empty( $this->wordpress_lifecycle ) ) {
			$this->main();
		}
		$this->wordpress_lifecycle->register_deactivation();
	}


}
